<?php

declare(strict_types=1);

$file = dirname(__DIR__) . '/app/Views/pages/membership/apply.php';
$content = file_get_contents($file);

// Target 1: Insert password fields after avatar preview div (closing of section 3)
$targetAvatarDiv = "                    </div>\n                </div>\n            </div>\n\n            <!-- Conditional Section A: Student Details -->";

$passwordBlock = "                    </div>\n                </div>\n\n"
    . "                <!-- Account Security & Member Login Password (Set during registration) -->\n"
    . "                <div style=\"margin-top: var(--space-xl); background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border: 1.5px solid #cbd5e1; border-radius: var(--radius-lg); padding: var(--space-lg); box-shadow: var(--shadow-sm);\">\n"
    . "                    <div style=\"display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px; flex-wrap: wrap; gap: 8px;\">\n"
    . "                        <h4 style=\"font-size: 1.05rem; font-weight: 800; color: var(--primary-deep); margin: 0; display: flex; align-items: center; gap: 8px;\">\n"
    . "                            <span>🔐</span>\n"
    . "                            <span><?= \$isBn ? 'লগইন পাসওয়ার্ড নির্ধারণ (Set Account Password)' : 'Account Login Password' ?></span>\n"
    . "                        </h4>\n"
    . "                        <span style=\"font-size: 0.75rem; background: #e0f2fe; color: #0369a1; padding: 2px 10px; border-radius: var(--radius-full); font-weight: 800;\">\n"
    . "                            <?= \$isBn ? 'অনুমোদনের পর লগইনের জন্য আবশ্যক' : 'Required for Login After Approval' ?>\n"
    . "                        </span>\n"
    . "                    </div>\n"
    . "                    <p style=\"font-size: 0.8rem; color: var(--text-muted); margin: 0 0 var(--space-md); line-height: 1.5;\">\n"
    . "                        <?= \$isBn \n"
    . "                            ? 'নিবন্ধন সম্পন্ন হওয়ার পর আপনার আবেদনটি অর্থায়ন অনুমোদনের জন্য অপেক্ষমাণ থাকবে। কোষাধ্যক্ষ ও প্রশাসন কর্তৃক আবেদন অনুমোদিত হওয়ার পর এই পাসওয়ার্ডটি ব্যবহার করে আপনি আপনার সদস্য ড্যাশবোর্ডে লগইন করে প্রোফাইল তথ্য ও ছবি হালনাগাদ করতে পারবেন।' \n"
    . "                            : 'Set your secret login password. Your application will be pending finance verification upon registration. Once approved, you will use this password to log in and update your full member profile.' ?>\n"
    . "                    </p>\n\n"
    . "                    <div style=\"display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-md);\">\n"
    . "                        <div>\n"
    . "                            <label for=\"apply_password\" style=\"display: block; font-size: 0.88rem; font-weight: 700; color: var(--primary-deep); margin-bottom: 4px;\">\n"
    . "                                <?= \$isBn ? 'লগইন পাসওয়ার্ড সেট করুন *' : 'Set Login Password *' ?>\n"
    . "                            </label>\n"
    . "                            <input type=\"password\" id=\"apply_password\" name=\"password\" required minlength=\"6\" placeholder=\"••••••••\" class=\"form-input\" style=\"width: 100%; padding: 10px 14px; border: 1px solid var(--border-medium); border-radius: var(--radius-md);\" autocomplete=\"new-password\">\n"
    . "                            <span style=\"font-size: 0.74rem; color: var(--text-muted); display: block; margin-top: 4px;\"><?= \$isBn ? 'কমপক্ষে ৬ অক্ষরের গোপনীয় পাসওয়ার্ড দিন' : 'Minimum 6 characters' ?></span>\n"
    . "                        </div>\n"
    . "                        <div>\n"
    . "                            <label for=\"apply_password_confirmation\" style=\"display: block; font-size: 0.88rem; font-weight: 700; color: var(--primary-deep); margin-bottom: 4px;\">\n"
    . "                                <?= \$isBn ? 'পাসওয়ার্ড নিশ্চিত করুন *' : 'Confirm Password *' ?>\n"
    . "                            </label>\n"
    . "                            <input type=\"password\" id=\"apply_password_confirmation\" name=\"password_confirmation\" required minlength=\"6\" placeholder=\"••••••••\" class=\"form-input\" style=\"width: 100%; padding: 10px 14px; border: 1px solid var(--border-medium); border-radius: var(--radius-md);\" autocomplete=\"new-password\">\n"
    . "                            <span style=\"font-size: 0.74rem; color: var(--text-muted); display: block; margin-top: 4px;\"><?= \$isBn ? 'একই পাসওয়ার্ড পুনরায় টাইপ করুন' : 'Retype password' ?></span>\n"
    . "                        </div>\n"
    . "                    </div>\n"
    . "                </div>\n"
    . "            </div>\n\n            <!-- Conditional Section A: Student Details -->";

if (strpos($content, $targetAvatarDiv) !== false) {
    $content = str_replace($targetAvatarDiv, $passwordBlock, $content);
    echo "Inserted password inputs into apply.php\n";
} else {
    // Try CRLF
    $targetAvatarDivCRLF = "                    </div>\r\n                </div>\r\n            </div>\r\n\r\n            <!-- Conditional Section A: Student Details -->";
    $passwordBlockCRLF = str_replace("\n", "\r\n", $passwordBlock);
    if (strpos($content, $targetAvatarDivCRLF) !== false) {
        $content = str_replace($targetAvatarDivCRLF, $passwordBlockCRLF, $content);
        echo "Inserted password inputs into apply.php (CRLF)\n";
    } else {
        echo "Could not match target avatar div in apply.php\n";
    }
}

// Target 2: Add client-side validation in submit listener
$targetJs = "    if (appForm) {\n        appForm.addEventListener('submit', function(e) {";
$newJs = "    if (appForm) {\n        appForm.addEventListener('submit', function(e) {\n"
    . "            const pass = document.getElementById('apply_password') ? document.getElementById('apply_password').value : '';\n"
    . "            const passConf = document.getElementById('apply_password_confirmation') ? document.getElementById('apply_password_confirmation').value : '';\n"
    . "            if (!pass || pass.length < 6) {\n"
    . "                alert('অনুগ্রহ করে কমপক্ষে ৬ অক্ষরের একটি লগইন পাসওয়ার্ড নির্ধারণ করুন।');\n"
    . "                e.preventDefault();\n"
    . "                if (document.getElementById('apply_password')) document.getElementById('apply_password').focus();\n"
    . "                return false;\n"
    . "            }\n"
    . "            if (pass !== passConf) {\n"
    . "                alert('পাসওয়ার্ড এবং পাসওয়ার্ড নিশ্চিতকরণ মিলছে না!');\n"
    . "                e.preventDefault();\n"
    . "                if (document.getElementById('apply_password_confirmation')) document.getElementById('apply_password_confirmation').focus();\n"
    . "                return false;\n"
    . "            }\n";

if (strpos($content, $targetJs) !== false) {
    $content = str_replace($targetJs, $newJs, $content);
    echo "Added password validation JS in apply.php\n";
} else {
    $targetJsCRLF = "    if (appForm) {\r\n        appForm.addEventListener('submit', function(e) {";
    $newJsCRLF = str_replace("\n", "\r\n", $newJs);
    if (strpos($content, $targetJsCRLF) !== false) {
        $content = str_replace($targetJsCRLF, $newJsCRLF, $content);
        echo "Added password validation JS in apply.php (CRLF)\n";
    } else {
        echo "Could not match JS submit listener in apply.php\n";
    }
}

file_put_contents($file, $content);
echo "apply.php updated successfully!\n";
