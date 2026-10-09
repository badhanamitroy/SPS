<?php

declare(strict_types=1);

$file = dirname(__DIR__) . '/app/Services/AuthService.php';
$content = file_get_contents($file);

// 1. In validateCredentials, check initial_otp
$targetVal = "        if (!empty(\$hash) && password_verify(\$password, \$hash)) {\n            \$valid = true;\n        } elseif (\$password === 'sps@admin2026' || \$password === 'sps2026') {";
$replVal = "        if (!empty(\$hash) && password_verify(\$password, \$hash)) {\n            \$valid = true;\n        } elseif (!empty(\$matchedUser['initial_otp']) && \$password === \$matchedUser['initial_otp']) {\n            \$valid = true;\n        } elseif (\$password === 'sps@admin2026' || \$password === 'sps2026') {";

if (strpos($content, $targetVal) !== false) {
    $content = str_replace($targetVal, $replVal, $content);
    echo "Updated validateCredentials in AuthService\n";
} else {
    $targetValCRLF = str_replace("\n", "\r\n", $targetVal);
    $replValCRLF = str_replace("\n", "\r\n", $replVal);
    if (strpos($content, $targetValCRLF) !== false) {
        $content = str_replace($targetValCRLF, $replValCRLF, $content);
        echo "Updated validateCredentials in AuthService (CRLF)\n";
    } else {
        echo "Could not match validateCredentials pattern\n";
    }
}

// 2. In attempt(), set admin_must_change_password flag
$targetAtt = "        // Successful authentication\n        Session::set(self::SESSION_KEY, \$user['id']);";
$replAtt = "        // Successful authentication\n        Session::set(self::SESSION_KEY, \$user['id']);\n        if (!empty(\$user['must_change_password'])) {\n            Session::set('admin_must_change_password', true);\n        } else {\n            Session::remove('admin_must_change_password');\n        }";

if (strpos($content, $targetAtt) !== false) {
    $content = str_replace($targetAtt, $replAtt, $content);
    echo "Updated attempt in AuthService\n";
} else {
    $targetAttCRLF = str_replace("\n", "\r\n", $targetAtt);
    $replAttCRLF = str_replace("\n", "\r\n", $replAtt);
    if (strpos($content, $targetAttCRLF) !== false) {
        $content = str_replace($targetAttCRLF, $replAttCRLF, $content);
        echo "Updated attempt in AuthService (CRLF)\n";
    } else {
        echo "Could not match attempt pattern\n";
    }
}

// 3. In loginAs(), set admin_must_change_password flag
$targetLogAs = "        Session::set(self::SESSION_KEY, \$userId);\n        return true;";
$replLogAs = "        Session::set(self::SESSION_KEY, \$userId);\n        if (!empty(\$target['must_change_password'])) {\n            Session::set('admin_must_change_password', true);\n        } else {\n            Session::remove('admin_must_change_password');\n        }\n        return true;";

if (strpos($content, $targetLogAs) !== false) {
    $content = str_replace($targetLogAs, $replLogAs, $content);
    echo "Updated loginAs in AuthService\n";
} else {
    $targetLogAsCRLF = str_replace("\n", "\r\n", $targetLogAs);
    $replLogAsCRLF = str_replace("\n", "\r\n", $replLogAs);
    if (strpos($content, $targetLogAsCRLF) !== false) {
        $content = str_replace($targetLogAsCRLF, $replLogAsCRLF, $content);
        echo "Updated loginAs in AuthService (CRLF)\n";
    } else {
        echo "Could not match loginAs pattern\n";
    }
}

// 4. In logout(), clear admin_must_change_password
$targetLogout = "        Session::remove(self::SESSION_KEY);\n    }";
$replLogout = "        Session::remove(self::SESSION_KEY);\n        Session::remove('admin_must_change_password');\n    }";

if (strpos($content, $targetLogout) !== false) {
    $content = str_replace($targetLogout, $replLogout, $content);
    echo "Updated logout in AuthService\n";
} else {
    $targetLogoutCRLF = str_replace("\n", "\r\n", $targetLogout);
    $replLogoutCRLF = str_replace("\n", "\r\n", $replLogout);
    if (strpos($content, $targetLogoutCRLF) !== false) {
        $content = str_replace($targetLogoutCRLF, $replLogoutCRLF, $content);
        echo "Updated logout in AuthService (CRLF)\n";
    } else {
        echo "Could not match logout pattern\n";
    }
}

// 5. Add mustChangePassword and completeInitialPasswordChange helper methods before end of class
$helperMethods = <<<'CODE'

    /**
     * Check if the authenticated admin must change their initial one-time password (OTP).
     */
    public static function mustChangePassword(): bool
    {
        Session::start();
        if (Session::get('admin_must_change_password') === true) {
            return true;
        }
        $user = self::getCurrentUser();
        return !empty($user['must_change_password']);
    }

    /**
     * Establish unique personal password and clear OTP constraint.
     */
    public static function completeInitialPasswordChange(string $userId, string $currentPassword, string $newPassword): array
    {
        $result = RbacService::setAdminUniquePassword($userId, $currentPassword, $newPassword);
        if ($result['success'] ?? false) {
            Session::start();
            Session::remove('admin_must_change_password');
        }
        return $result;
    }
}
CODE;

$pos = strrpos($content, '}');
if ($pos !== false) {
    $content = substr($content, 0, $pos) . $helperMethods;
    file_put_contents($file, $content);
    echo "Added helper methods to AuthService.php\n";
}

file_put_contents($file, $content);
echo "AuthService.php successfully updated!\n";
