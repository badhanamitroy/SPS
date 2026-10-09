<?php

declare(strict_types=1);

$file = dirname(__DIR__) . '/routes/web.php';
$content = file_get_contents($file);

// 1. Add force-password-change routes right after admin 2fa routes
$target2fa = "\$router->post('/{lang}/admin/2fa/resend', 'AdminController@twoFactorResend', 'admin.2fa.resend');";
$repl2fa = "\$router->post('/{lang}/admin/2fa/resend', 'AdminController@twoFactorResend', 'admin.2fa.resend');\n"
    . "\$router->get('/{lang}/admin/force-password-change', 'AdminController@forcePasswordChangePage', 'admin.force_password_change');\n"
    . "\$router->post('/{lang}/admin/force-password-change', 'AdminController@forcePasswordChangeSubmit', 'admin.force_password_change.submit');";

if (strpos($content, $target2fa) !== false) {
    $content = str_replace($target2fa, $repl2fa, $content);
    echo "Added force-password-change routes to web.php\n";
} else {
    $target2faCRLF = str_replace("\n", "\r\n", $target2fa);
    $repl2faCRLF = str_replace("\n", "\r\n", $repl2fa);
    if (strpos($content, $target2faCRLF) !== false) {
        $content = str_replace($target2faCRLF, $repl2faCRLF, $content);
        echo "Added force-password-change routes to web.php (CRLF)\n";
    }
}

// 2. Add users/create and users/reset-otp routes right after users/assign-role
$targetAssign = "\$router->post('/{lang}/admin/users/assign-role', 'AdminController@assignRole', 'admin.users.assign_role');";
$replAssign = "\$router->post('/{lang}/admin/users/assign-role', 'AdminController@assignRole', 'admin.users.assign_role');\n"
    . "\$router->post('/{lang}/admin/users/create', 'AdminController@createAdminUser', 'admin.users.create');\n"
    . "\$router->post('/{lang}/admin/users/reset-otp', 'AdminController@resetAdminOtp', 'admin.users.reset_otp');";

if (strpos($content, $targetAssign) !== false) {
    $content = str_replace($targetAssign, $replAssign, $content);
    echo "Added admin user create and reset OTP routes to web.php\n";
} else {
    $targetAssignCRLF = str_replace("\n", "\r\n", $targetAssign);
    $replAssignCRLF = str_replace("\n", "\r\n", $replAssign);
    if (strpos($content, $targetAssignCRLF) !== false) {
        $content = str_replace($targetAssignCRLF, $replAssignCRLF, $content);
        echo "Added admin user create and reset OTP routes to web.php (CRLF)\n";
    }
}

file_put_contents($file, $content);
echo "web.php routes successfully updated!\n";
