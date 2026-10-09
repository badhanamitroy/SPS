<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Session;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

class EmailService
{
    private static function smtpConfig(): array
    {
        return [
            'host'       => $_ENV['SMTP_HOST'] ?? 'smtp.gmail.com',
            'port'       => (int)($_ENV['SMTP_PORT'] ?? 587),
            'encryption' => strtolower($_ENV['SMTP_ENCRYPTION'] ?? 'tls'),
            'user'       => $_ENV['SMTP_USER'] ?? '',
            'pass'       => $_ENV['SMTP_PASS'] ?? '',
            'from'       => $_ENV['SMTP_FROM'] ?? ($_ENV['SMTP_USER'] ?? ''),
            'from_name'  => $_ENV['SMTP_FROM_NAME'] ?? 'SPS Platform Security',
        ];
    }

    /**
     * Returns true when a real SMTP password is configured.
     * Views use this to decide whether to show the dev-sandbox OTP helper.
     */
    public static function isSmtpConfigured(): bool
    {
        return !empty($_ENV['SMTP_PASS']);
    }

    public static function send2FaOtp(
        string $toEmail,
        string $userName,
        string $code,
        string $accountType = 'Admin'
    ): bool {
        // Route: admin OTPs -> MAIL_ADMIN, member OTPs -> MAIL_MEMBER_OVERRIDE (dev) or member email (prod)
        $finalRecipient = $toEmail;
        if ($accountType === 'Administrator' && !empty($_ENV['MAIL_ADMIN'])) {
            $finalRecipient = $_ENV['MAIL_ADMIN'];
        } elseif ($accountType === 'Member' && !empty($_ENV['MAIL_MEMBER_OVERRIDE'])) {
            $finalRecipient = $_ENV['MAIL_MEMBER_OVERRIDE'];
        }

        $subject = "Your SPS 2FA Verification Code: {$code}";
        $htmlMessage = self::buildOtpHtml($userName, $code, $accountType);

        // Store in session ONLY when SMTP is NOT configured (local dev sandbox mode)
        if (!self::isSmtpConfigured()) {
            Session::set('sps_dev_last_otp', [
                'email'   => $toEmail,
                'name'    => $userName,
                'code'    => $code,
                'type'    => $accountType,
                'sent_at' => time(),
            ]);
        } else {
            Session::forget('sps_dev_last_otp');
        }

        return self::deliverEmail($finalRecipient, $userName, $subject, $htmlMessage);
    }

    private static function buildOtpHtml(string $userName, string $code, string $accountType): string
    {
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f7f3ed; color: #1e293b; margin: 0; padding: 24px; }
        .card { max-width: 520px; margin: 0 auto; background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 16px rgba(0,0,0,0.06); }
        .header { background: #14202e; color: #ffffff; padding: 24px; text-align: center; border-bottom: 3px solid #c65a1e; }
        .header h1 { font-size: 20px; margin: 0 0 4px; color: #ffffff; }
        .header p { font-size: 13px; color: #94a3b8; margin: 0; }
        .content { padding: 28px 24px; }
        .otp-box { background: #fffbeb; border: 2px dashed #f59e0b; border-radius: 8px; text-align: center; padding: 18px; margin: 24px 0; }
        .otp-code { font-family: 'Courier New', Courier, monospace; font-size: 32px; font-weight: bold; letter-spacing: 8px; color: #b45309; }
        .footer { font-size: 12px; color: #64748b; border-top: 1px solid #e2e8f0; padding: 16px 24px; text-align: center; background: #f8fafc; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <h1>Sanatan Philosophy and Scripture (SPS)</h1>
            <p>Two-Factor Authentication (2FA) Security Gateway</p>
        </div>
        <div class="content">
            <p>Namaskar <strong>{$userName}</strong>,</p>
            <p>A sign-in attempt was initiated for your SPS {$accountType} Account. Use the one-time verification code below to complete your login:</p>
            <div class="otp-box">
                <div style="font-size:12px;text-transform:uppercase;color:#92400e;font-weight:bold;margin-bottom:6px;">Your 6-Digit Verification Code</div>
                <div class="otp-code">{$code}</div>
                <div style="font-size:12px;color:#78350f;margin-top:6px;">Valid for 10 minutes. Never share this code with anyone.</div>
            </div>
            <p style="font-size:13px;color:#475569;line-height:1.5;">
                If you did not initiate this login request, your password may be compromised. Please contact SPS Security Administration immediately.
            </p>
        </div>
        <div class="footer">&copy; 2026 Sanatan Philosophy and Scripture (SPS) Platform. All rights reserved.</div>
    </div>
</body>
</html>
HTML;
    }

    public static function sendPasswordChangedAlert(
        string $toEmail,
        string $userName,
        string $accountType = 'Admin'
    ): bool {
        $subject = "Security Alert: SPS {$accountType} Password Updated";
        $now = date('Y-m-d H:i:s T');
        $ip  = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        $html = <<<HTML
<!DOCTYPE html><html><head><meta charset="utf-8">
<style>body{font-family:Arial,sans-serif;background:#f7f3ed;color:#1e293b;padding:24px;}
.card{max-width:520px;margin:0 auto;background:#fff;border-radius:12px;border:1px solid #e2e8f0;padding:24px;}
.header{text-align:center;border-bottom:2px solid #c65a1e;padding-bottom:14px;margin-bottom:20px;}</style>
</head><body><div class="card">
<div class="header"><h2 style="margin:0;color:#14202e;">SPS Account Security Alert</h2></div>
<p>Namaskar <strong>{$userName}</strong>,</p>
<p>The password for your SPS {$accountType} account was updated on <strong>{$now}</strong> (IP: <code>{$ip}</code>).</p>
<p>If you made this change, you can safely ignore this email.</p>
<p style="color:#b91c1c;font-weight:bold;">If you did NOT authorise this, contact SPS administration immediately.</p>
</div></body></html>
HTML;

        return self::deliverEmail($toEmail, $userName, $subject, $html);
    }

    public static bool $simulateFailure = false;

    public static function sendMembershipApprovedEmail(array $member, array $payment): array
    {
        $toEmail    = trim((string)($member['email'] ?? ''));
        $memberName = $member['name_bn'] ?: $member['name_en'] ?: 'Honorable Member';
        $memberCode = $member['member_code'] ?? 'SPS-MEMBER';
        $txId       = $payment['transaction_id'] ?? '';
        $trxId      = $payment['trx_id'] ?? 'N/A';
        $amount     = number_format((float)($payment['amount'] ?? 0), 2);
        $invoiceUrl = '/bn/invoice/' . urlencode($txId);
        $subject    = "সদস্যপদ সক্রিয় ও পেমেন্ট ভেরিফিকেশন সম্পন্ন | SPS ({$memberCode})";

        $html = <<<HTML
<!DOCTYPE html><html><head><meta charset="utf-8">
<style>body{font-family:'Segoe UI',Arial,sans-serif;background:#f7f3ed;color:#1e293b;padding:24px;}
.card{max-width:600px;margin:0 auto;background:#fff;border-radius:12px;border:1px solid #e2e8f0;overflow:hidden;}
.hdr{background:#14202e;color:#fff;padding:24px;text-align:center;border-bottom:3px solid #15803d;}
.content{padding:28px 24px;}.row{display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px dashed #e2e8f0;font-size:14px;}
.btn{display:inline-block;background:#15803d;color:#fff;text-decoration:none;padding:12px 24px;border-radius:6px;font-weight:bold;margin-top:18px;}
.footer{font-size:12px;color:#64748b;border-top:1px solid #e2e8f0;padding:16px 24px;text-align:center;}</style>
</head><body><div class="card">
<div class="hdr"><h1 style="font-size:20px;margin:0 0 4px;color:#fff;">সনাতন ফিলোসফি এন্ড স্ক্রিপচার (SPS)</h1>
<p style="font-size:13px;color:#86efac;margin:0;font-weight:bold;">✓ সদস্যপদ অনুমোদন নিশ্চিতকরণ</p></div>
<div class="content">
<p>নমস্কার <strong>{$memberName}</strong>,</p>
<p>আপনার সদস্যপদ আবেদনটি চূড়ান্তভাবে অনুমোদিত হয়েছে।</p>
<div style="background:#f0fdf4;border:1.5px solid #86efac;border-radius:8px;padding:16px;margin:20px 0;">
<div class="row"><span>মেম্বার আইডি:</span><strong>{$memberCode}</strong></div>
<div class="row"><span>ট্রানজেকশন আইডি:</span><strong>{$txId}</strong></div>
<div class="row"><span>TrxID:</span><strong>{$trxId}</strong></div>
<div class="row"><span>গৃহীত অর্থ:</span><strong>৳ {$amount} BDT</strong></div>
<div class="row"><span>স্ট্যাটাস:</span><strong style="color:#15803d;">Active Member</strong></div>
</div>
<div style="text-align:center;"><a href="{$invoiceUrl}" class="btn" style="color:#fff;">📄 মানি রসিদ / ইনভয়েস দেখুন</a></div>
</div>
<div class="footer">&copy; 2026 SPS Platform. All rights reserved.</div>
</div></body></html>
HTML;

        if (self::$simulateFailure) {
            AuditService::log('email.failed_simulated', 'security', 'system', 'SPS Mailer', [], ['to' => $toEmail, 'subject' => $subject], "Simulated failure for {$memberCode}");
            return ['delivered' => false, 'status' => 'FAILED', 'error' => 'Simulated delivery failure'];
        }

        $delivered = self::deliverEmail($toEmail, $memberName, $subject, $html);
        return [
            'delivered' => $delivered,
            'status'    => $delivered ? 'SENT' : 'FAILED',
            'error'     => $delivered ? null : 'SMTP delivery returned false',
        ];
    }

    // ---------------------------------------------------------------
    // Core PHPMailer delivery engine
    // ---------------------------------------------------------------
    private static function deliverEmail(
        string $toEmail,
        string $toName,
        string $subject,
        string $htmlBody
    ): bool {
        if (empty($toEmail) || filter_var($toEmail, FILTER_VALIDATE_EMAIL) === false) {
            return false;
        }

        $cfg = self::smtpConfig();
        $delivered = false;
        $errorMsg  = null;

        if (self::isSmtpConfigured()) {
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = $cfg['host'];
                $mail->SMTPAuth   = true;
                $mail->Username   = $cfg['user'];
                $mail->Password   = $cfg['pass'];
                $mail->SMTPSecure = $cfg['encryption'] === 'ssl'
                    ? PHPMailer::ENCRYPTION_SMTPS
                    : PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = $cfg['port'];
                $mail->CharSet    = 'UTF-8';
                $mail->setFrom($cfg['from'], $cfg['from_name']);
                $mail->addAddress($toEmail, $toName);
                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body    = $htmlBody;
                $mail->AltBody = strip_tags(str_replace(['<br>', '<br/>', '</p>'], "\n", $htmlBody));
                $mail->send();
                $delivered = true;
            } catch (PHPMailerException $e) {
                $errorMsg  = $e->getMessage();
                $delivered = false;
            }
        } else {
            $headers = implode("\r\n", [
                'MIME-Version: 1.0',
                'Content-type: text/html; charset=UTF-8',
                "From: {$cfg['from_name']} <{$cfg['from']}>",
                'Reply-To: contact@sps-platform.org',
                'X-Mailer: SPS-PHP/' . phpversion(),
            ]);
            $delivered = function_exists('mail') ? @mail($toEmail, $subject, $htmlBody, $headers) : false;
        }

        AuditService::log(
            'email.dispatched', 'security', 'system', 'SPS Mailer', [],
            ['to' => $toEmail, 'subject' => $subject, 'smtp' => self::isSmtpConfigured(), 'delivered' => $delivered, 'error' => $errorMsg],
            "Email to '{$toEmail}': " . ($delivered ? 'SUCCESS' : 'FAILED - ' . ($errorMsg ?? 'mail() false'))
        );

        return $delivered;
    }
}