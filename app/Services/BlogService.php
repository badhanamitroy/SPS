<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\HtmlSanitizer;

class BlogService
{
    private static ?array $blogs = null;

    public static function getStoragePath(): string
    {
        return dirname(__DIR__, 2) . '/storage/data/blogs.json';
    }

    /**
     * Clear in-memory cache (for testing and mutation sync)
     */
    public static function clearCache(): void
    {
        self::$blogs = null;
    }

    /**
     * Get raw blog entries from storage.
     */
    public static function getBlogs(bool $onlyPublished = false): array
    {
        if (self::$blogs === null) {
            $path = self::getStoragePath();
            if (file_exists($path)) {
                $raw = (string)file_get_contents($path);
                $decoded = json_decode($raw, true);
                if (is_array($decoded)) {
                    self::$blogs = $decoded;
                }
            }
            if (self::$blogs === null) {
                self::$blogs = [];
            }
        }

        if ($onlyPublished) {
            return array_values(array_filter(self::$blogs, fn($b) => ($b['status'] ?? '') === 'published'));
        }

        return self::$blogs;
    }

    /** @var resource|null */
    private static $lockFp = null;
    private static int $lockDepth = 0;

    /**
     * Perform an operation holding an exclusive advisory lock on the storage lock file.
     * Guarantees the entire read-modify-write sequence is mutually exclusive.
     */
    public static function withExclusiveLock(callable $operation): mixed
    {
        if (self::$lockDepth === 0) {
            $lockPath = dirname(self::getStoragePath()) . '/.blogs.lock';
            self::$lockFp = @fopen($lockPath, 'c+');
            if (self::$lockFp) {
                @flock(self::$lockFp, LOCK_EX);
            }
            self::$blogs = null; // Always fresh read from disk under lock
        }
        self::$lockDepth++;

        try {
            return $operation();
        } finally {
            self::$lockDepth--;
            if (self::$lockDepth === 0) {
                if (self::$lockFp) {
                    @flock(self::$lockFp, LOCK_UN);
                    @fclose(self::$lockFp);
                    self::$lockFp = null;
                }
            }
        }
    }

    /**
     * Atomically write file contents using temporary file and atomic rename.
     */
    public static function atomicWrite(string $path, string $content): bool
    {
        $dir = dirname($path);
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        $tmp = $path . '.tmp_' . bin2hex(random_bytes(6));
        if (file_put_contents($tmp, $content) === false) {
            return false;
        }

        if (@rename($tmp, $path)) {
            return true;
        }

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' && file_exists($path)) {
            @unlink($path);
            if (@rename($tmp, $path)) {
                return true;
            }
        }

        @unlink($tmp);
        return false;
    }

    /**
     * Save blogs list back to storage.
     */
    public static function saveBlogs(array $blogs): bool
    {
        self::$blogs = $blogs;
        $path = self::getStoragePath();
        $json = json_encode($blogs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return self::atomicWrite($path, $json);
    }

    /**
     * Query published blogs with filters for category, tag, archive (YYYY-MM), or search text.
     */
    public static function getPublishedBlogs(?string $category = null, ?string $tag = null, ?string $archive = null, ?string $search = null): array
    {
        $all = self::getBlogs(true);

        if ($category !== null && $category !== '' && $category !== 'all') {
            $all = array_filter($all, fn($b) => ($b['category'] ?? '') === $category);
        }

        if ($tag !== null && $tag !== '') {
            $all = array_filter($all, function($b) use ($tag) {
                $tags = $b['tags'] ?? [];
                foreach ($tags as $t) {
                    if (mb_strtolower((string)$t) === mb_strtolower($tag)) {
                        return true;
                    }
                }
                return false;
            });
        }

        if ($archive !== null && $archive !== '') {
            $all = array_filter($all, function($b) use ($archive) {
                $date = $b['published_at'] ?? $b['created_at'] ?? '';
                if (strlen($archive) === 4) {
                    // Filter by year e.g. "2026"
                    return str_starts_with($date, $archive);
                }
                // Filter by year-month e.g. "2026-09"
                return str_starts_with($date, $archive);
            });
        }

        if ($search !== null && $search !== '') {
            $q = mb_strtolower(trim($search));
            $all = array_filter($all, function($b) use ($q) {
                $haystack = mb_strtolower(
                    ($b['title_bn'] ?? '') . ' ' .
                    ($b['title_en'] ?? '') . ' ' .
                    ($b['excerpt_bn'] ?? '') . ' ' .
                    ($b['excerpt_en'] ?? '') . ' ' .
                    ($b['author']['name_bn'] ?? '') . ' ' .
                    ($b['author']['name_en'] ?? '') . ' ' .
                    implode(' ', $b['tags'] ?? [])
                );
                return str_contains($haystack, $q);
            });
        }

        // Sort by published_at or created_at descending
        usort($all, function($a, $b) {
            $da = $a['published_at'] ?? $a['created_at'] ?? '';
            $db = $b['published_at'] ?? $b['created_at'] ?? '';
            return strcmp($db, $da);
        });

        return array_values($all);
    }

    /**
     * Get pending blogs awaiting moderation.
     */
    public static function getPendingBlogs(): array
    {
        $all = self::getBlogs(false);
        $pending = array_values(array_filter($all, fn($b) => ($b['status'] ?? '') === 'pending'));
        usort($pending, fn($a, $b) => strcmp($b['created_at'] ?? '', $a['created_at'] ?? ''));
        return $pending;
    }

    /**
     * Get rejected blogs.
     */
    public static function getRejectedBlogs(): array
    {
        $all = self::getBlogs(false);
        $rejected = array_values(array_filter($all, fn($b) => ($b['status'] ?? '') === 'rejected'));
        usort($rejected, fn($a, $b) => strcmp($b['created_at'] ?? '', $a['created_at'] ?? ''));
        return $rejected;
    }

    /**
     * Find single blog by slug.
     */
    public static function findBySlug(string $slug, bool $onlyPublished = false): ?array
    {
        $blogs = self::getBlogs($onlyPublished);
        foreach ($blogs as $b) {
            if (($b['slug'] ?? '') === $slug) {
                if (isset($b['content_bn'])) {
                    $b['content_bn'] = HtmlSanitizer::clean($b['content_bn']);
                }
                if (isset($b['content_en'])) {
                    $b['content_en'] = HtmlSanitizer::clean($b['content_en']);
                }
                return $b;
            }
        }
        return null;
    }

    /**
     * Find single blog by unique ID.
     */
    public static function findById(string $id): ?array
    {
        $blogs = self::getBlogs(false);
        foreach ($blogs as $b) {
            if (($b['id'] ?? '') === $id) {
                if (isset($b['content_bn'])) {
                    $b['content_bn'] = HtmlSanitizer::clean($b['content_bn']);
                }
                if (isset($b['content_en'])) {
                    $b['content_en'] = HtmlSanitizer::clean($b['content_en']);
                }
                return $b;
            }
        }
        return null;
    }

    /**
     * Get Popular Posts sorted by readership and likes.
     */
    public static function getPopularPosts(int $limit = 4): array
    {
        $all = self::getBlogs(true);
        usort($all, function($a, $b) {
            $scoreA = ((int)($a['views_count'] ?? 0)) + (((int)($a['likes_count'] ?? 0)) * 2);
            $scoreB = ((int)($b['views_count'] ?? 0)) + (((int)($b['likes_count'] ?? 0)) * 2);
            return $scoreB <=> $scoreA;
        });
        return array_slice($all, 0, $limit);
    }

    /**
     * Hierarchical Blog Archive (Year -> Month) for Blogspot sidebar gadget.
     */
    public static function getArchiveTree(): array
    {
        $all = self::getBlogs(true);
        $tree = [];

        $bnMonths = [
            '01' => 'জানুয়ারি', '02' => 'ফেব্রুয়ারি', '03' => 'মার্চ',
            '04' => 'এপ্রিল', '05' => 'মে', '06' => 'জুন',
            '07' => 'জুলাই', '08' => 'আগস্ট', '09' => 'সেপ্টেম্বর',
            '10' => 'অক্টোবর', '11' => 'নভেম্বর', '12' => 'ডিসেম্বর'
        ];

        $enMonths = [
            '01' => 'January', '02' => 'February', '03' => 'March',
            '04' => 'April', '05' => 'May', '06' => 'June',
            '07' => 'July', '08' => 'August', '09' => 'September',
            '10' => 'October', '11' => 'November', '12' => 'December'
        ];

        foreach ($all as $b) {
            $date = $b['published_at'] ?? $b['created_at'] ?? '';
            if (empty($date)) continue;
            $year = substr($date, 0, 4);
            $month = substr($date, 5, 2);
            if (!$year || !$month) continue;

            if (!isset($tree[$year])) {
                $tree[$year] = [
                    'year' => $year,
                    'count' => 0,
                    'months' => [],
                ];
            }
            $tree[$year]['count']++;

            if (!isset($tree[$year]['months'][$month])) {
                $tree[$year]['months'][$month] = [
                    'month' => $month,
                    'year' => $year,
                    'archive_key' => $year . '-' . $month,
                    'name_bn' => ($bnMonths[$month] ?? $month) . ' ' . $year,
                    'name_en' => ($enMonths[$month] ?? $month) . ' ' . $year,
                    'count' => 0,
                ];
            }
            $tree[$year]['months'][$month]['count']++;
        }

        // Sort years descending
        krsort($tree);
        foreach ($tree as &$yData) {
            krsort($yData['months']);
            $yData['months'] = array_values($yData['months']);
        }

        return array_values($tree);
    }

    /**
     * Categories with published article counts.
     */
    public static function getCategories(): array
    {
        $all = self::getBlogs(true);
        $categories = [
            'vedanta' => ['id' => 'vedanta', 'name_bn' => 'বেদান্ত ও দর্শন', 'name_en' => 'Vedanta & Philosophy', 'count' => 0, 'icon' => '🕉️'],
            'gita' => ['id' => 'gita', 'name_bn' => 'ভগবদগীতা ও আত্মউন্নয়ন', 'name_en' => 'Bhagavad Gita & Growth', 'count' => 0, 'icon' => '📖'],
            'history' => ['id' => 'history', 'name_bn' => 'সনাতন ইতিহাস ও ঐতিহ্য', 'name_en' => 'Sanatan History & Heritage', 'count' => 0, 'icon' => '🏛️'],
            'seva' => ['id' => 'seva', 'name_bn' => 'সেবা ও মানবকল্যাণ', 'name_en' => 'Seva & Welfare', 'count' => 0, 'icon' => '🤝'],
            'scripture' => ['id' => 'scripture', 'name_bn' => 'শাস্ত্র ও আধ্যাত্মিকতা', 'name_en' => 'Scriptures & Spirituality', 'count' => 0, 'icon' => '🪔'],
        ];

        foreach ($all as $b) {
            $cat = $b['category'] ?? 'vedanta';
            if (isset($categories[$cat])) {
                $categories[$cat]['count']++;
            } else {
                $categories[$cat] = [
                    'id' => $cat,
                    'name_bn' => $b['category_bn'] ?? ucfirst($cat),
                    'name_en' => $b['category_en'] ?? ucfirst($cat),
                    'count' => 1,
                    'icon' => '📝'
                ];
            }
        }

        return $categories;
    }

    /**
     * Labels / Tags cloud with usage count.
     */
    public static function getTags(): array
    {
        $all = self::getBlogs(true);
        $tags = [];

        foreach ($all as $b) {
            $postTags = $b['tags'] ?? [];
            foreach ($postTags as $t) {
                $t = trim((string)$t);
                if ($t === '') continue;
                if (!isset($tags[$t])) {
                    $tags[$t] = ['name' => $t, 'count' => 0];
                }
                $tags[$t]['count']++;
            }
        }

        uasort($tags, fn($a, $b) => $b['count'] <=> $a['count']);
        return array_values($tags);
    }

    /**
     * Paid Member blog submission: sets status 'pending' awaiting moderation.
     */
    public static function createBlog(array $data, ?array $author = null): array
    {
        return self::withExclusiveLock(function () use ($data, $author) {
            $blogs = self::getBlogs(false);

            $titleBn = trim((string)($data['title_bn'] ?? ''));
            $titleEn = trim((string)($data['title_en'] ?? ''));

            // Generate URL slug
            $baseSlug = !empty($titleEn) 
                ? strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', trim($titleEn)))
                : 'blog-' . time();
            $baseSlug = trim($baseSlug, '-');
            if (empty($baseSlug)) {
                $baseSlug = 'blog-' . time();
            }

            // Ensure unique slug
            $slug = $baseSlug;
            $counter = 1;
            while (self::findBySlug($slug, false) !== null) {
                $slug = $baseSlug . '-' . (++$counter);
            }

            $now = date('Y-m-d H:i:s');
            $id = 'blog-' . (count($blogs) + 1) . '-' . substr(md5(uniqid()), 0, 5);

            // Tags parsing
            $tags = [];
            if (isset($data['tags'])) {
                if (is_array($data['tags'])) {
                    $tags = array_map('trim', $data['tags']);
                } elseif (is_string($data['tags'])) {
                    $parts = explode(',', $data['tags']);
                    foreach ($parts as $p) {
                        $p = trim($p);
                        if ($p !== '') $tags[] = $p;
                    }
                }
            }

            // Category mapping
            $cat = trim((string)($data['category'] ?? 'vedanta'));
            $catNames = [
                'vedanta' => ['bn' => 'বেদান্ত ও দর্শন', 'en' => 'Vedanta & Philosophy'],
                'gita' => ['bn' => 'ভগবদগীতা ও আত্মউন্নয়ন', 'en' => 'Bhagavad Gita & Growth'],
                'history' => ['bn' => 'সনাতন ইতিহাস ও ঐতিহ্য', 'en' => 'Sanatan History & Heritage'],
                'seva' => ['bn' => 'সেবা ও মানবকল্যাণ', 'en' => 'Seva & Welfare'],
                'scripture' => ['bn' => 'শাস্ত্র ও আধ্যাত্মিকতা', 'en' => 'Scriptures & Spirituality'],
            ];

            $authorInfo = [
                'name_bn' => $author['name_bn'] ?? ($data['author_name_bn'] ?? 'পেইড সদস্য'),
                'name_en' => $author['name_en'] ?? ($data['author_name_en'] ?? 'Paid Member'),
                'role' => $author['role'] ?? 'paid_member',
                'tier_bn' => $author['tier_bn'] ?? 'পেইড সদস্য',
                'tier_en' => $author['tier_en'] ?? 'Paid Member',
                'avatar' => $author['avatar'] ?? ('https://api.dicebear.com/7.x/bottts/svg?seed=' . urlencode($data['author_name_en'] ?? 'author')),
                'email' => $author['email'] ?? ($data['author_email'] ?? 'member@sps.org'),
            ];

            $featuredImage = trim((string)($data['featured_image'] ?? ''));
            if (empty($featuredImage)) {
                $featuredImage = 'assets/images/library/covers/sps-samachar-feb.jpg';
            }

            $newBlog = [
                'id' => $id,
                'slug' => $slug,
                'title_bn' => $titleBn ?: $titleEn,
                'title_en' => $titleEn ?: $titleBn,
                'category' => $cat,
                'category_bn' => $catNames[$cat]['bn'] ?? ucfirst($cat),
                'category_en' => $catNames[$cat]['en'] ?? ucfirst($cat),
                'featured_image' => $featuredImage,
                'excerpt_bn' => trim((string)($data['excerpt_bn'] ?? '')),
                'excerpt_en' => trim((string)($data['excerpt_en'] ?? '')),
                'content_bn' => HtmlSanitizer::clean(trim((string)($data['content_bn'] ?? ''))),
                'content_en' => HtmlSanitizer::clean(trim((string)($data['content_en'] ?? ''))),
                'tags' => array_values(array_unique($tags)),
                'author' => $authorInfo,
                'status' => 'pending', // Awaiting moderation
                'published_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
                'approved_by' => null,
                'approved_at' => null,
                'rejection_reason' => null,
                'likes_count' => 0,
                'views_count' => 1,
                'liked_ips' => [],
                'comments' => [],
            ];

            $blogs[] = $newBlog;
            self::saveBlogs($blogs);

            AuditService::log(
                'blog.submitted',
                'blog',
                $id,
                $newBlog['title_en'],
                [],
                ['author' => $authorInfo['name_en'], 'status' => 'pending'],
                "New blog post submitted by paid member {$authorInfo['name_en']} awaiting moderation",
                $author
            );

            return $newBlog;
        });
    }

    /**
     * Check if an admin user is allowed to moderate blogs:
     * Allowed: Super Admin, Admin, and Literature-Admin (content_editor / adminship Literature-Admin)
     */
    public static function canModerateBlogs(?array $user = null): bool
    {
        if ($user === null) {
            $user = AuthService::getCurrentUser();
        }
        if (!$user) return false;

        $role = $user['role'] ?? '';
        $adminship = $user['adminship'] ?? '';

        if (in_array($role, ['super_admin', 'admin', 'content_editor', 'blog_moderator'], true)) {
            return true;
        }

        if ($adminship === 'Super-Admin' || $adminship === 'Admin' || $adminship === 'Literature-Admin' || stripos($adminship, 'literature') !== false) {
            return true;
        }

        return false;
    }

    /**
     * Approve a blog post (Super Admin, Admin, Literature-Admin).
     */
    public static function approveBlog(string $id, array $adminUser): bool
    {
        if (!self::canModerateBlogs($adminUser)) {
            return false;
        }

        return self::withExclusiveLock(function () use ($id, $adminUser) {
            $blogs = self::getBlogs(false);
            $found = false;
            $now = date('Y-m-d H:i:s');
            $approverName = ($adminUser['name_bn'] ?? $adminUser['name_en'] ?? 'Admin') . 
                ' (' . ($adminUser['designation_bn'] ?? $adminUser['adminship'] ?? 'প্রশাসক') . ')';

            foreach ($blogs as &$b) {
                if (($b['id'] ?? '') === $id) {
                    $b['status'] = 'published';
                    $b['approved_by'] = $approverName;
                    $b['approved_at'] = $now;
                    $b['rejection_reason'] = null;
                    $b['updated_at'] = $now;
                    if (empty($b['published_at'])) {
                        $b['published_at'] = $now;
                    }
                    $found = true;
                    break;
                }
            }

            if ($found) {
                self::saveBlogs($blogs);
                AuditService::log(
                    'blog.approved',
                    'blog',
                    $id,
                    $id,
                    ['status' => 'pending'],
                    ['status' => 'published', 'approver' => $approverName],
                    "Blog {$id} approved and published by {$approverName}",
                    $adminUser
                );
                return true;
            }

            return false;
        });
    }

    /**
     * Reject a blog post (Super Admin, Admin, Literature-Admin).
     */
    public static function rejectBlog(string $id, array $adminUser, string $reason = ''): bool
    {
        if (!self::canModerateBlogs($adminUser)) {
            return false;
        }

        return self::withExclusiveLock(function () use ($id, $adminUser, $reason) {
            $blogs = self::getBlogs(false);
            $found = false;
            $now = date('Y-m-d H:i:s');
            $approverName = ($adminUser['name_bn'] ?? $adminUser['name_en'] ?? 'Admin') . 
                ' (' . ($adminUser['designation_bn'] ?? $adminUser['adminship'] ?? 'প্রশাসক') . ')';

            foreach ($blogs as &$b) {
                if (($b['id'] ?? '') === $id) {
                    $b['status'] = 'rejected';
                    $b['rejection_reason'] = trim($reason) ?: 'পাণ্ডুলিপিটি সম্পাদকীয় নীতিমালার সাথে সামঞ্জস্যপূর্ণ নয়।';
                    $b['approved_by'] = null;
                    $b['updated_at'] = $now;
                    $found = true;
                    break;
                }
            }

            if ($found) {
                self::saveBlogs($blogs);
                AuditService::log(
                    'blog.rejected',
                    'blog',
                    $id,
                    $id,
                    ['status' => 'pending'],
                    ['status' => 'rejected', 'reason' => $reason, 'reviewer' => $approverName],
                    "Blog {$id} rejected by {$approverName}: {$reason}",
                    $adminUser
                );
                return true;
            }

            return false;
        });
    }

    /**
     * Delete blog post.
     */
    public static function deleteBlog(string $id, array $adminUser): bool
    {
        if (!self::canModerateBlogs($adminUser)) {
            return false;
        }

        return self::withExclusiveLock(function () use ($id, $adminUser) {
            $blogs = self::getBlogs(false);
            $initialCount = count($blogs);
            $filtered = array_values(array_filter($blogs, fn($b) => ($b['id'] ?? '') !== $id));

            if (count($filtered) < $initialCount) {
                self::saveBlogs($filtered);
                AuditService::log(
                    'blog.deleted',
                    'blog',
                    $id,
                    $id,
                    [],
                    ['deleted_by' => $adminUser['name_en'] ?? 'Admin'],
                    "Blog post {$id} permanently removed",
                    $adminUser
                );
                return true;
            }

            return false;
        });
    }

    /**
     * Social interaction: Toggle like for a post by IP/user identifier.
     */
    public static function toggleLike(string $slug, string $identifier): array
    {
        return self::withExclusiveLock(function () use ($slug, $identifier) {
            $blogs = self::getBlogs(false);
            $found = false;
            $liked = false;
            $currentLikes = 0;

            foreach ($blogs as &$b) {
                if (($b['slug'] ?? '') === $slug) {
                    $likedIps = $b['liked_ips'] ?? [];
                    if (in_array($identifier, $likedIps, true)) {
                        // Unlike
                        $likedIps = array_values(array_diff($likedIps, [$identifier]));
                        $b['likes_count'] = max(0, ((int)($b['likes_count'] ?? 1)) - 1);
                        $liked = false;
                    } else {
                        // Like
                        $likedIps[] = $identifier;
                        $b['likes_count'] = ((int)($b['likes_count'] ?? 0)) + 1;
                        $liked = true;
                    }
                    $b['liked_ips'] = $likedIps;
                    $currentLikes = (int)$b['likes_count'];
                    $found = true;
                    break;
                }
            }

            if ($found) {
                self::saveBlogs($blogs);
            }

            return [
                'success' => $found,
                'liked' => $liked,
                'likes_count' => $currentLikes,
            ];
        });
    }

    /**
     * Check if a user/IP has liked a post.
     */
    public static function hasLiked(string $slug, string $identifier): bool
    {
        $blog = self::findBySlug($slug, false);
        if (!$blog) return false;
        $likedIps = $blog['liked_ips'] ?? [];
        return in_array($identifier, $likedIps, true);
    }

    /**
     * Social interaction: Add a Facebook-style comment to a blog post.
     */
    public static function addComment(string $slug, array $commentData): ?array
    {
        return self::withExclusiveLock(function () use ($slug, $commentData) {
            $blogs = self::getBlogs(false);
            $newComment = null;

            $name = trim((string)($commentData['author_name'] ?? ''));
            $content = trim((string)($commentData['content'] ?? ''));
            if (empty($name) || empty($content)) {
                return null;
            }

            $now = date('Y-m-d H:i:s');
            $commentId = 'cmt-' . time() . '-' . rand(100, 999);
            $role = trim((string)($commentData['author_role'] ?? 'visitor'));
            $email = trim((string)($commentData['author_email'] ?? ''));

            // Enforce length limits
            if (mb_strlen($name) > 100) {
                $name = mb_substr($name, 0, 100);
            }
            if (mb_strlen($email) > 120) {
                $email = mb_substr($email, 0, 120);
            }
            if (mb_strlen($content) > 2000) {
                $content = mb_substr($content, 0, 2000);
            }

            $newComment = [
                'id' => $commentId,
                'author_name' => $name,
                'author_email' => $email,
                'author_role' => $role,
                'author_avatar' => $commentData['author_avatar'] ?? ('https://api.dicebear.com/7.x/bottts/svg?seed=' . urlencode($name)),
                'content' => $content,
                'created_at' => $now,
                'likes' => 0,
                'raw' => true,
            ];

            foreach ($blogs as &$b) {
                if (($b['slug'] ?? '') === $slug) {
                    if (!isset($b['comments']) || !is_array($b['comments'])) {
                        $b['comments'] = [];
                    }
                    $b['comments'][] = $newComment;
                    self::saveBlogs($blogs);
                    return $newComment;
                }
            }

            return null;
        });
    }

    /**
     * Increment view counter.
     */
    public static function incrementViews(string $slug): void
    {
        self::withExclusiveLock(function () use ($slug) {
            $blogs = self::getBlogs(false);
            foreach ($blogs as &$b) {
                if (($b['slug'] ?? '') === $slug) {
                    $b['views_count'] = ((int)($b['views_count'] ?? 0)) + 1;
                    self::saveBlogs($blogs);
                    break;
                }
            }
        });
    }
}
