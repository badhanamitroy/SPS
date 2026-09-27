<?php

declare(strict_types=1);

namespace App\Services;

class MembershipService
{
    private static string $storagePath = '';
    private static ?array $cachedData = null;

    private static function getStoragePath(): string
    {
        if (empty(self::$storagePath)) {
            self::$storagePath = dirname(__DIR__, 2) . '/storage/data/membership.json';
        }
        return self::$storagePath;
    }

    private static function loadData(): array
    {
        if (self::$cachedData !== null) {
            return self::$cachedData;
        }

        $path = self::getStoragePath();
        if (!file_exists($path)) {
            return [
                'categories' => [],
                'plans' => [],
                'members' => [],
                'payments' => [],
                'history' => [],
                'volunteers' => [],
            ];
        }

        $json = file_get_contents($path);
        $data = json_decode((string)$json, true);
        self::$cachedData = is_array($data) ? $data : [];
        return self::$cachedData;
    }

    private static function saveData(array $data): void
    {
        self::$cachedData = $data;
        $path = self::getStoragePath();
        file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    public static function clearCache(): void
    {
        self::$cachedData = null;
    }

    /**
     * Get all membership categories (Student, Earning).
     */
    public static function getCategories(): array
    {
        $data = self::loadData();
        return $data['categories'] ?? [];
    }

    /**
     * Get single category by ID.
     */
    public static function getCategory(string $id): ?array
    {
        $categories = self::getCategories();
        foreach ($categories as $cat) {
            if ($cat['id'] === $id) {
                return $cat;
            }
        }
        return null;
    }

    /**
     * Get all defined membership plans.
     */
    public static function getPlans(): array
    {
        $data = self::loadData();
        return $data['plans'] ?? [];
    }

    /**
     * Get single plan by ID.
     */
    public static function getPlan(string $id): ?array
    {
        $plans = self::getPlans();
        foreach ($plans as $plan) {
            if ($plan['id'] === $id) {
                return $plan;
            }
        }
        return null;
    }

    /**
     * Get plans applicable to a specific category.
     */
    public static function getPlansForCategory(string $categoryId): array
    {
        $plans = self::getPlans();
        return array_values(array_filter($plans, function ($plan) use ($categoryId) {
            return ($plan['category_id'] === $categoryId || $plan['category_id'] === 'ALL') && !empty($plan['active']);
        }));
    }

    /**
     * Query all members with flexible filtering.
     */
    public static function getAllMembers(
        ?string $status = null,
        ?string $categoryId = null,
        ?string $planId = null,
        ?string $search = null
    ): array {
        $data = self::loadData();
        $members = $data['members'] ?? [];

        if ($status !== null && $status !== '' && $status !== 'all') {
            $members = array_filter($members, function ($m) use ($status) {
                return strcasecmp($m['status'] ?? '', $status) === 0;
            });
        }

        if ($categoryId !== null && $categoryId !== '' && $categoryId !== 'all') {
            $members = array_filter($members, function ($m) use ($categoryId) {
                return ($m['category_id'] ?? '') === $categoryId;
            });
        }

        if ($planId !== null && $planId !== '' && $planId !== 'all') {
            $members = array_filter($members, function ($m) use ($planId) {
                return ($m['plan_id'] ?? '') === $planId;
            });
        }

        if ($search !== null && trim($search) !== '') {
            $q = mb_strtolower(trim($search));
            $members = array_filter($members, function ($m) use ($q) {
                return str_contains(mb_strtolower($m['name_bn'] ?? ''), $q) ||
                       str_contains(mb_strtolower($m['name_en'] ?? ''), $q) ||
                       str_contains(mb_strtolower($m['member_code'] ?? ''), $q) ||
                       str_contains(mb_strtolower($m['email'] ?? ''), $q) ||
                       str_contains(mb_strtolower($m['phone'] ?? ''), $q);
            });
        }

        return array_values($members);
    }

    /**
     * Find member by internal ID or Member Code (e.g. SPS-000872).
     */
    public static function getMemberById(string $idOrCode): ?array
    {
        $data = self::loadData();
        foreach ($data['members'] ?? [] as $m) {
            if ($m['id'] === $idOrCode || ($m['member_code'] ?? '') === $idOrCode) {
                return $m;
            }
        }
        return null;
    }

    /**
     * Find member by Email address.
     */
    public static function getMemberByEmail(string $email): ?array
    {
        $data = self::loadData();
        $email = mb_strtolower(trim($email));
        foreach ($data['members'] ?? [] as $m) {
            if (mb_strtolower(trim($m['email'] ?? '')) === $email) {
                return $m;
            }
        }
        return null;
    }

    /**
     * Generate unique lifetime Member ID (e.g. SPS-000873).
     * Retains numerical consistency even if category transitions later.
     */
    public static function generateNextMemberCode(): string
    {
        $data = self::loadData();
        $maxNum = 872;
        foreach ($data['members'] ?? [] as $m) {
            if (preg_match('/SPS-(\d+)/', $m['member_code'] ?? '', $matches)) {
                $num = (int)$matches[1];
                if ($num > $maxNum) {
                    $maxNum = $num;
                }
            }
        }
        $next = $maxNum + 1;
        return sprintf('SPS-%06d', $next);
    }

    /**
     * Generate unique Transaction ID (e.g. SPS-MEM-2026-000193).
     */
    public static function generateTransactionId(): string
    {
        $year = date('Y');
        $random = mt_rand(100000, 999999);
        return "SPS-MEM-{$year}-{$random}";
    }

    /**
     * Register a new membership application.
     * Enters as 'Pending' until verified and approved.
     */
    public static function createApplication(array $input): array
    {
        $data = self::loadData();
        $memberId = 'mem_' . uniqid();
        $memberCode = self::generateNextMemberCode();

        $category = self::getCategory($input['category_id'] ?? 'STUDENT');
        $plan = self::getPlan($input['plan_id'] ?? 'STUDENT_MONTHLY');

        $isLifetime = ($plan['id'] ?? '') === 'LIFETIME';
        $entryFee = (float)($plan['entry_fee'] ?? 0);
        $planFee = (float)($plan['fee'] ?? 0);
        $totalInitialFee = (float)($plan['total_first_payment'] ?? ($entryFee + $planFee));

        $qrToken = "SPS-VERIFY-" . substr($memberCode, 4) . "-" . strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $input['name_en'] ?? 'MEMBER'), 0, 8)) . "-" . date('Y');

        $newMember = [
            'id' => $memberId,
            'member_code' => $memberCode,
            'name_bn' => trim($input['name_bn'] ?? ''),
            'name_en' => trim($input['name_en'] ?? ''),
            'email' => trim($input['email'] ?? ''),
            'phone' => trim($input['phone'] ?? ''),
            'avatar' => $input['avatar'] ?? ('https://api.dicebear.com/7.x/bottts/svg?seed=' . urlencode($input['name_en'] ?? 'Member')),
            'category_id' => $category['id'] ?? 'STUDENT',
            'plan_id' => $plan['id'] ?? 'STUDENT_MONTHLY',
            'status' => 'Pending',
            'join_date' => date('Y-m-d'),
            'start_date' => null,
            'end_date' => null,
            'is_lifetime' => $isLifetime,
            'next_payment_date' => null,
            'entry_fee_paid' => 0,
            'total_paid' => 0,
            'district' => trim($input['district'] ?? ''),
            'upazila' => trim($input['upazila'] ?? ''),
            'address' => trim($input['address'] ?? ''),
            'education' => !empty($input['education']) ? $input['education'] : null,
            'profession' => !empty($input['profession']) ? $input['profession'] : null,
            'recognitions' => ['Regular Member'],
            'volunteer_profile' => [
                'is_volunteer' => !empty($input['apply_volunteer']),
                'status' => !empty($input['apply_volunteer']) ? 'Applied' : 'Inactive',
                'interests' => $input['volunteer_interests'] ?? [],
                'experience' => trim($input['volunteer_experience'] ?? ''),
            ],
            'card_qr_token' => $qrToken,
            'notes' => 'অনলাইন পোর্টাল থেকে জমাকৃত নতুন সদস্যপদের আবেদন।',
        ];

        $data['members'][] = $newMember;

        // Create Initial Pending Payment Record with TrxID and Screenshot
        $txId = self::generateTransactionId();
        $paymentType = $isLifetime ? 'lifetime' : (($plan['id'] === 'YEARLY') ? 'yearly' : 'entry');
        $trxId = trim((string)($input['trx_id'] ?? ''));
        if (empty($trxId)) {
            $trxId = 'BKA' . strtoupper(substr(md5(uniqid()), 0, 8));
        }
        $paymentScreenshot = trim((string)($input['payment_screenshot'] ?? ''));
        if (empty($paymentScreenshot)) {
            $paymentScreenshot = 'assets/images/payments/bkash-success-sample.svg';
        }

        $newPayment = [
            'id' => 'pay_' . uniqid(),
            'member_id' => $memberId,
            'member_code' => $memberCode,
            'transaction_id' => $txId,
            'trx_id' => $trxId,
            'payment_screenshot' => $paymentScreenshot,
            'payment_type' => $paymentType,
            'amount' => $totalInitialFee,
            'currency' => 'BDT',
            'payment_method' => $input['payment_method'] ?? 'bKash',
            'sender_number' => $input['sender_number'] ?? $input['phone'] ?? '',
            'sender_name' => trim((string)($input['sender_name'] ?? '')),
            'payment_time' => !empty($input['payment_time']) ? trim((string)$input['payment_time']) : date('Y-m-d H:i:s'),
            'payment_reference' => trim((string)($input['payment_reference'] ?? '')),
            'payment_date' => date('Y-m-d H:i:s'),
            'period_start' => null,
            'period_end' => null,
            'status' => 'Pending',
            'verified_by' => null,
            'verified_at' => null,
            'notes' => 'আবেদনকালীন প্রাথমিক ফি প্রদান (ফাইন্যান্স অফিসার কর্তৃক ভেরিফিকেশন অপেক্ষমাণ)।',
        ];

        $data['payments'][] = $newPayment;

        self::saveData($data);

        return [
            'member' => $newMember,
            'payment' => $newPayment,
        ];
    }

    /**
     * Approve a pending member application & activate membership.
     */
    public static function approveMember(string $memberId, ?array $admin = null): bool
    {
        $data = self::loadData();
        $found = false;

        $adminName = $admin['name_bn'] ?? $admin['name_en'] ?? 'Administrator';

        foreach ($data['members'] as &$m) {
            if ($m['id'] === $memberId || $m['member_code'] === $memberId) {
                $plan = self::getPlan($m['plan_id']);
                $isLifetime = ($plan['id'] ?? '') === 'LIFETIME';

                $now = date('Y-m-d');
                $m['status'] = $isLifetime ? 'Lifetime Active' : 'Active';
                $m['start_date'] = $now;

                if ($isLifetime) {
                    $m['end_date'] = null;
                    $m['expiry_date'] = null;
                    $m['next_payment_date'] = null;
                    if (!in_array('Lifetime Member', $m['recognitions'] ?? [])) {
                        $m['recognitions'][] = 'Lifetime Member';
                    }
                } elseif ($plan['duration_type'] === 'yearly') {
                    $m['end_date'] = date('Y-m-d', strtotime('+12 months -1 day'));
                    $m['expiry_date'] = $m['end_date'];
                    $m['next_payment_date'] = $m['end_date'];
                } else {
                    $m['end_date'] = date('Y-m-d', strtotime('+1 month'));
                    $m['expiry_date'] = $m['end_date'];
                    $m['next_payment_date'] = $m['end_date'];
                }

                $m['entry_fee_paid'] = $plan['entry_fee'] ?? 0;
                $m['total_paid'] = $plan['total_first_payment'] ?? $plan['fee'] ?? 0;
                $m['notes'] = "আবেদনটি {$adminName} কর্তৃক পর্যালোচিত ও অনুমোদিত হয়েছে।";
                $found = true;
                break;
            }
        }
        unset($m);

        if (!$found) {
            return false;
        }

        // Also mark any pending initial payment as Verified
        foreach ($data['payments'] as &$p) {
            if ($p['member_id'] === $memberId && $p['status'] === 'Pending') {
                $p['status'] = 'Verified';
                $p['verified_by'] = $adminName;
                $p['verified_at'] = date('Y-m-d H:i:s');
                $p['period_start'] = date('Y-m-d');
                $p['period_end'] = $isLifetime ? null : ($plan['duration_type'] === 'yearly' ? date('Y-m-d', strtotime('+12 months -1 day')) : date('Y-m-d', strtotime('+1 month')));
            }
        }
        unset($p);

        self::saveData($data);
        return true;
    }

    /**
     * Reject a membership application with formal reasoning.
     */
    public static function rejectMember(string $memberId, string $reason, ?array $admin = null): bool
    {
        $data = self::loadData();
        $adminName = $admin['name_bn'] ?? $admin['name_en'] ?? 'Administrator';

        foreach ($data['members'] as &$m) {
            if ($m['id'] === $memberId || $m['member_code'] === $memberId) {
                $m['status'] = 'Rejected';
                $m['notes'] = "বাতিলকারী: {$adminName}। কারণ: {$reason}";
                self::saveData($data);
                return true;
            }
        }
        return false;
    }

    /**
     * Suspend an active member for misconduct or administrative policy breach.
     */
    public static function suspendMember(string $memberId, string $reason, ?array $admin = null): bool
    {
        $data = self::loadData();
        $adminName = $admin['name_bn'] ?? $admin['name_en'] ?? 'Administrator';

        foreach ($data['members'] as &$m) {
            if ($m['id'] === $memberId || $m['member_code'] === $memberId) {
                $prevStatus = $m['status'];
                $m['status'] = 'Suspended';
                $m['notes'] = "স্থগিতকারী: {$adminName} (পূর্ববর্তী স্ট্যাটাস: {$prevStatus})। কারণ: {$reason}";

                // Log into history
                $data['history'][] = [
                    'id' => 'hist_' . uniqid(),
                    'member_id' => $m['id'],
                    'member_code' => $m['member_code'],
                    'old_category' => $m['category_id'],
                    'new_category' => $m['category_id'],
                    'old_plan' => $m['plan_id'],
                    'new_plan' => $m['plan_id'],
                    'reason' => "সদস্যপদ সাময়িক স্থগিতকরণ: {$reason}",
                    'changed_by' => $adminName,
                    'changed_at' => date('Y-m-d H:i:s'),
                ];

                self::saveData($data);
                return true;
            }
        }
        return false;
    }

    /**
     * Reactivate a suspended or expired member.
     */
    public static function activateMember(string $memberId, ?array $admin = null): bool
    {
        $data = self::loadData();
        $adminName = $admin['name_bn'] ?? $admin['name_en'] ?? 'Administrator';

        foreach ($data['members'] as &$m) {
            if ($m['id'] === $memberId || $m['member_code'] === $memberId) {
                $m['status'] = !empty($m['is_lifetime']) ? 'Lifetime Active' : 'Active';
                $m['notes'] = "পুনরায় সক্রিয়কারী: {$adminName}।";
                self::saveData($data);
                return true;
            }
        }
        return false;
    }

    /**
     * Transition a Student Member to Earning Member upon graduation/employment.
     * Retains the permanent unique Member ID (e.g. SPS-000872), while recording
     * immutable historical transition records.
     */
    public static function transitionCategory(
        string $memberId,
        string $newCategoryId,
        string $newPlanId,
        string $reason,
        ?array $admin = null
    ): bool {
        $data = self::loadData();
        $adminName = $admin['name_bn'] ?? $admin['name_en'] ?? 'Administrator';
        $found = false;

        foreach ($data['members'] as &$m) {
            if ($m['id'] === $memberId || $m['member_code'] === $memberId) {
                $oldCategory = $m['category_id'];
                $oldPlan = $m['plan_id'];

                $m['category_id'] = $newCategoryId;
                $m['plan_id'] = $newPlanId;
                if ($newPlanId === 'LIFETIME') {
                    $m['is_lifetime'] = true;
                    $m['status'] = 'Lifetime Active';
                    $m['end_date'] = null;
                    if (!in_array('Lifetime Member', $m['recognitions'] ?? [])) {
                        $m['recognitions'][] = 'Lifetime Member';
                    }
                }

                // Append to immutable history log
                $data['history'][] = [
                    'id' => 'hist_' . uniqid(),
                    'member_id' => $m['id'],
                    'member_code' => $m['member_code'],
                    'old_category' => $oldCategory,
                    'new_category' => $newCategoryId,
                    'old_plan' => $oldPlan,
                    'new_plan' => $newPlanId,
                    'reason' => $reason,
                    'changed_by' => $adminName,
                    'changed_at' => date('Y-m-d H:i:s'),
                ];

                $found = true;
                break;
            }
        }
        unset($m);

        if ($found) {
            self::saveData($data);
            return true;
        }
        return false;
    }

    /**
     * Record a new membership payment (renewal, monthly, yearly, lifetime).
     */
    public static function recordPayment(array $paymentInput, ?array $admin = null): array
    {
        $data = self::loadData();
        $member = self::getMemberById($paymentInput['member_id']);
        if (!$member) {
            throw new \InvalidArgumentException('Member not found');
        }

        $adminName = $admin ? ($admin['name_bn'] ?? $admin['name_en'] ?? 'Admin') : null;
        $isAutoVerified = !empty($paymentInput['auto_verify']) || $admin !== null;

        $txId = self::generateTransactionId();
        $paymentType = $paymentInput['payment_type'] ?? 'monthly';
        $amount = (float)($paymentInput['amount'] ?? 0);

        $startDate = $paymentInput['period_start'] ?? date('Y-m-d');
        $endDate = null;

        if ($paymentType === 'yearly') {
            $endDate = date('Y-m-d', strtotime($startDate . ' +12 months -1 day'));
        } elseif ($paymentType === 'monthly') {
            $endDate = date('Y-m-d', strtotime($startDate . ' +1 month'));
        } elseif ($paymentType === 'lifetime') {
            $endDate = null;
        }

        $trxId = trim((string)($paymentInput['trx_id'] ?? ''));
        if (empty($trxId)) {
            $trxId = 'BKA' . strtoupper(substr(md5(uniqid()), 0, 8));
        }
        $paymentScreenshot = trim((string)($paymentInput['payment_screenshot'] ?? ''));
        if (empty($paymentScreenshot)) {
            $paymentScreenshot = 'assets/images/payments/bkash-success-sample.svg';
        }

        $payment = [
            'id' => 'pay_' . uniqid(),
            'member_id' => $member['id'],
            'member_code' => $member['member_code'],
            'transaction_id' => $txId,
            'trx_id' => $trxId,
            'payment_screenshot' => $paymentScreenshot,
            'payment_type' => $paymentType,
            'amount' => $amount,
            'currency' => 'BDT',
            'payment_method' => $paymentInput['payment_method'] ?? 'bKash',
            'sender_number' => $paymentInput['sender_number'] ?? '',
            'sender_name' => trim((string)($paymentInput['sender_name'] ?? '')),
            'payment_time' => !empty($paymentInput['payment_time']) ? trim((string)$paymentInput['payment_time']) : date('Y-m-d H:i:s'),
            'payment_reference' => trim((string)($paymentInput['payment_reference'] ?? '')),
            'payment_date' => date('Y-m-d H:i:s'),
            'period_start' => $startDate,
            'period_end' => $endDate,
            'status' => $isAutoVerified ? 'Verified' : 'Pending',
            'verified_by' => $isAutoVerified ? $adminName : null,
            'verified_at' => $isAutoVerified ? date('Y-m-d H:i:s') : null,
            'notes' => $paymentInput['notes'] ?? 'সদস্যপদ ফি লেনদেন।',
        ];

        $data['payments'][] = $payment;

        // If verified, update member validity dates
        if ($isAutoVerified) {
            foreach ($data['members'] as &$m) {
                if ($m['id'] === $member['id']) {
                    $m['total_paid'] = (float)($m['total_paid'] ?? 0) + $amount;
                    if ($paymentType === 'lifetime') {
                        $m['status'] = 'Lifetime Active';
                        $m['is_lifetime'] = true;
                        $m['plan_id'] = 'LIFETIME';
                        $m['end_date'] = null;
                        $m['expiry_date'] = null;
                        $m['next_payment_date'] = null;
                    } elseif ($paymentType === 'yearly') {
                        $m['status'] = 'Active';
                        $m['plan_id'] = 'YEARLY';
                        $m['end_date'] = $endDate;
                        $m['expiry_date'] = $endDate;
                        $m['next_payment_date'] = $endDate;
                    } else {
                        $m['status'] = 'Active';
                        $m['end_date'] = $endDate;
                        $m['expiry_date'] = $endDate;
                        $m['next_payment_date'] = $endDate;
                    }
                    break;
                }
            }
            unset($m);
        }

        self::saveData($data);
        return $payment;
    }

    /**
     * Get all payments recorded in the system, optionally filtered by status.
     */
    public static function getAllPayments(?string $status = null): array
    {
        $data = self::loadData();
        $payments = $data['payments'] ?? [];
        if ($status && $status !== 'all') {
            $payments = array_filter($payments, fn($p) => ($p['status'] ?? '') === $status);
        }
        usort($payments, fn($a, $b) => strcmp($b['payment_date'] ?? $b['created_at'] ?? '', $a['payment_date'] ?? $a['created_at'] ?? ''));
        return array_values($payments);
    }

    /**
     * Get pending payments awaiting Finance Officer verification.
     */
    public static function getPendingPayments(): array
    {
        return self::getAllPayments('Pending');
    }

    /**
     * Verify an existing pending payment.
     * Accessible by Finance Officer (primary operator), Super Admin, and Admin (supervisory monitoring).
     */
    public static function verifyPayment(string $paymentId, ?array $admin = null): bool
    {
        $data = self::loadData();
        $adminName = $admin ? ($admin['name_bn'] ?? $admin['name_en'] ?? 'Finance Officer') : 'Joy Chakraborty (Treasurer / Finance Officer)';
        $foundPayment = null;

        foreach ($data['payments'] as &$p) {
            if ($p['id'] === $paymentId || $p['transaction_id'] === $paymentId) {
                $p['status'] = 'Verified';
                $p['verified_by'] = $adminName;
                $p['verified_at'] = date('Y-m-d H:i:s');
                $foundPayment = $p;
                break;
            }
        }
        unset($p);

        if (!$foundPayment) {
            return false;
        }

        // Apply validity extension & member activation
        foreach ($data['members'] as &$m) {
            if ($m['id'] === $foundPayment['member_id']) {
                $m['total_paid'] = (float)($m['total_paid'] ?? 0) + (float)$foundPayment['amount'];
                if (empty($m['start_date'])) {
                    $m['start_date'] = date('Y-m-d');
                }

                if ($foundPayment['payment_type'] === 'lifetime') {
                    $m['status'] = 'Lifetime Active';
                    $m['is_lifetime'] = true;
                    $m['plan_id'] = 'LIFETIME';
                    $m['end_date'] = null;
                    $m['expiry_date'] = null;
                    $m['next_payment_date'] = null;
                    if (!in_array('Lifetime Member', $m['recognitions'] ?? [])) {
                        $m['recognitions'][] = 'Lifetime Member';
                    }
                } elseif ($foundPayment['payment_type'] === 'yearly') {
                    $m['status'] = 'Active';
                    $m['plan_id'] = 'YEARLY';
                    $m['end_date'] = date('Y-m-d', strtotime('+12 months -1 day'));
                    $m['expiry_date'] = $m['end_date'];
                    $m['next_payment_date'] = $m['end_date'];
                } else {
                    $m['status'] = 'Active';
                    $m['end_date'] = date('Y-m-d', strtotime('+1 month'));
                    $m['expiry_date'] = $m['end_date'];
                    $m['next_payment_date'] = $m['end_date'];
                }

                $m['notes'] = "পেমেন্ট {$adminName} কর্তৃক যাচাইকৃত ও সদস্যপদ হালনাগাদকৃত।";
                break;
            }
        }
        unset($m);

        self::saveData($data);
        return true;
    }

    /**
     * Reject a fraudulent or unmatched payment.
     */
    public static function rejectPayment(string $paymentId, string $reason, ?array $admin = null): bool
    {
        $data = self::loadData();
        $adminName = $admin ? ($admin['name_bn'] ?? $admin['name_en'] ?? 'Finance Officer') : 'Joy Chakraborty (Treasurer / Finance Officer)';
        $found = false;

        foreach ($data['payments'] as &$p) {
            if ($p['id'] === $paymentId || $p['transaction_id'] === $paymentId) {
                $p['status'] = 'Rejected';
                $p['verified_by'] = $adminName;
                $p['verified_at'] = date('Y-m-d H:i:s');
                $p['notes'] = "পেমেন্ট বাতিলকারী: {$adminName}। কারণ: {$reason}";
                $found = true;
                break;
            }
        }
        unset($p);

        if ($found) {
            self::saveData($data);
            return true;
        }
        return false;
    }

    /**
     * Submit volunteer interest.
     */
    public static function applyForVolunteer(string $memberId, array $interests, string $experience): bool
    {
        $data = self::loadData();
        foreach ($data['members'] as &$m) {
            if ($m['id'] === $memberId || $m['member_code'] === $memberId) {
                $m['volunteer_profile'] = [
                    'is_volunteer' => true,
                    'status' => 'Applied',
                    'interests' => $interests,
                    'experience' => $experience,
                ];

                $data['volunteers'][] = [
                    'id' => 'vol_' . uniqid(),
                    'member_id' => $m['id'],
                    'member_code' => $m['member_code'],
                    'name' => $m['name_en'] ?? $m['name_bn'],
                    'status' => 'Applied',
                    'interests' => $interests,
                    'applied_at' => date('Y-m-d'),
                    'approved_at' => null,
                ];

                self::saveData($data);
                return true;
            }
        }
        return false;
    }

    /**
     * Retrieve all payment records for a member.
     */
    public static function getMemberPayments(string $memberId): array
    {
        $data = self::loadData();
        $payments = [];
        foreach ($data['payments'] ?? [] as $p) {
            if ($p['member_id'] === $memberId || $p['member_code'] === $memberId) {
                $payments[] = $p;
            }
        }
        // sort latest first
        usort($payments, fn($a, $b) => strcmp($b['payment_date'], $a['payment_date']));
        return $payments;
    }

    /**
     * Retrieve category / plan transition history for a member.
     */
    public static function getMemberHistory(string $memberId): array
    {
        $data = self::loadData();
        $history = [];
        foreach ($data['history'] ?? [] as $h) {
            if ($h['member_id'] === $memberId || $h['member_code'] === $memberId) {
                $history[] = $h;
            }
        }
        usort($history, fn($a, $b) => strcmp($b['changed_at'], $a['changed_at']));
        return $history;
    }

    /**
     * Get aggregate statistics for admin dashboard.
     */
    public static function getMemberStats(): array
    {
        $data = self::loadData();
        $members = $data['members'] ?? [];

        $stats = [
            'total' => count($members),
            'active' => 0,
            'student' => 0,
            'earning' => 0,
            'monthly' => 0,
            'yearly' => 0,
            'lifetime' => 0,
            'payment_due' => 0,
            'expiring_soon' => 0,
            'pending' => 0,
            'suspended' => 0,
            'volunteers' => 0,
            'total_collected' => 0,
        ];

        $now = time();
        $thirtyDays = $now + (30 * 86400);

        foreach ($members as $m) {
            $status = $m['status'] ?? '';
            $cat = $m['category_id'] ?? '';
            $plan = $m['plan_id'] ?? '';

            if ($status === 'Active' || $status === 'Lifetime Active') {
                $stats['active']++;
            }
            if ($cat === 'STUDENT') {
                $stats['student']++;
            }
            if ($cat === 'EARNING') {
                $stats['earning']++;
            }
            if ($plan === 'STUDENT_MONTHLY' || $plan === 'EARNING_MONTHLY') {
                $stats['monthly']++;
            }
            if ($plan === 'YEARLY') {
                $stats['yearly']++;
            }
            if ($plan === 'LIFETIME' || !empty($m['is_lifetime'])) {
                $stats['lifetime']++;
            }
            if ($status === 'Payment Due') {
                $stats['payment_due']++;
            }
            if ($status === 'Pending') {
                $stats['pending']++;
            }
            if ($status === 'Suspended') {
                $stats['suspended']++;
            }
            if (!empty($m['volunteer_profile']['is_volunteer']) && ($m['volunteer_profile']['status'] ?? '') === 'Active') {
                $stats['volunteers']++;
            }

            // check expiry within 30 days
            if (!empty($m['end_date']) && $status === 'Active') {
                $endTs = strtotime($m['end_date']);
                if ($endTs >= $now && $endTs <= $thirtyDays) {
                    $stats['expiring_soon']++;
                }
            }
        }

        foreach ($data['payments'] ?? [] as $p) {
            if (($p['status'] ?? '') === 'Verified') {
                $stats['total_collected'] += (float)($p['amount'] ?? 0);
            }
        }

        return $stats;
    }

    /**
     * Check if a member is eligible to author blogs (must be Active or Lifetime Active).
     */
    public static function isEligibleAuthor(string $emailOrCode): bool
    {
        $member = self::getMemberByEmail($emailOrCode) ?? self::getMemberById($emailOrCode);
        if (!$member) {
            return false;
        }
        return in_array($member['status'], ['Active', 'Lifetime Active'], true);
    }
}
