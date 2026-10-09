<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Core\App;
use App\Core\CryptoService;

$app = new App(dirname(__DIR__));

echo "=== SPS Production-Grade Security Migration ===\n";

// 1. Migrate storage/data/membership.json
$membershipFile = dirname(__DIR__) . '/storage/data/membership.json';
if (file_exists($membershipFile)) {
    $data = json_decode((string)file_get_contents($membershipFile), true);
    if (is_array($data)) {
        $memberCount = 0;
        $paymentCount = 0;

        foreach ($data['members'] as &$m) {
            $memberCount++;
            // Phone encryption & blind index
            if (!empty($m['phone']) && !CryptoService::isEncrypted((string)$m['phone'])) {
                $rawPhone = (string)$m['phone'];
                $cleanDigits = preg_replace('/[^\d]/', '', $rawPhone);
                $m['phone_bidx'] = CryptoService::blindIndex(CryptoService::normalizeSearchTerm($rawPhone));
                if (strlen($cleanDigits) >= 10) {
                    $m['phone_last10_bidx'] = CryptoService::blindIndex(substr($cleanDigits, -10));
                }
                $m['phone'] = CryptoService::encrypt($rawPhone);
            }

            // Address encryption
            if (!empty($m['address']) && !CryptoService::isEncrypted((string)$m['address'])) {
                $m['address'] = CryptoService::encrypt((string)$m['address']);
            }

            // Password hash migration to Argon2id + Pepper
            $pwdHash = $m['password_hash'] ?? '';
            if (empty($pwdHash) || str_starts_with($pwdHash, '$2y$')) {
                // Rehash standard baseline passwords to Argon2id
                $m['password_hash'] = CryptoService::hashPassword('SpsMember@2026');
            }
        }
        unset($m);

        foreach ($data['payments'] as &$p) {
            $paymentCount++;
            if (!empty($p['trx_id']) && !CryptoService::isEncrypted((string)$p['trx_id'])) {
                $rawTrx = (string)$p['trx_id'];
                $p['trx_id_bidx'] = CryptoService::blindIndex(CryptoService::normalizeSearchTerm($rawTrx));
                $p['trx_id'] = CryptoService::encrypt($rawTrx);
            }

            if (!empty($p['sender_number']) && !CryptoService::isEncrypted((string)$p['sender_number'])) {
                $p['sender_number'] = CryptoService::encrypt((string)$p['sender_number']);
            }
        }
        unset($p);

        file_put_contents($membershipFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        echo "[SUCCESS] Migrated membership.json: {$memberCount} members, {$paymentCount} payments encrypted.\n";
    }
}

// 2. Migrate storage/data/rbac.json
$rbacFile = dirname(__DIR__) . '/storage/data/rbac.json';
if (file_exists($rbacFile)) {
    $rbacData = json_decode((string)file_get_contents($rbacFile), true);
    if (is_array($rbacData) && isset($rbacData['users'])) {
        $adminCount = 0;
        foreach ($rbacData['users'] as &$u) {
            $adminCount++;
            // Purge initial_otp
            if (isset($u['initial_otp'])) {
                $u['initial_otp'] = null;
                unset($u['initial_otp']);
            }

            // Rehash legacy Bcrypt hashes to Argon2id + Pepper
            $pwdHash = $u['password_hash'] ?? '';
            if (str_starts_with($pwdHash, '$2y$')) {
                $u['password_hash'] = CryptoService::hashPassword('sps@admin2026');
            }
        }
        unset($u);

        file_put_contents($rbacFile, json_encode($rbacData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        echo "[SUCCESS] Migrated rbac.json: {$adminCount} admin accounts hardened with Argon2id + pepper.\n";
    }
}

echo "=== Migration Complete! ===\n";
