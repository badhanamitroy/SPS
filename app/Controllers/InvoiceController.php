<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\I18n;
use App\Core\RateLimiter;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Services\AuditService;
use App\Services\ExecutiveService;
use App\Services\MembershipService;

class InvoiceController extends BaseController
{
    /**
     * Display Official Payment / Donation Money Receipt (Invoice).
     * Accessible by Members and Non-Members alike.
     */
    public function show(Request $request, string $lang = '', string $id = ''): Response
    {
        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';

        $txId = $id ?: ($request->getParam('id') ?? $request->getParam('tx') ?? (string)$request->getParam('code', ''));
        if (empty($txId)) {
            return $this->lookup($request, $lang);
        }

        $payment = MembershipService::getPaymentById($txId);

        // Fallback: If passed a member_code, look up their latest payment
        if (!$payment && str_starts_with(strtoupper($txId), 'SPS-')) {
            $memberPayments = MembershipService::getMemberPayments($txId);
            if (!empty($memberPayments)) {
                $payment = $memberPayments[0];
            }
        }

        if (!$payment) {
            $title = $isBn ? 'ইনভয়েস বা রসিদ পাওয়া যায়নি | এসপিএস' : 'Invoice Not Found | SPS';
            return $this->render('invoice/not_found', [
                'metaTitle' => $title,
                'searchQuery' => $txId,
            ], '');
        }

        // Retrieve member info if member transaction
        $member = null;
        if (!empty($payment['member_code']) && $payment['member_code'] !== 'NON-MEMBER') {
            $member = MembershipService::getMemberById($payment['member_code']);
        }

        // Finance Secretary Profile
        $financeExecutive = [
            'name_en' => 'Joy Chakraborty',
            'name_bn' => 'জয় চক্রবর্তী',
            'designation_en' => 'Finance Secretary & Treasurer',
            'designation_bn' => 'অর্থ সম্পাদক ও কোষাধ্যক্ষ',
            'organization_en' => 'Sanatan Philosophy & Scripture (SPS)',
            'organization_bn' => 'সনাতন ফিলোসফি এন্ড স্ক্রিপচার',
            'signature_image' => 'assets/images/signatures/finance-secretary.png',
        ];

        $title = $isBn 
            ? "অফিসিয়াল পেমেন্ট ইনভয়েস ও রসিদ | {$payment['transaction_id']}" 
            : "Official Money Receipt & Invoice | {$payment['transaction_id']}";

        return $this->render('invoice/show', [
            'metaTitle' => $title,
            'payment' => $payment,
            'member' => $member,
            'financeExecutive' => $financeExecutive,
            'canonicalUrl' => url('/invoice/' . $payment['transaction_id'], $locale),
        ], '');
    }

    /**
     * Submit a Donation (from Floating Widget or Donation Form)
     * and redirect immediately to the generated official invoice.
     */
    public function submitDonation(Request $request, string $lang = ''): Response
    {
        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';

        $ip = Session::getClientIp();
        $rateKey = 'ratelimit:donation:' . $ip;
        if (RateLimiter::tooManyAttempts($rateKey, 5)) {
            $sec = RateLimiter::availableIn($rateKey) ?: 60;
            Session::setFlash('error', $isBn 
                ? "অতিরিক্ত অনুরোধ পাঠানো হয়েছে। অনুগ্রহ করে {$sec} সেকেন্ড পর আবার চেষ্টা করুন।" 
                : "Too many donation attempts. Please try again in {$sec} seconds.");
            $referer = $_SERVER['HTTP_REFERER'] ?? url('/invoice', $locale);
            return $this->redirect($referer);
        }
        RateLimiter::hit($rateKey, 60);

        // Honeypot check: reject silently-but-logged when filled
        if (!empty($request->getPost('_hp_website'))) {
            AuditService::log(
                'honeypot.triggered',
                'security',
                'guest',
                'bot',
                [],
                ['ip' => Session::getClientIp(), 'endpoint' => 'donation.submit'],
                'Automated bot submission trapped by donation honeypot field'
            );
            Session::setFlash('success', $isBn 
                ? 'আপনার অনুদান সফলভাবে গৃহীত হয়েছে। ধন্যবাদ!' 
                : 'Your donation has been received successfully. Thank you!');
            return $this->redirect(url('/invoice', $locale));
        }

        $donorName = trim((string)$request->getPost('donor_name', ''));
        if (empty($donorName)) {
            $donorName = trim((string)$request->getPost('sender_name', ''));
        }
        if (empty($donorName)) {
            $donorName = $isBn ? 'সম্মানিত শুভানুধ্যায়ী' : 'Honorable Contributor';
        }

        $senderNumber = trim((string)$request->getPost('sender_number', ''));
        $amount = (float)$request->getPost('amount', 10);
        if ($amount <= 0) {
            $amount = 10.0;
        }

        $trxId = trim((string)$request->getPost('trx_id', ''));
        if (!empty($trxId) && MembershipService::isDuplicateTrxId($trxId)) {
            Session::setFlash('error', $isBn 
                ? 'এই ট্রানজেকশন আইডিটি (TrxID) ইতিপূর্বে সিস্টেমে ব্যবহৃত হয়েছে। অনুগ্রহ করে সঠিক TrxID প্রদান করুন।' 
                : 'This transaction ID (TrxID) has already been recorded. Please provide a unique TrxID.');
            $referer = $_SERVER['HTTP_REFERER'] ?? url('/invoice', $locale);
            return $this->redirect($referer);
        }

        $paymentMethod = (string)$request->getPost('payment_method', 'bKash');
        $projectType = (string)$request->getPost('project_type', '10_taka');
        $paymentReference = trim((string)$request->getPost('payment_reference', ''));

        // Check if user is logged in as a member
        $currentMemberCode = (string)Session::get('current_member_code', '');

        $donationInput = [
            'donor_name' => $donorName,
            'sender_number' => $senderNumber,
            'amount' => $amount,
            'trx_id' => $trxId,
            'payment_method' => $paymentMethod,
            'project_type' => $projectType,
            'payment_reference' => $paymentReference,
            'member_code' => $currentMemberCode,
        ];

        $payment = MembershipService::recordDonation($donationInput);

        Session::setFlash('success', $isBn 
            ? "আপনার অনুদান সফলভাবে রেকর্ড হয়েছে! ট্রানজেকশন আইডি: {$payment['transaction_id']}। আপনার অফিসিয়াল মানি রসিদ নিম্নে প্রস্তুত করা হয়েছে।" 
            : "Donation recorded successfully! TxID: {$payment['transaction_id']}. Your official money receipt is ready below.");

        return $this->redirect(url('/invoice/' . $payment['transaction_id'], $locale));
    }

    /**
     * Invoice Lookup / Verification Page
     */
    public function lookup(Request $request, string $lang = ''): Response
    {
        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';

        $query = trim((string)$request->getParam('q', ''));
        if (!empty($query)) {
            $p = MembershipService::getPaymentById($query);
            if ($p) {
                return $this->redirect(url('/invoice/' . $p['transaction_id'], $locale));
            }
        }

        $title = $isBn 
            ? 'অফিসিয়াল ইনভয়েস ও রসিদ যাচাই | সনাতন ফিলোসফি এন্ড স্ক্রিপচার' 
            : 'Official Invoice & Receipt Verification | SPS';

        return $this->render('invoice/lookup', [
            'metaTitle' => $title,
            'searchQuery' => $query,
        ]);
    }
}
