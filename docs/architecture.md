# Sanatan Philosophy and Scripture (SPS) — Master Architectural Specification & Blueprint

**Document Version:** 1.0.0  
**Target Release:** Production  
**Lead Roles:** Product Architect, Senior UI/UX Designer, Senior PHP/MySQL Engineer, Security Engineer, QA Engineer  

---

## 1. Specification Analysis & Core Philosophy

### 1.1 Organizational Character & Positioning
**Sanatan Philosophy and Scripture (SPS)** is an authentic, scholarly, cultural, and social-service organization. It is neither a commercial startup, nor an esoteric cult site, nor a generic NGO brochure.

The platform balances four distinct institutional pillars:
1. **Academic & Spiritual Rigor (জ্ঞান ও তত্ত্ব):** Authentic scripture archiving, chapter/verse hierarchy, philosophical exegesis (Vedanta, Samkhya, Yoga, Gita, Upanishads) with original Sanskrit, Bengali translation/commentary, and English equivalents.
2. **Community Co-Creation & Moderation (সমাজ ও ব্লগ):** Member authoring, peer collaboration, and tiered human moderation workflow (Draft → Review → Revision Requested → Approved → Published).
3. **Scholarly Digital & Physical Library (গ্রন্থাগার):** High-standard e-book reader with progress tracking, controlled access tiers (Public, Member, Entitled), and an integrated physical book inventory/dispatch tracking workflow.
4. **Radical Transparency & Seva (সেবা ও আর্থিক স্বচ্ছতা):** Real-world humanitarian projects, audited financial breakdowns, verified transaction ledgers, server-side aggregation, and read-only Google Sheets synchronization for administrative reporting.

### 1.2 Bilingual First Principles
- **Default Language:** Bangla (`bn`).
- **Secondary Language:** English (`en`).
- **No Guessing / No Auto-Redirect by Browser Headers:** Visitors select explicitly. Selected locale is persisted via URL prefix (`/bn/`, `/en/`), verified session, cookie/localStorage fallback, and user profile database preference for authenticated accounts.
- **Relational Translation Separation:** Content is never stored as composite strings (e.g. never `"বাংলা / English"`). Master entities (`posts`, `books`, `projects`, `verses`) maintain structural attributes; multi-row translation tables (`post_translations`, `book_translations`, etc.) store locale-specific copy, slugs, and SEO metadata. Missing translations gracefully fall back or indicate availability without blocking the default language.

---

## 2. Complete Project Architecture

```
                      +---------------------------------------+
                      |       Web Client (Desktop / Mobile)   |
                      +---------------------------------------+
                                          |
                                    HTTPS / WAF
                                          |
                      +---------------------------------------+
                      |         Web Server (Apache/Nginx)     |
                      |        Public DocumentRoot: /public   |
                      +---------------------------------------+
                                          |
                         Front Controller (`public/index.php`)
                                          |
        +---------------------------------+---------------------------------+
        |                                                                   |
+---------------+                                                   +---------------+
| HTTP Kernel   |                                                   | Security Core |
| - Request/URI |                                                   | - CSRF Guard  |
| - Locale Detect                                                   | - Rate Limiter|
| - Route Match |                                                   | - XSS Filter  |
+---------------+                                                   | - CSP Headers |
        |                                                           +---------------+
        v                                                                   |
+---------------+                                                           |
| Middleware    | <---------------------------------------------------------+
| Pipeline      | (Session, Auth, Role-Based Access Control, CSRF, Secure Headers)
+---------------+
        |
        v
+-----------------------------------------------------------------------------------+
| Controllers Layer                                                                 |
| [PublicController] [BlogController] [LibraryController] [FinanceController] [Admin]
+-----------------------------------------------------------------------------------+
        |                                           |
        v                                           v
+-------------------------+             +-------------------------+
| Domain Services Layer   |             | View / Template Engine  |
| - AuthService (Google)  |             | - Semantic HTML5        |
| - BlogWorkflowService   |             | - Bilingual Helpers     |
| - LibraryReaderService  |             | - Editorial Components  |
| - InventoryService      |             | - Asset Minification    |
| - FinancialLedgerService|             +-------------------------+
| - GoogleSheetsSyncService             
+-------------------------+
        |
        v
+-------------------------+
| Repository / ORM Data   |
| - Prepared Statements   |
| - Master Transaction Mgr|
+-------------------------+
        |
        +-----------------------------------+-----------------------------------+
        |                                   |                                   |
        v                                   v                                   v
+-------------------+               +-------------------+               +-------------------+
| MySQL 8 Database  |               | Local Storage     |               | External Services |
| Primary Source of |               | - Encrypted Files |               | - Google OAuth2   |
| Financial & System|               | - E-book chunks   |               | - Google Sheets   |
| Truth             |               | - Protected Media |               |   API (Export)    |
+-------------------+               +-------------------+               +-------------------+
```

### Key Architectural Tenets:
1. **Separation of Concerns:** Strict MVC architecture without bloat.
2. **Deterministic Routing:** URL pattern `/{lang}/{module}/{slug?}` with canonical resolution.
3. **Data Integrity & Ledger Immutability:** Financial ledger entries (`donations`, `expenses`, `financial_transactions`) are append-only. Corrections require adjusting contra-entries with explicit audit log references.
4. **Google Sheets Asymmetry:** Google Sheets is exclusively an administrative export and collaborative reporting sink. It is *never* an upstream database authority.
5. **No Direct Public E-Book Access:** All digital book content and reading streams pass through the `LibraryAccessController` with entitlement verification.

---

## 3. Database ER Structure (Relational Schema)

### 3.1 Authentication, RBAC & Memberships
- `users`: `id`, `uuid`, `google_id`, `email`, `name`, `avatar_url`, `status` (active, suspended, pending), `preferred_locale` (bn/en), `created_at`, `updated_at`.
- `roles`: `id`, `slug` (visitor, member, volunteer, content_editor, project_manager, finance_officer, admin, super_admin), `name_bn`, `name_en`, `description`.
- `permissions`: `id`, `slug` (e.g. `blog.moderate`, `finance.post`, `library.manage`), `description`.
- `role_permissions`: `role_id`, `permission_id`.
- `user_roles`: `user_id`, `role_id`, `assigned_by`, `assigned_at`.
- `member_profiles`: `id`, `user_id`, `member_code` (unique, e.g. `SPS-M-2026-0042`), `phone`, `occupation`, `education`, `address`, `bio_bn`, `bio_en`, `interests`, `is_public_profile`, `status` (applied, under_review, approved, rejected), `approved_at`, `approved_by`.
- `volunteers`: `id`, `member_id`, `skills` (JSON), `areas_of_interest` (education, food, medical, media, etc.), `availability`, `status` (active, paused).

### 3.2 Multilingual Content & Editorial CMS
- `pages`: `id`, `slug`, `template`, `is_published`, `created_at`, `updated_at`.
- `page_translations`: `id`, `page_id`, `language` (bn, en), `title`, `content_html`, `meta_title`, `meta_description`. (Unique: `page_id + language`)
- `posts` (Member Blog): `id`, `author_id`, `category_id`, `cover_image_url`, `reading_time_min`, `views_count`, `likes_count`, `created_at`, `updated_at`.
- `post_translations`: `id`, `post_id`, `language` (bn, en), `title`, `slug`, `excerpt`, `content_html`, `status` (draft, submitted, changes_requested, approved, published, archived), `rejection_notes`, `published_at`, `meta_title`, `meta_description`. (Unique: `post_id + language`, Unique: `language + slug`)
- `post_categories`: `id`, `slug`, `name_bn`, `name_en`, `sort_order`.
- `post_likes`: `id`, `post_id`, `user_id`, `created_at`. (Unique: `post_id + user_id`)
- `comments`: `id`, `post_id`, `user_id`, `parent_id` (nullable, max 1 level), `content`, `status` (pending, approved, spam, deleted), `created_at`, `updated_at`.

### 3.3 Scriptures & Knowledge Architecture
- `scriptures`: `id`, `slug`, `category` (sruti, smriti, upanishad, gita, darshana), `created_at`.
- `scripture_translations`: `id`, `scripture_id`, `language`, `title`, `introduction`.
- `scripture_books`: `id`, `scripture_id`, `book_number`, `slug`.
- `scripture_chapters`: `id`, `book_id`, `chapter_number`, `verse_count`.
- `scripture_verses`: `id`, `chapter_id`, `verse_number`, `sanskrit_devanagari`, `sanskrit_bengali_translit`, `sanskrit_iast`.
- `verse_translations`: `id`, `verse_id`, `language` (bn, en), `translation`, `commentary_exegesis`, `scholarly_references`. (Unique: `verse_id + language`)

### 3.4 Library, E-Books & Physical Store
- `books`: `id`, `isbn`, `publication_year`, `edition`, `pages_count`, `cover_image_url`, `is_physical_available`, `physical_price`, `stock_quantity`, `reserved_quantity`, `is_digital_available`, `digital_access_type` (public, registered, members, purchased), `copyright_status` (public_domain, sps_original, licensed, permission_granted), `created_at`, `updated_at`.
- `book_translations`: `id`, `book_id`, `language` (bn, en), `title`, `subtitle`, `slug`, `author`, `translator`, `publisher`, `description`, `toc_json`.
- `book_files`: `id`, `book_id`, `file_format` (epub, pdf, html_manifest), `storage_path` (private non-webroot), `file_hash`, `file_size_bytes`.
- `reading_progress`: `id`, `user_id`, `book_id`, `last_chapter_verse`, `progress_percent`, `last_read_at`. (Unique: `user_id + book_id`)
- `bookmarks`: `id`, `user_id`, `book_id`, `cfi_or_location`, `note`, `created_at`.
- `orders`: `id`, `order_number` (e.g. `SPS-ORD-2026-00052`), `user_id`, `total_amount`, `currency` (BDT/INR/USD), `payment_method` (bank_transfer, bkash, cod), `payment_status` (unpaid, verified, refunded), `shipping_status` (pending, confirmed, processing, packed, shipped, delivered, returned, cancelled), `shipping_address` (JSON), `tracking_number`, `created_at`.
- `order_items`: `id`, `order_id`, `book_id`, `quantity`, `unit_price`, `subtotal`.
- `inventory_logs`: `id`, `book_id`, `change_type` (purchase_stock, order_reserve, order_deduct, return, manual_adjustment), `delta_quantity`, `balance_after`, `reference_id`, `created_by`, `created_at`.

### 3.5 Projects, Activities & Updates
- `projects`: `id`, `slug`, `category_id`, `status` (planned, ongoing, completed, cancelled), `start_date`, `end_date`, `budget`, `beneficiaries_count`, `featured_image`, `created_at`.
- `project_translations`: `id`, `project_id`, `language`, `title`, `description_html`, `objectives_html`, `location_name`.
- `project_updates`: `id`, `project_id`, `published_at`, `author_id`.
- `project_update_translations`: `id`, `project_update_id`, `language`, `title`, `content_html`, `slug`.

### 3.6 Finance, Transparency & Audit Trails
- `donations`: `id`, `transaction_code` (e.g. `SPS-TXN-2026-000184`), `user_id` (nullable for anonymous), `donor_display_name` (or "Anonymous"), `donor_private_name`, `donor_email_hash` (for private verification), `amount`, `currency`, `fund_category` (general, education, food, medical, project_specific), `project_id` (nullable), `payment_channel`, `payment_reference`, `verification_status` (pending, verified, rejected), `verified_by`, `verified_at`, `is_public_acknowledged`, `created_at`.
- `expenses`: `id`, `voucher_code` (e.g. `SPS-EXP-2026-000091`), `project_id` (nullable), `category_id`, `amount`, `currency`, `expense_date`, `description`, `receipt_file_path`, `entered_by`, `approved_by`, `status` (entered, approved, voided_contra), `created_at`.
- `financial_adjustments`: `id`, `original_table` (donations/expenses), `original_id`, `reversal_amount`, `reason`, `authorized_by`, `created_at`.
- `audit_logs`: `id`, `actor_id`, `action` (e.g. `member.approve`, `expense.create`, `blog.publish`), `entity_type`, `entity_id`, `ip_address`, `user_agent`, `payload_before` (JSON), `payload_after` (JSON), `created_at`.
- `google_sheets_sync_logs`: `id`, `sheet_type` (donations, expenses, inventory), `sync_status` (success, failed), `records_synced`, `error_message`, `synced_at`.

---

## 4. Route Architecture (`/bn/` & `/en/`)

All public routes have an explicit language prefix. The router resolves `/{lang}/...`. If a visitor hits `/`, the application inspects the preferred cookie/session; if none, it issues a 302 redirect to `/bn/`.

| Canonical Route (`/bn/` & `/en/`) | Purpose & View |
|---|---|
| `/{lang}/` | Institutional Editorial Homepage |
| `/{lang}/about` | About SPS, Mission, Vision, Values, Leadership |
| `/{lang}/activities` | Comprehensive Activities & Field Works Directory |
| `/{lang}/activities/{slug}` | Activity/Project Detail & Impact Log |
| `/{lang}/knowledge` | Curated Sanatan Philosophy & Exegesis Portal |
| `/{lang}/scriptures` | Canonical Scripture Tree Directory |
| `/{lang}/scriptures/{book}/{chapter}` | Scripture Reader (Sanskrit + Translations + Commentary) |
| `/{lang}/library` | Digital & Scholarly Physical Library Catalog |
| `/{lang}/library/book/{slug}` | Book Detail, Preview & Order Gateway |
| `/{lang}/library/reader/{book_id}` | Protected Online E-Book Reader Engine |
| `/{lang}/blog` | Community Member Blog Feed |
| `/{lang}/blog/{slug}` | Blog Post Detail with Author Card, Likes, Comments, Share |
| `/{lang}/author/{id}` | Verified Member Author Profile & Published Essays |
| `/{lang}/get-involved` | Membership Application & Volunteer Registration |
| `/{lang}/transparency` | Financial Reports, Project-wise Ledger & Aggregations |
| `/{lang}/contact` | Institutional Contact, Office Locations & Inquiries |
| `/{lang}/search` | Multilingual Search (Pages, Books, Scriptures, Posts) |
| `/{lang}/cart` & `/{lang}/checkout` | Physical Book Order Cart & Checkout Pipeline |
| `/{lang}/auth/google` | Google OAuth 2.0 Entry Redirect |
| `/{lang}/auth/callback` | OAuth Callback Handler & Session Provisioning |
| `/{lang}/auth/logout` | Session Destruction & CSRF Invalidation |

---

## 5. Design System: Editorial & Scholarly Bengali Aesthetic

### 5.1 Design Philosophy
"Ancient knowledge presented through contemporary editorial design."
- **Human-Curated & Architectural:** Generous whitespace, refined hairline borders (`1px solid var(--border-subtle)`), classic book margins, balanced asymmetric layouts.
- **Dignified Palettes:** No screaming neon saffron; instead, warm ivory backgrounds, deep charcoal ink tones, and restrained touches of antique gold and terracota/muted saffron.

### 5.2 Color Tokens
```css
:root {
  /* Surfaces & Backgrounds */
  --bg-primary: #FAF8F5;       /* Warm Ivory canvas */
  --bg-secondary: #F3EFEA;     /* Soft parchment container */
  --bg-card: #FFFFFF;          /* Pure editorial white card */
  --bg-card-subtle: #FAF6F0;   /* Muted accent card */
  --bg-elevated: #FFFFFF;      /* Modals and popovers */
  --bg-dark-editorial: #1F2421;/* Deep charcoal for hero banners/footers */

  /* Ink & Typography */
  --text-primary: #1C201D;     /* Deep Charcoal ink */
  --text-secondary: #4A524D;   /* Scholarly slate/charcoal */
  --text-muted: #747E77;       /* Muted citation/caption text */
  --text-on-dark: #F7F5F0;     /* Parchment white on charcoal */
  --text-on-dark-muted: #B8BFBA;

  /* Restrained Accents */
  --accent-saffron: #C65A1E;   /* Muted temple terracotta / saffron */
  --accent-saffron-hover: #A84915;
  --accent-saffron-subtle: #FDF3EB;
  
  --accent-gold: #A37E36;      /* Antique manuscript gold */
  --accent-gold-light: #CBB079;
  --accent-gold-subtle: #F9F5EC;

  --accent-brown: #5C4334;     /* Sandalwood / warm earth */

  /* Borders & Dividers */
  --border-subtle: #E8E2D8;    /* Hairline parchment border */
  --border-medium: #D5CCC0;    /* Card and table border */
  --border-focus: #A37E36;     /* Accessible focus indicator */

  /* Semantic Feedback */
  --status-success: #2E6B4F;   /* Forest leaf green */
  --status-success-bg: #EDF7F2;
  --status-warning: #B37418;   /* Ochre */
  --status-warning-bg: #FEF8ED;
  --status-danger: #9E332B;    /* Madder crimson */
  --status-danger-bg: #FDF2F1;
  --status-info: #355E75;      /* River blue */
  --status-info-bg: #EEF5F9;
}
```

### 5.3 Typography Matrix
- **Bengali Headings & Long-Form Reading:** `'Noto Serif Bengali', 'SolaimanLipi', Georgia, serif`
- **Bengali Interface & Data:** `'Noto Sans Bengali', system-ui, sans-serif`
- **English Headings & Monograms:** `'Cinzel', 'Noto Serif', Georgia, serif`
- **English Long-Form Reading:** `'Noto Serif', 'Source Serif Pro', Georgia, serif`
- **English Interface:** `'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif`
- **Sanskrit Verses:** `'Noto Serif Devanagari', 'Noto Serif Bengali', serif`
