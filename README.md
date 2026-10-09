# Sanatan Philosophy and Scripture (SPS)

<p align="center">
  <img src="public/assets/images/brand/sps-10.png" alt="SPS Logo" width="160" />
</p>

<p align="center">
  <strong>সনাতন দর্শন, শাস্ত্র গবেষণা, সমাজসেবা ও ডিজিটাল গ্রন্থাগারের এক সমন্বিত উন্মুক্ত প্ল্যাটফর্ম</strong><br>
  <em>An authentic, scholarly, cultural, and social-service enterprise platform for Sanatan Dharma and Vedic research.</em>
</p>

<p align="center">
  <a href="#overview">Overview</a> •
  <a href="#core-pillars">Core Pillars</a> •
  <a href="#architecture--tech-stack">Architecture</a> •
  <a href="#security--data-protection">Security</a> •
  <a href="#installation--setup">Setup</a> •
  <a href="#automated-testing">Testing</a> •
  <a href="#deployment">Deployment</a>
</p>

---

## 📖 Overview

**Sanatan Philosophy and Scripture (SPS)** is an authentic, scholarly, cultural, and humanitarian platform. It balances four distinct institutional pillars:

1. **Academic & Spiritual Rigor (জ্ঞান ও তত্ত্ব):** Scripture archiving, philosophical research (Vedanta, Samkhya, Yoga, Gita, Upanishads) with original Sanskrit, Bengali translation/commentaries, and English equivalents.
2. **Community Co-Creation & Moderation (সমাজ ও ব্লগ):** Member authoring, peer collaboration, and tiered editorial moderation workflow (`Draft` → `Pending` → `Approved` / `Rejected`).
3. **Scholarly Digital & Physical Library (গ্রন্থাগার):** In-browser e-book reader with progress tracking, fail-closed preview slicing, access tiers (Public, Member, Entitled), and physical book dispatch management.
4. **Radical Transparency & Seva (সেবা ও আর্থিক স্বচ্ছতা):** Humanitarian initiatives (e.g. *Sanatani 10 Taka Project*, *SPS Welfare Trust*), verifiable invoices, blind-indexed transaction security, and tamper-resistant audit trails.

---

## 🏛️ Core Pillars & Modules

### 1. 📚 Digital & Physical Library
- **Dual Reading Experience:** Custom responsive browser reader + partial page preview generator for non-members.
- **Fail-Closed PDF Slicer:** High-security PDF previews via PyMuPDF backend with strict fail-closed fallbacks (no full document leaks).
- **Physical Book Lending:** Request management, queue tracking, and administrative dispatch tracking.

### 2. ✍️ Community Publishing & Editorial Workflow
- **Member Blogging:** Paid and verified members can draft and submit articles for review.
- **Three-Tier Moderation:** Super Admin, Admin, and Literature-Admin review, approve, or reject manuscripts with feedback.
- **Sanitized Rich Content:** Real-time sanitization of all HTML entries stripping malicious scripts, attributes, and tags.
- **Engagement Features:** Real-time reading counters, unique-IP likes, and Facebook-style threaded discussions.

### 3. 👥 Membership Management & Digital Identity
- **Membership Categories:** Student (`STUDENT`), Earning (`EARNING`), Monthly, Yearly, and Lifetime (`LIFETIME`) memberships.
- **Member ID Cards:** Dynamic generation of verified digital identity cards featuring high-density QR verification tokens.
- **Google OAuth 2.0 Integration:** Frictionless sign-up and login with automatic member account provisioning.
- **Finance Approval Queue:** Treasurer / Finance Officer verification workflow with proof-of-payment review.

### 4. 💰 Financial Transparency & Contributions
- **Seva Projects:** Dedicated donation portals for humanitarian aid, student funds, and temple welfare.
- **Duplicate Prevention:** Keyed blind indexing (`trx_id_bidx`) ensures duplicate Transaction IDs (bKash, Nagad, Rocket) are immediately detected and rejected.
- **Official Invoices:** Instant digitally signable and printable receipts with Finance Secretary verification.

---

## ⚙️ Architecture & Tech Stack

```
                           +---------------------------+
                           |  Web Client (Desktop/App) |
                           +---------------------------+
                                         |
                                    HTTPS / TLS
                                         |
                           +---------------------------+
                           |   Nginx / Apache Server   |
                           |    DocumentRoot: /public  |
                           +---------------------------+
                                         |
                                public/index.php (Front Controller)
                                         |
          +------------------------------+------------------------------+
          |                                                             |
   HTTP Kernel / Router                                          Security Middleware
   - Locale Resolver (/bn, /en)                                  - CSRF Enforcement
   - Bilingual Route Matching                                    - IP Rate Limiter
   - Controller Dispatch                                         - Honeypot Shield
          |                                                             |
          +------------------------------+------------------------------+
                                         |
                               Domain Controllers
     (HomeController, BlogController, LibraryController, MembershipController, AdminController)
                                         |
                               Domain Services Layer
     (AuthService, MembershipService, BlogService, LibraryService, CryptoService, AuditService)
                                         |
                   +---------------------+---------------------+
                   |                                           |
           Storage Engine                               External Integrations
           - Mutually Exclusive flock()                 - Google OAuth 2.0
           - Atomic File Renames (temp+rename)          - PyMuPDF Slicer
           - AES-256-GCM Encrypted JSON Records         - SMTP Mail Dispatcher
```

- **Runtime:** PHP 8.0.30 (Strict typing, no heavy monolithic framework dependencies).
- **Frontend:** Vanilla modern JavaScript, custom CSS design system with Dark/Light theme switching, responsive grid, and glassmorphism styling.
- **Storage:** High-performance, concurrent-safe flat-file JSON datastores (`storage/data/`).
- **Typography:** Google Fonts (`Hind Siliguri`, `Outfit`, `Inter`).

---

## 🔒 Security & Data Protection

| Security Control | Implementation Standard |
| :--- | :--- |
| **Password Hashing** | Argon2id with 32-byte server-side secret pepper (`PASSWORD_PEPPER`). |
| **PII Data Encryption** | AES-256-GCM AEAD encryption on sensitive fields (Phone numbers, Addresses, TrxIDs). |
| **Searchable Encryption** | Keyed HMAC-SHA256 Blind Indexing (`BLIND_INDEX_KEY`) with phone & transaction normalization. |
| **JSON Store Concurrency** | Exclusive advisory locks (`LOCK_EX`) spanning the entire read-modify-write cycle + atomic temp-file rename. |
| **Double-Submit Guard** | Synchronized JS client-side button lock + backend blind-indexed TrxID deduplication. |
| **Abuse & Bot Prevention** | Invisible honeypot traps (`_hp_website`) + IP sliding-window Rate Limiting. |
| **XSS & Content Cleansing** | Strict HTML whitelist sanitization (`HtmlSanitizer`) stripping `<script>`, `onerror`, and JS pseudo-protocols. |
| **Access Control (IDOR)** | Ownership verification on member dashboards, PDF streams, and administrative controls. |

---

## 🚀 Installation & Local Setup

### Prerequisites
- **PHP**: `8.0` or higher with `openssl`, `mbstring`, `fileinfo`, `json` extensions.
- **Web Server**: Apache / Nginx or built-in PHP development server.
- **Python**: `3.8+` (Optional, required only for local PDF preview generation).

### 1. Clone the Repository
```bash
git clone https://github.com/badhanamitroy/SPS.git
cd SPS
```

### 2. Configure Environment Variables
Copy the configuration template:
```bash
cp .env.example .env
```
Generate your 32-byte hexadecimal secrets and populate `.env`:
```ini
APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_TIMEZONE=Asia/Dhaka

# 32-byte Hex (64 hex characters)
APP_KEY=your_generated_32_byte_hex_key_here
APP_KEY_ACTIVE=v1
APP_KEY_V1=your_generated_32_byte_hex_key_here

# 32-byte Hex Passwords & Indexing Keys
PASSWORD_PEPPER=your_generated_32_byte_pepper_here
BLIND_INDEX_KEY=your_generated_32_byte_blind_index_key_here

SESSION_LIFETIME=1800
SESSION_SECURE_COOKIE=false
```

### 3. Run Development Server
Start the local PHP development server pointing strictly to the `/public` directory:
```bash
php -S 127.0.0.1:8000 -t public public/index.php
```
Open your browser and navigate to:
```
http://127.0.0.1:8000/bn
```

---

## 🧪 Automated Testing

The platform features an automated test runner executing 26 comprehensive test suites covering unit, integration, and security regressions:

```bash
php tests/run_all.php
```

### Test Coverage Highlights
- ✅ **`test_phase1_security.php`**: Validates fail-closed PDF streaming, disabled role simulator in production, SVG rejection on payment screenshots, CSRF token enforcement, HTML sanitizer, and dashboard IDOR prevention.
- ✅ **`test_t4_double_submit_and_trx.php`**: Validates client-side button disabling and server-side duplicate TrxID rejection.
- ✅ **`test_t5_rate_limiting.php`**: Validates rate-limiter throttling across public POST endpoints.
- ✅ **`test_t6_honeypot.php`**: Validates silent rejection and audit logging of automated bot submissions.
- ✅ **`test_t7_json_safety.php`**: Validates exclusive flock reentrancy and atomic writes under concurrency.
- ✅ **`test_google_auth_security.php`**: Validates OAuth token exchange, state validation, and automatic member provisioning.

---

## 🌐 Production Deployment

Refer to the complete deployment guide in [`docs/deployment.md`](docs/deployment.md).

### Key Production Rules:
1. **DocumentRoot**: Must point strictly to `/public`. Direct web access to `storage/`, `app/`, `Media/`, or `.env` must be completely blocked (returns HTTP 403/404).
2. **Environment**:
   ```ini
   APP_ENV=production
   APP_DEBUG=false
   SESSION_SECURE_COOKIE=true
   ```
3. **Backup Schedule**: Automate daily archives of `storage/data/` (JSON database) and `Media/` (uploaded user documents).

---

## 📜 License

This project is licensed under the **MIT License**.

---

<p align="center">
  <strong>Sanatan Philosophy and Scripture (SPS)</strong><br>
  <em>Dedicated to Truth, Knowledge, and Humanitarian Service.</em>
</p>
