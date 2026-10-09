<?php

declare(strict_types=1);

$file = dirname(__DIR__) . '/app/Controllers/AdminController.php';
$content = file_get_contents($file);

// 1. Update requireAuth to intercept users who must change initial OTP
$targetRequireAuth = <<<'CODE'
    private function requireAuth(Request $request): ?Response
    {
        if (!AuthService::check()) {
            $locale = I18n::getLocale();
            $isBn = $locale === 'bn';
            Session::setFlash('error', $isBn 
                ? 'অ্যাডমিন প্যানেলে প্রবেশ করতে অনুগ্রহ করে লগইন করুন।' 
                : 'Please log in with admin credentials to access the admin portal.');
            Session::set('auth_return_url', $request->getPath());
            return $this->redirect(url('/admin/login', $locale));
        }
        return null;
    }
CODE;

$newRequireAuth = <<<'CODE'
    private function requireAuth(Request $request): ?Response
    {
        if (!AuthService::check()) {
            $locale = I18n::getLocale();
            $isBn = $locale === 'bn';
            Session::setFlash('error', $isBn 
                ? 'অ্যাডমিন প্যানেলে প্রবেশ করতে অনুগ্রহ করে লগইন করুন।' 
                : 'Please log in with admin credentials to access the admin portal.');
            Session::set('auth_return_url', $request->getPath());
            return $this->redirect(url('/admin/login', $locale));
        }

        // Force password change on initial login with One-Time Password (OTP)
        if (AuthService::mustChangePassword()) {
            $locale = I18n::getLocale();
            $path = $request->getPath();
            $isForceChangeRoute = str_ends_with($path, '/admin/force-password-change') || str_contains($path, 'force-password-change');
            $isLogoutRoute = str_ends_with($path, '/admin/logout') || str_contains($path, '/logout');

            if (!$isForceChangeRoute && !$isLogoutRoute) {
                Session::setFlash('warning', $locale === 'bn' 
                    ? 'নিরাপত্তার স্বার্থে প্রশাসন কর্তৃক প্রদত্ত ওয়ান-টাইম পাসওয়ার্ড (OTP) পরিবর্তন করে আপনার নিজস্ব অনন্য পাসওয়ার্ড সেট করা আবশ্যক।' 
                    : 'Security Notice: Please replace your one-time password (OTP) with your secret unique password to access the admin panel.');
                return $this->redirect(url('/admin/force-password-change', $locale));
            }
        }

        return null;
    }
CODE;

if (strpos($content, $targetRequireAuth) !== false) {
    $content = str_replace($targetRequireAuth, $newRequireAuth, $content);
    echo "Updated requireAuth in AdminController\n";
} else {
    $targetRequireAuthCRLF = str_replace("\n", "\r\n", $targetRequireAuth);
    $newRequireAuthCRLF = str_replace("\n", "\r\n", $newRequireAuth);
    if (strpos($content, $targetRequireAuthCRLF) !== false) {
        $content = str_replace($targetRequireAuthCRLF, $newRequireAuthCRLF, $content);
        echo "Updated requireAuth in AdminController (CRLF)\n";
    } else {
        echo "Could not match requireAuth\n";
    }
}

// 2. Add OTP methods to AdminController before closing brace
$methods = <<<'CODE'

    /**
     * Admin First-Login Force Password Change Screen (GET)
     */
    public function forcePasswordChangePage(Request $request, string $lang = ''): Response
    {
        if (!AuthService::check()) {
            return $this->redirect(url('/admin/login', I18n::getLocale()));
        }

        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';
        $title = $isBn ? 'অনন্য পাসওয়ার্ড নির্ধারণ | এসপিএস অ্যাডমিন' : 'Set Unique Password | SPS Admin';

        return $this->render('admin/force_password_change', [
            'metaTitle' => $title,
        ]);
    }

    /**
     * Process Admin First-Login Force Password Change (POST)
     */
    public function forcePasswordChangeSubmit(Request $request, string $lang = ''): Response
    {
        if (!AuthService::check()) {
            return $this->redirect(url('/admin/login', I18n::getLocale()));
        }

        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';

        $currentPassword = (string)$request->getPost('current_password', '');
        $newPassword = (string)$request->getPost('new_password', '');
        $newPasswordConfirmation = (string)$request->getPost('new_password_confirmation', '');

        if (empty($currentPassword) || empty($newPassword)) {
            Session::setFlash('error', $isBn ? 'বর্তমান ওয়ান-টাইম পাসওয়ার্ড এবং নতুন পাসওয়ার্ড উভয়টি দেওয়া আবশ্যক।' : 'Both current OTP and new password are required.');
            return $this->redirect(url('/admin/force-password-change', $locale));
        }

        if ($newPassword !== $newPasswordConfirmation) {
            Session::setFlash('error', $isBn ? 'নতুন পাসওয়ার্ড এবং নিশ্চিতকরণ পাসওয়ার্ড মিলছে না।' : 'New password and confirmation do not match.');
            return $this->redirect(url('/admin/force-password-change', $locale));
        }

        if (mb_strlen($newPassword) < 8) {
            Session::setFlash('error', $isBn ? 'নতুন নিজস্ব পাসওয়ার্ড কমপক্ষে ৮ অক্ষরের হতে হবে।' : 'New password must be at least 8 characters.');
            return $this->redirect(url('/admin/force-password-change', $locale));
        }

        $user = AuthService::getCurrentUser();
        $result = AuthService::completeInitialPasswordChange($user['id'], $currentPassword, $newPassword);

        if (!($result['success'] ?? false)) {
            Session::setFlash('error', $result['message']);
            return $this->redirect(url('/admin/force-password-change', $locale));
        }

        Session::setFlash('success', $isBn 
            ? 'আপনার নিজস্ব অনন্য পাসওয়ার্ড সফলভাবে নির্ধারিত হয়েছে! প্রশাসনিক ড্যাশবোর্ডে স্বাগতম।' 
            : 'Your unique personal password has been successfully established! Welcome to the Admin Console.');

        return $this->redirect(url('/admin', $locale));
    }

    /**
     * Super Admin: Create a new admin officer with a generated One-Time Password (OTP) (POST)
     */
    public function createAdminUser(Request $request, string $lang = ''): Response
    {
        if ($guard = $this->requireAuth($request)) return $guard;
        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';

        if (!AuthService::isSuperAdmin()) {
            Session::setFlash('error', $isBn ? 'শুধুমাত্র সুপার অ্যাডমিন নতুন প্রশাসনিক কর্মকর্তা যুক্ত করতে পারেন।' : 'Only Super Admin can create admin users.');
            return $this->redirect(url('/admin/users', $locale));
        }

        $currentAdmin = AuthService::getCurrentUser();
        $userData = [
            'name_bn' => trim((string)$request->getPost('name_bn', '')),
            'name_en' => trim((string)$request->getPost('name_en', '')),
            'username' => trim((string)$request->getPost('username', '')),
            'email' => trim((string)$request->getPost('email', '')),
            'phone' => trim((string)$request->getPost('phone', '')),
            'role' => (string)$request->getPost('role', 'moderator'),
            'designation_bn' => trim((string)$request->getPost('designation_bn', '')),
            'designation_en' => trim((string)$request->getPost('designation_en', '')),
            'scope' => trim((string)$request->getPost('scope', '')),
        ];

        $res = \App\Services\RbacService::createAdminUserWithOtp($userData, $currentAdmin['id']);
        if ($res['success']) {
            Session::setFlash('success', $res['message']);
        } else {
            Session::setFlash('error', $res['message']);
        }

        return $this->redirect(url('/admin/users', $locale));
    }

    /**
     * Super Admin: Reset an officer's password to a fresh One-Time Password (OTP) (POST)
     */
    public function resetAdminOtp(Request $request, string $lang = ''): Response
    {
        if ($guard = $this->requireAuth($request)) return $guard;
        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';

        if (!AuthService::isSuperAdmin()) {
            Session::setFlash('error', $isBn ? 'শুধুমাত্র সুপার অ্যাডমিন পাসওয়ার্ড রিসেট করতে পারেন।' : 'Only Super Admin can reset admin passwords.');
            return $this->redirect(url('/admin/users', $locale));
        }

        $targetUserId = (string)$request->getPost('target_user_id', '');
        $currentAdmin = AuthService::getCurrentUser();

        $res = \App\Services\RbacService::resetAdminOtp($targetUserId, $currentAdmin['id']);
        if ($res['success']) {
            Session::setFlash('success', $res['message']);
        } else {
            Session::setFlash('error', $res['message']);
        }

        return $this->redirect(url('/admin/users', $locale));
    }
}
CODE;

$pos = strrpos($content, '}');
if ($pos !== false) {
    $content = substr($content, 0, $pos) . $methods;
    file_put_contents($file, $content);
    echo "Added OTP controller methods to AdminController.php\n";
}

file_put_contents($file, $content);
echo "AdminController.php updated successfully!\n";
