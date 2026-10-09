<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\App;
use App\Core\Session;

class GoogleAuthService
{
    /**
     * Retrieve the configured Google OAuth 2.0 Client ID.
     */
    public static function getClientId(): string
    {
        $clientId = (string)App::config('app.google.client_id', '');
        if ($clientId === '') {
            $clientId = (string)(getenv('GOOGLE_CLIENT_ID') ?: ($_ENV['GOOGLE_CLIENT_ID'] ?? ''));
        }
        return trim($clientId);
    }

    /**
     * Check if a Google Client ID is configured.
     */
    public static function isConfigured(): bool
    {
        return self::getClientId() !== '';
    }

    /**
     * Get list of explicit admin emails allowed to sign into the administrative console.
     */
    public static function getAllowedAdminEmails(): array
    {
        $list = App::config('app.google.allowed_admin_emails', []);
        if (!is_array($list)) {
            $list = [];
        }
        return array_map('strtolower', array_map('trim', $list));
    }

    /**
     * Cryptographically verify a Google ID Token (JWT) against Google's official tokeninfo endpoint.
     * Guarantees that the token was signed by Google, has not expired, and belongs to an authorized client.
     *
     * @param string $idToken The raw JWT credential received from Google Identity Services.
     * @return array|null Returns verified user profile array on success, or null on failure.
     */
    public static function verifyIdToken(string $idToken): ?array
    {
        $idToken = trim($idToken);
        if ($idToken === '') {
            return null;
        }

        $url = 'https://oauth2.googleapis.com/tokeninfo?id_token=' . urlencode($idToken);
        $json = null;

        // Preferred: cURL with SSL verification
        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 6);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_USERAGENT, 'SPS-Security-Verifier/2.0');
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200 && is_string($response)) {
                $json = $response;
            }
        }

        // Fallback: file_get_contents with stream context
        if ($json === null) {
            $ctx = stream_context_create([
                'http' => [
                    'method' => 'GET',
                    'timeout' => 6,
                    'ignore_errors' => true,
                    'header' => "User-Agent: SPS-Security-Verifier/2.0\r\n"
                ],
                'ssl' => [
                    'verify_peer' => true,
                    'verify_peer_name' => true,
                ]
            ]);
            $response = @file_get_contents($url, false, $ctx);
            if (is_string($response)) {
                $json = $response;
            }
        }

        if (empty($json)) {
            AuditService::log(
                'security.google_token_unreachable',
                'security',
                'system',
                'Google OAuth Gateway',
                [],
                ['reason' => 'Unable to connect to Google tokeninfo endpoint'],
                'Google token verification request could not be completed.'
            );
            return null;
        }

        $payload = json_decode($json, true);
        if (!is_array($payload) || !empty($payload['error']) || !empty($payload['error_description'])) {
            $err = $payload['error_description'] ?? ($payload['error'] ?? 'Token verification rejected by Google');
            AuditService::log(
                'security.google_token_invalid',
                'security',
                'system',
                'Google OAuth Gateway',
                [],
                ['error' => $err],
                "Google ID token rejected: {$err}"
            );
            return null;
        }

        // 1. Verify Issuer
        $iss = (string)($payload['iss'] ?? '');
        if (!in_array($iss, ['accounts.google.com', 'https://accounts.google.com'], true)) {
            AuditService::log(
                'security.google_token_iss_mismatch',
                'security',
                'system',
                'Google OAuth Gateway',
                [],
                ['iss' => $iss],
                "Google token rejected: invalid issuer '{$iss}'"
            );
            return null;
        }

        // 2. Verify Expiry
        $exp = isset($payload['exp']) ? (int)$payload['exp'] : 0;
        if ($exp <= time()) {
            AuditService::log(
                'security.google_token_expired',
                'security',
                'system',
                'Google OAuth Gateway',
                [],
                ['exp' => $exp, 'now' => time()],
                'Google token rejected: token has expired.'
            );
            return null;
        }

        // 3. Verify Audience (Client ID) if configured
        $configuredClientId = self::getClientId();
        if ($configuredClientId !== '') {
            $aud = (string)($payload['aud'] ?? '');
            if ($aud !== $configuredClientId) {
                AuditService::log(
                    'security.google_token_aud_mismatch',
                    'security',
                    'system',
                    'Google OAuth Gateway',
                    [],
                    ['expected' => $configuredClientId, 'received' => $aud],
                    'Google token rejected: audience client ID mismatch.'
                );
                return null;
            }
        }

        // 4. Verify Email & Email Verification Status
        $email = strtolower(trim((string)($payload['email'] ?? '')));
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return null;
        }

        $emailVerified = ($payload['email_verified'] ?? '') === 'true' || ($payload['email_verified'] ?? false) === true;
        if (!$emailVerified) {
            AuditService::log(
                'security.google_email_unverified',
                'security',
                'system',
                'Google OAuth Gateway',
                [],
                ['email' => $email],
                "Google token rejected: email '{$email}' is not verified by Google."
            );
            return null;
        }

        return [
            'google_id' => (string)($payload['sub'] ?? ''),
            'email' => $email,
            'email_verified' => true,
            'name' => trim((string)($payload['name'] ?? '')),
            'avatar' => trim((string)($payload['picture'] ?? '')),
            'locale' => (string)($payload['locale'] ?? 'en'),
            'raw' => $payload,
        ];
    }

    /**
     * Authenticate or register a Member via verified Google Profile.
     */
    public static function authenticateMember(array $profile): array
    {
        $res = MembershipService::findOrCreateMemberByGoogle([
            'email' => $profile['email'],
            'name' => $profile['name'] ?: 'Sanatan Member',
            'google_id' => $profile['google_id'],
            'avatar' => $profile['avatar'] ?? '',
        ]);

        $member = $res['member'];

        // Establish member session
        Session::start();
        Session::set('current_member_code', $member['member_code']);
        Session::forget('member_logged_out');

        AuditService::log(
            'membership.google_login',
            'membership',
            $member['id'],
            $member['name_en'] ?? $member['name_bn'],
            [],
            [
                'email' => $profile['email'],
                'google_id' => $profile['google_id'],
                'member_code' => $member['member_code'],
                'is_new' => $res['is_new']
            ],
            "Member '{$member['member_code']}' authenticated via secure Google Identity Services"
        );

        return [
            'success' => true,
            'member' => $member,
            'is_new' => $res['is_new']
        ];
    }

    /**
     * Authenticate an Administrator via verified Google Profile.
     * Matches against registered administrative accounts in RBAC.
     */
    public static function authenticateAdmin(array $profile): array
    {
        $email = strtolower(trim($profile['email']));
        $googleId = trim($profile['google_id']);

        $users = RbacService::getUsers();
        $matchedUser = null;

        foreach ($users as $user) {
            $userEmail = strtolower(trim((string)($user['email'] ?? '')));
            $userGoogleEmail = strtolower(trim((string)($user['google_email'] ?? '')));
            $userGoogleId = trim((string)($user['google_id'] ?? ''));


            if ($userEmail === $email || ($userGoogleEmail !== '' && $userGoogleEmail === $email)) {
                $matchedUser = $user;
                break;
            }

            if ($userGoogleId !== '' && $userGoogleId === $googleId) {
                $matchedUser = $user;
                break;
            }
        }

        // Also check explicit admin allowlist from configuration
        if (!$matchedUser) {
            $allowedEmails = self::getAllowedAdminEmails();
            if (in_array($email, $allowedEmails, true)) {
                // Find super_admin or first active administrator
                foreach ($users as $u) {
                    if (($u['role'] ?? '') === 'super_admin' && ($u['status'] ?? '') === 'active') {
                        $matchedUser = $u;
                        break;
                    }
                }
            }
        }

        if (!$matchedUser) {
            AuditService::log(
                'auth.admin_google_unauthorized',
                'security',
                'unauthorized',
                $email,
                [],
                ['email' => $email, 'google_id' => $googleId],
                "Unauthorized Google login attempt for admin portal with email '{$email}'"
            );

            return [
                'success' => false,
                'error' => "আপনার জিমেইল অ্যাকাউন্টটি ({$email}) কোনো অনুমোদিত এসপিএস প্রশাসনিক পদের সাথে সংযুক্ত নয়। দয়া করে প্রাতিষ্ঠানিক অ্যাডমিন অ্যাকাউন্ট ব্যবহার করুন।"
            ];
        }

        if (($matchedUser['status'] ?? 'active') !== 'active') {
            return [
                'success' => false,
                'error' => 'আপনার প্রশাসনিক অ্যাকাউন্টটি বর্তমানে নিষ্ক্রিয় বা স্থগিত রয়েছে।'
            ];
        }

        // Establish admin session
        AuthService::loginAs($matchedUser['id']);

        AuditService::log(
            'auth.admin_google_login_success',
            'security',
            $matchedUser['id'],
            $matchedUser['name_en'] ?? $matchedUser['name_bn'],
            [],
            [
                'email' => $email,
                'role' => $matchedUser['role'] ?? 'admin',
                'google_id' => $googleId
            ],
            "Administrator '{$matchedUser['name_en']}' successfully logged in via Google Identity Services",
            $matchedUser
        );

        return [
            'success' => true,
            'user' => $matchedUser
        ];
    }
}
