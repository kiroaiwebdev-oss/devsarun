# Devs Arun — v2 (portfolio + CRM + admin)

PHP 8.1+ · MySQL 5.7+/MariaDB 10.4+ · Tailwind CSS · vanilla JS. No frameworks, no Composer needed.

## What's inside

**Public site** (clean URLs, every page is a folder): `/`, `/services/`, `/services/{slug}/`, `/work/`, `/pricing/`, `/aeo-geo/`, `/about/`, `/contact/`, `/start-project/`, `/track/`, `/checkout/`, `/pay/{token}/`, `/receipt/`, `/brochure/{slug}/`, `/privacy/`, `/terms/`, `/refund-policy/`, `/sitemap.xml`.

- Every service is a product page with deliverables, process, FAQs, price (always "negotiable") and a **downloadable PDF brochure** in the same slide style as the AEO/GEO deck.
- 4-step **project brief** form (service → details + file → budget → contact) → reference ID, confirmation email, admin alert.
- **AI assistant** trained on live services/prices + your own knowledge text. Works offline (rules engine) or with Gemini / OpenAI / Groq. Captures name/email/phone into the CRM.
- **Razorpay**: book selected services online, or pay negotiated quotes via secure links. Signature-verified + webhook.
- Client **tracking page** with status timeline and public updates.
- First-party analytics (no IPs stored), UTM / referrer attribution on every lead.

**Admin** (`/admin/`): dashboard & funnel, leads CRM (status, priority, value, follow-ups, notes, client-visible updates, email, quotes, CSV export, attachments), AI chat transcripts, payments, quotes/payment links, email center (SMTP, templates, segment emails, log), analytics, services/portfolio/testimonials editors, settings (SMTP, Razorpay, AI, branding — upload your own logo/favicon), team roles (owner/manager/viewer), audit log.

**Security**: PDO prepared statements, CSRF on every form/API, bcrypt passwords, login lockout, session hardening, rate limits, honeypots, CSP & security headers, secrets encrypted (AES-256-GCM), uploads type-checked and stored outside the web root, brand images re-encoded, private folders blocked by `.htaccess`.

## Deploy (Hostinger / cPanel)

1. Upload everything to `public_html` (keep the `.htaccess` files).
2. Create an empty MySQL database + user.
3. Open `https://yourdomain.com/install/` and fill the form once. It creates tables, seeds all services & projects, your owner account and `app/config.php`, then locks itself.
4. Log in at `/admin/` → **Settings**: add SMTP, Razorpay keys + webhook secret (webhook URL is shown there), and an AI key (optional).
5. Turn on HTTPS and uncomment the HTTPS redirect in `.htaccess`.

Sub-folder installs: set `RewriteBase` in `.htaccess`. Nginx: route `/services/{slug}/`, `/brochure/{slug}/`, `/pay/{token}/`, `/sitemap.xml` the same way `dev-router.php` does and deny `app/ database/ storage/`.

## Local development

```bash
npm install && npm run build:css      # rebuild CSS after changing classes
php -S localhost:8000 dev-router.php  # mirrors .htaccess routing
```
