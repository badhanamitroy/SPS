<?php

declare(strict_types=1);

// 1. Update MembershipController.php submitApplication to validate password
$controllerFile = dirname(__DIR__) . '/app/Controllers/MembershipController.php';
$controllerContent = file_get_contents($controllerFile);

$targetCheck = "        if (empty(\$phone)) {\n            Session::setFlash('error', \$isBn ? 'যোগাযোগের মোবাইল নম্বর প্রদান করা আবশ্যক।' : 'Phone number is required.');\n            return \$this->redirect(url('/membership/apply', \$locale));\n        }";

$passwordCheck = "        if (empty(\$phone)) {\n            Session::setFlash('error', \$isBn ? 'যোগাযোগের মোবাইল নম্বর প্রদান করা আবশ্যক।' : 'Phone number is required.');\n            return \$this->redirect(url('/membership/apply', \$locale));\n        }\n\n"
    . "        \$password = (string)\$request->getPost('password', '');\n"
    . "        \$passwordConfirmation = (string)\$request->getPost('password_confirmation', '');\n"
    . "        if (empty(\$password) || mb_strlen(\$password) < 6) {\n"
    . "            Session::setFlash('error', \$isBn ? 'কমপক্ষে ৬ অক্ষরের একটি লগইন পাসওয়ার্ড প্রদান করা আবশ্যক।' : 'A login password of at least 6 characters is required.');\n"
    . "            return \$this->redirect(url('/membership/apply', \$locale));\n"
    . "        }\n"
    . "        if (\$password !== \$passwordConfirmation) {\n"
    . "            Session::setFlash('error', \$isBn ? 'পাসওয়ার্ড এবং পাসওয়ার্ড নিশ্চিতকরণ মিলছে না।' : 'Password and password confirmation do not match.');\n"
    . "            return \$this->redirect(url('/membership/apply', \$locale));\n"
    . "        }";

if (strpos($controllerContent, $targetCheck) !== false) {
    $controllerContent = str_replace($targetCheck, $passwordCheck, $controllerContent);
    echo "Added password validation to submitApplication in MembershipController\n";
} else {
    echo "Target check not found in MembershipController (trying CRLF)\n";
    $targetCheckCRLF = str_replace("\n", "\r\n", $targetCheck);
    $passwordCheckCRLF = str_replace("\n", "\r\n", $passwordCheck);
    if (strpos($controllerContent, $targetCheckCRLF) !== false) {
        $controllerContent = str_replace($targetCheckCRLF, $passwordCheckCRLF, $controllerContent);
        echo "Added password validation to submitApplication in MembershipController (CRLF)\n";
    }
}

// Add 'password' => $password to $inputData in MembershipController
$targetInputData = "            'payment_screenshot' => \$paymentScreenshot,\n        ];";
$replacementInputData = "            'payment_screenshot' => \$paymentScreenshot,\n            'password' => \$password,\n        ];";

if (strpos($controllerContent, $targetInputData) !== false) {
    $controllerContent = str_replace($targetInputData, $replacementInputData, $controllerContent);
    echo "Added password to inputData in MembershipController\n";
} else {
    $targetInputDataCRLF = str_replace("\n", "\r\n", $targetInputData);
    $replacementInputDataCRLF = str_replace("\n", "\r\n", $replacementInputData);
    if (strpos($controllerContent, $targetInputDataCRLF) !== false) {
        $controllerContent = str_replace($targetInputDataCRLF, $replacementInputDataCRLF, $controllerContent);
        echo "Added password to inputData in MembershipController (CRLF)\n";
    }
}

file_put_contents($controllerFile, $controllerContent);

// 2. Update MembershipService.php createApplication to store password_hash
$serviceFile = dirname(__DIR__) . '/app/Services/MembershipService.php';
$serviceContent = file_get_contents($serviceFile);

$targetMemberArray = "            'category_id' => \$category['id'] ?? 'STUDENT',\n            'plan_id' => \$plan['id'] ?? 'STUDENT_MONTHLY',\n            'status' => 'Pending',";
$replacementMemberArray = "            'category_id' => \$category['id'] ?? 'STUDENT',\n            'plan_id' => \$plan['id'] ?? 'STUDENT_MONTHLY',\n            'status' => 'Pending',\n            'password_hash' => !empty(\$input['password']) ? password_hash((string)\$input['password'], PASSWORD_BCRYPT) : password_hash('sps@member2026', PASSWORD_BCRYPT),";

if (strpos($serviceContent, $targetMemberArray) !== false) {
    $serviceContent = str_replace($targetMemberArray, $replacementMemberArray, $serviceContent);
    echo "Added password_hash to newMember in MembershipService\n";
} else {
    $targetMemberArrayCRLF = str_replace("\n", "\r\n", $targetMemberArray);
    $replacementMemberArrayCRLF = str_replace("\n", "\r\n", $replacementMemberArray);
    if (strpos($serviceContent, $targetMemberArrayCRLF) !== false) {
        $serviceContent = str_replace($targetMemberArrayCRLF, $replacementMemberArrayCRLF, $serviceContent);
        echo "Added password_hash to newMember in MembershipService (CRLF)\n";
    }
}

file_put_contents($serviceFile, $serviceContent);
echo "Membership registration password backend successfully updated!\n";
