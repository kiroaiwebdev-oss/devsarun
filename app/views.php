<?php
declare(strict_types=1);

/* Public layout + reusable UI components. */

function brand_logo_url(): string
{
    $p = setting('logo_path');
    return $p !== '' ? url($p) : asset('img/logo.svg');
}

function brand_favicon_url(): string
{
    $p = setting('favicon_path');
    return $p !== '' ? url($p) : asset('img/favicon.svg');
}

function nav_items(): array
{
    return [
        'services' => ['Services', 'services/'],
        'work' => ['Work', 'work/'],
        'pricing' => ['Pricing', 'pricing/'],
        'aeo-geo' => ['AEO / GEO', 'aeo-geo/'],
        'about' => ['About', 'about/'],
        'contact' => ['Contact', 'contact/'],
    ];
}

/**
 * $meta: title, description, active, canonical, schema (array), noindex, body_class
 */
function page_start(array $meta = []): void
{
    track_pageview();
    $site = setting('site_name', 'Devs Arun');
    $title = isset($meta['title']) ? $meta['title'] . ' — ' . $site : setting('seo_title', $site);
    $desc = $meta['description'] ?? setting('seo_description');
    $canonical = $meta['canonical'] ?? abs_url(current_path());
    $active = $meta['active'] ?? '';
    $schema = $meta['schema'] ?? null;
    $org = [
        '@context' => 'https://schema.org', '@type' => 'ProfessionalService', 'name' => $site,
        'url' => abs_url(''), 'email' => setting('contact_email'), 'telephone' => setting('whatsapp'),
        'address' => ['@type' => 'PostalAddress', 'addressLocality' => setting('location', 'India')],
        'priceRange' => '₹₹', 'sameAs' => array_values(array_filter([setting('github_url'), setting('instagram_url'), setting('twitter_url'), setting('threads_url')])),
    ];
    ?><!doctype html>
<html lang="en" class="scroll-smooth">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title><?= e($title) ?></title>
<meta name="description" content="<?= e($desc) ?>">
<link rel="canonical" href="<?= e($canonical) ?>">
<?php if (!empty($meta['noindex'])): ?><meta name="robots" content="noindex, nofollow"><?php endif; ?>
<meta name="theme-color" content="#0A0A0A">
<meta name="csrf-token" content="<?= e(csrf_token()) ?>">
<meta property="og:type" content="website">
<meta property="og:site_name" content="<?= e($site) ?>">
<meta property="og:title" content="<?= e($title) ?>">
<meta property="og:description" content="<?= e($desc) ?>">
<meta property="og:url" content="<?= e($canonical) ?>">
<meta property="og:image" content="<?= e(abs_url('assets/img/og.svg')) ?>">
<meta name="twitter:card" content="summary_large_image">
<link rel="icon" href="<?= e(brand_favicon_url()) ?>">
<link rel="apple-touch-icon" href="<?= e(brand_favicon_url()) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>">
<script type="application/ld+json"><?= json_encode($org, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) ?></script>
<?php if ($schema): ?><script type="application/ld+json"><?= json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) ?></script><?php endif; ?>
</head>
<body class="bg-neo-black text-white font-sans antialiased selection:bg-neo-green/30 <?= e($meta['body_class'] ?? '') ?>" data-base="<?= e(BASE_PATH) ?>">
<a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-[100] focus:rounded-full focus:bg-neo-green focus:px-4 focus:py-2 focus:text-neo-black">Skip to content</a>

<header class="fixed inset-x-0 top-0 z-50">
  <div class="mx-auto mt-3 max-w-6xl px-3 sm:px-4">
    <nav class="flex h-16 items-center justify-between rounded-full border border-white/[0.07] bg-neo-black/70 pl-3 pr-2 backdrop-blur-xl sm:pl-4" aria-label="Main">
      <a href="<?= e(url('')) ?>" class="flex items-center gap-2.5" aria-label="<?= e($site) ?> home">
        <img src="<?= e(brand_logo_url()) ?>" alt="" class="h-9 w-9 rounded-xl" width="36" height="36">
        <span class="text-[15px] font-bold tracking-tight"><?= e($site) ?></span>
      </a>
      <div class="hidden items-center gap-1 lg:flex">
        <?php foreach (nav_items() as $key => [$label, $href]): ?>
          <a href="<?= e(url($href)) ?>" class="rounded-full px-3.5 py-2 text-sm transition <?= $active === $key ? 'bg-white/[0.06] text-white' : 'text-white/60 hover:text-white' ?>"><?= e($label) ?></a>
        <?php endforeach; ?>
      </div>
      <div class="flex items-center gap-2">
        <a href="<?= e(url('start-project/')) ?>" data-track="cta_click" data-label="nav_start" class="hidden rounded-full bg-neo-green px-5 py-2.5 text-sm font-semibold text-neo-black transition hover:bg-neo-green/90 sm:inline-flex">Start a project</a>
        <button type="button" class="inline-flex h-11 w-11 items-center justify-center rounded-full text-white/80 hover:bg-white/5 lg:hidden" data-menu-open aria-label="Open menu" aria-expanded="false" aria-controls="mobile-menu"><?= icon('menu', 'h-6 w-6') ?></button>
      </div>
    </nav>
  </div>
</header>

<div id="mobile-menu" class="fixed inset-0 z-[60] hidden" role="dialog" aria-modal="true" aria-label="Menu">
  <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" data-menu-close></div>
  <div class="absolute inset-x-3 top-3 rounded-[28px] border border-white/10 bg-neo-black p-4 shadow-2xl">
    <div class="mb-2 flex items-center justify-between pl-2">
      <span class="font-mono text-xs uppercase tracking-[.2em] text-neo-green">// menu</span>
      <button type="button" class="inline-flex h-11 w-11 items-center justify-center rounded-full hover:bg-white/5" data-menu-close aria-label="Close menu"><?= icon('x', 'h-6 w-6') ?></button>
    </div>
    <div class="grid gap-1">
      <?php foreach (nav_items() as $key => [$label, $href]): ?>
        <a href="<?= e(url($href)) ?>" class="rounded-2xl px-4 py-3.5 text-base <?= $active === $key ? 'bg-white/[0.06] text-white' : 'text-white/70' ?>"><?= e($label) ?></a>
      <?php endforeach; ?>
      <a href="<?= e(url('track/')) ?>" class="rounded-2xl px-4 py-3.5 text-base text-white/70">Track my request</a>
      <a href="<?= e(url('start-project/')) ?>" class="mt-2 rounded-full bg-neo-green px-5 py-3.5 text-center font-semibold text-neo-black">Start a project</a>
    </div>
  </div>
</div>

<main id="main">
<?php
}

function page_end(): void
{
    $site = setting('site_name', 'Devs Arun');
    $wa = preg_replace('/\D+/', '', setting('whatsapp'));
    ?>
</main>

<footer class="relative mt-24 border-t border-white/[0.06]">
  <div class="mx-auto grid max-w-6xl gap-10 px-5 py-16 sm:px-6 md:grid-cols-12">
    <div class="md:col-span-4">
      <a href="<?= e(url('')) ?>" class="flex items-center gap-2.5"><img src="<?= e(brand_logo_url()) ?>" alt="" class="h-10 w-10 rounded-xl" width="40" height="40"><span class="text-lg font-bold"><?= e($site) ?></span></a>
      <p class="mt-4 max-w-xs text-sm leading-relaxed text-white/50"><?= e(setting('site_tagline')) ?>. Honest pricing — every price is negotiable.</p>
      <div class="mt-5 flex gap-2">
        <?php foreach (['github_url' => 'github', 'instagram_url' => 'instagram', 'twitter_url' => 'twitter', 'threads_url' => 'threads'] as $k => $ic): if (setting($k)): ?>
          <a href="<?= e(setting($k)) ?>" target="_blank" rel="noopener" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/10 text-white/60 transition hover:border-neo-green/40 hover:text-neo-green" aria-label="<?= e(ucfirst($ic)) ?>"><?= icon($ic, 'h-4 w-4') ?></a>
        <?php endif; endforeach; ?>
      </div>
    </div>
    <div class="grid grid-cols-2 gap-8 sm:grid-cols-3 md:col-span-8">
      <div>
        <p class="font-mono text-xs uppercase tracking-[.2em] text-white/40">Services</p>
        <ul class="mt-4 space-y-2.5 text-sm text-white/60">
          <?php foreach (array_slice(services_active(), 0, 7) as $s): ?><li><a class="hover:text-white" href="<?= e(url('services/' . $s['slug'] . '/')) ?>"><?= e($s['title']) ?></a></li><?php endforeach; ?>
        </ul>
      </div>
      <div>
        <p class="font-mono text-xs uppercase tracking-[.2em] text-white/40">Company</p>
        <ul class="mt-4 space-y-2.5 text-sm text-white/60">
          <li><a class="hover:text-white" href="<?= e(url('work/')) ?>">Work</a></li>
          <li><a class="hover:text-white" href="<?= e(url('about/')) ?>">About</a></li>
          <li><a class="hover:text-white" href="<?= e(url('pricing/')) ?>">Pricing</a></li>
          <li><a class="hover:text-white" href="<?= e(url('aeo-geo/')) ?>">AEO / GEO</a></li>
          <li><a class="hover:text-white" href="<?= e(url('start-project/')) ?>">Start a project</a></li>
          <li><a class="hover:text-white" href="<?= e(url('track/')) ?>">Track request</a></li>
        </ul>
      </div>
      <div>
        <p class="font-mono text-xs uppercase tracking-[.2em] text-white/40">Contact</p>
        <ul class="mt-4 space-y-2.5 text-sm text-white/60">
          <li><a class="break-all hover:text-white" href="mailto:<?= e(setting('contact_email')) ?>"><?= e(setting('contact_email')) ?></a></li>
          <?php if ($wa): ?><li><a class="hover:text-white" href="https://wa.me/<?= e($wa) ?>" target="_blank" rel="noopener" data-track="whatsapp_click" data-label="footer">WhatsApp <?= e(setting('whatsapp')) ?></a></li><?php endif; ?>
          <li><?= e(setting('location')) ?></li>
          <li class="pt-2"><a class="hover:text-white" href="<?= e(url('privacy/')) ?>">Privacy</a> · <a class="hover:text-white" href="<?= e(url('terms/')) ?>">Terms</a> · <a class="hover:text-white" href="<?= e(url('refund-policy/')) ?>">Refunds</a></li>
        </ul>
      </div>
    </div>
  </div>
  <div class="border-t border-white/[0.06]">
    <div class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-2 px-5 py-6 text-xs text-white/40 sm:flex-row sm:px-6">
      <p>© <?= date('Y') ?> <?= e($site) ?>. All prices in INR · always negotiable.</p>
      <p class="font-mono"><span class="text-neo-green">●</span> <?= e(setting('availability', 'Accepting new projects')) ?></p>
    </div>
  </div>
</footer>

<?php if ($wa): ?>
<a href="https://wa.me/<?= e($wa) ?>?text=<?= rawurlencode('Hi Arun, I found you on your website and want to discuss a project.') ?>" target="_blank" rel="noopener" data-track="whatsapp_click" data-label="floating" class="fixed bottom-5 left-5 z-40 inline-flex h-12 w-12 items-center justify-center rounded-full bg-[#25D366] text-white shadow-lg shadow-black/40 transition hover:scale-105" aria-label="Chat on WhatsApp"><?= icon('whatsapp', 'h-6 w-6') ?></a>
<?php endif; ?>

<!-- AI assistant -->
<div id="ai-chat" class="fixed bottom-5 right-5 z-50" data-greeting="<?= e(setting('ai_greeting')) ?>">
  <div id="ai-window" class="absolute bottom-16 right-0 hidden w-[min(92vw,380px)] flex-col overflow-hidden rounded-[28px] border border-white/10 bg-[#0d0d0d] shadow-2xl shadow-black/60" role="dialog" aria-label="AI assistant">
    <div class="flex items-center justify-between border-b border-white/[0.06] px-4 py-3.5">
      <div class="flex items-center gap-3">
        <div class="relative flex h-10 w-10 items-center justify-center rounded-2xl bg-neo-green/10 text-neo-green"><?= icon('bot', 'h-5 w-5') ?><span class="absolute -bottom-0.5 -right-0.5 h-3 w-3 rounded-full border-2 border-[#0d0d0d] bg-neo-green"></span></div>
        <div><p class="text-sm font-semibold">Arun's AI assistant</p><p class="text-xs text-white/50">Online · prices are negotiable</p></div>
      </div>
      <button type="button" id="ai-close" class="inline-flex h-9 w-9 items-center justify-center rounded-full text-white/60 hover:bg-white/5" aria-label="Close chat"><?= icon('x', 'h-5 w-5') ?></button>
    </div>
    <div id="ai-messages" class="chat-scroll flex h-[min(60vh,440px)] flex-col gap-3 overflow-y-auto px-4 py-4" aria-live="polite"></div>
    <div id="ai-chips" class="flex gap-2 overflow-x-auto px-4 pb-2"></div>
    <form id="ai-form" class="flex items-center gap-2 border-t border-white/[0.06] p-3">
      <input id="ai-input" type="text" maxlength="800" autocomplete="off" placeholder="Ask anything… (English / Hindi)" class="min-w-0 flex-1 rounded-full border border-white/10 bg-white/[0.03] px-4 py-3 text-sm placeholder:text-white/30 focus:border-neo-green/40 focus:outline-none" aria-label="Message">
      <button type="submit" class="inline-flex h-11 w-11 flex-none items-center justify-center rounded-full bg-neo-green text-neo-black transition hover:bg-neo-green/90" aria-label="Send"><?= icon('send', 'h-4 w-4') ?></button>
    </form>
  </div>
  <button type="button" id="ai-toggle" class="relative inline-flex h-14 w-14 items-center justify-center rounded-full bg-neo-green text-neo-black shadow-lg shadow-neo-green/20 transition hover:scale-105" aria-label="Open AI assistant" aria-expanded="false">
    <?= icon('chat', 'h-6 w-6') ?>
    <span id="ai-badge" class="absolute -right-0.5 -top-0.5 flex h-5 w-5 items-center justify-center rounded-full bg-white text-[10px] font-bold text-neo-black">1</span>
  </button>
</div>

<div id="toast" class="pointer-events-none fixed left-1/2 top-24 z-[90] -translate-x-1/2 opacity-0 transition"></div>
<script src="<?= e(asset('js/app.js')) ?>" defer></script>
<?php if (!empty($GLOBALS['PAGE_SCRIPTS'])): foreach ($GLOBALS['PAGE_SCRIPTS'] as $src): ?>
<script src="<?= e($src) ?>" defer></script>
<?php endforeach; endif; ?>
</body>
</html>
<?php
}

function page_script(string $src): void
{
    $GLOBALS['PAGE_SCRIPTS'][] = $src;
}

/* ---------------- Components ---------------- */

function section_head(string $kicker, string $title, string $sub = '', string $align = 'center'): void
{
    $a = $align === 'center' ? 'mx-auto text-center' : '';
    echo '<div class="' . $a . ' max-w-2xl">'
        . '<p class="font-mono text-xs uppercase tracking-[.25em] text-neo-green">// ' . e($kicker) . '</p>'
        . '<h2 class="mt-3 text-3xl font-bold tracking-tight sm:text-4xl">' . $title . '</h2>'
        . ($sub ? '<p class="mt-4 text-base leading-relaxed text-white/60 sm:text-lg">' . e($sub) . '</p>' : '')
        . '</div>';
}

function service_card(array $s): void
{
    $features = json_list($s['features']);
    ?>
    <a href="<?= e(url('services/' . $s['slug'] . '/')) ?>" class="card group relative flex flex-col p-6 sm:p-7">
      <?php if ((int)$s['is_featured']): ?><span class="absolute right-5 top-5 rounded-full bg-neo-green/10 px-2.5 py-1 font-mono text-[10px] uppercase tracking-wider text-neo-green">Popular</span><?php endif; ?>
      <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-neo-green/10 text-neo-green transition group-hover:scale-105"><?= icon($s['icon'], 'h-6 w-6') ?></div>
      <h3 class="mt-5 text-lg font-semibold tracking-tight"><?= e($s['title']) ?></h3>
      <p class="mt-2 text-sm leading-relaxed text-white/55"><?= e($s['summary']) ?></p>
      <ul class="mt-5 space-y-2 text-sm text-white/60">
        <?php foreach (array_slice($features, 0, 3) as $f): ?><li class="flex gap-2"><span class="mt-0.5 text-neo-green"><?= icon('check', 'h-4 w-4') ?></span><?= e($f) ?></li><?php endforeach; ?>
      </ul>
      <div class="mt-auto flex items-end justify-between pt-6">
        <div>
          <p class="font-mono text-[10px] uppercase tracking-widest text-white/35"><?= $s['price_from'] ? 'Starting from' : 'Pricing' ?></p>
          <p class="mt-1 text-lg font-bold text-neo-green"><?= e(price_text($s)) ?></p>
        </div>
        <span class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/10 text-white/60 transition group-hover:border-neo-green/40 group-hover:bg-neo-green group-hover:text-neo-black"><?= icon('arrow', 'h-4 w-4') ?></span>
      </div>
    </a>
    <?php
}

function project_card(array $p): void
{
    $tags = json_list($p['tags']);
    $label = PROJECT_TYPES[$p['type']] ?? 'Project';
    $cta = match ($p['type']) { 'android' => 'Google Play', 'amazon' => 'Amazon Appstore', 'extension' => 'Chrome Web Store', default => 'Visit website' };
    ?>
    <article class="card flex flex-col p-6" data-type="<?= e($p['type']) ?>">
      <div class="flex items-start justify-between gap-3">
        <div class="flex h-12 w-12 flex-none items-center justify-center rounded-2xl bg-white/[0.04] text-neo-green ring-1 ring-white/[0.06]"><?= icon($p['icon'] ?: 'globe', 'h-6 w-6') ?></div>
        <?php if ($p['metric']): ?><span class="rounded-full border border-neo-green/20 bg-neo-green/[0.07] px-2.5 py-1 text-[11px] font-medium text-neo-green"><?= e($p['metric']) ?></span><?php endif; ?>
      </div>
      <p class="mt-5 font-mono text-[11px] uppercase tracking-widest text-white/35"><?= e($p['subtitle'] ?: $label) ?></p>
      <h3 class="mt-1.5 text-lg font-semibold tracking-tight"><?= e($p['title']) ?></h3>
      <p class="mt-2 text-sm leading-relaxed text-white/55"><?= e($p['description']) ?></p>
      <div class="mt-4 flex flex-wrap gap-1.5">
        <?php foreach (array_slice($tags, 0, 4) as $t): ?><span class="rounded-full bg-white/[0.04] px-2.5 py-1 text-[11px] text-white/55"><?= e($t) ?></span><?php endforeach; ?>
      </div>
      <div class="mt-auto pt-6">
        <?php if ($p['url']): ?>
          <a href="<?= e($p['url']) ?>" target="_blank" rel="noopener" data-track="outbound_click" data-label="<?= e($p['title']) ?>" class="inline-flex items-center gap-1.5 text-sm font-medium text-neo-green hover:text-white"><?= e($cta) ?> <?= icon('external', 'h-3.5 w-3.5') ?></a>
        <?php else: ?>
          <span class="text-sm text-white/35">Private / coming soon</span>
        <?php endif; ?>
      </div>
    </article>
    <?php
}

function cta_band(string $title = 'Have a project in mind?', string $sub = 'Share your idea in a 2-minute brief and get an honest, negotiable quote within 24 hours.'): void
{
    ?>
    <section class="mx-auto mt-24 max-w-6xl px-5 sm:px-6">
      <div class="relative overflow-hidden rounded-[32px] border border-neo-green/15 bg-gradient-to-br from-neo-green/[0.08] via-white/[0.02] to-transparent px-6 py-14 text-center sm:px-12">
        <div class="pointer-events-none absolute -top-24 left-1/2 h-64 w-[36rem] -translate-x-1/2 rounded-full bg-neo-green/10 blur-3xl"></div>
        <h2 class="relative text-3xl font-bold tracking-tight sm:text-4xl"><?= e($title) ?></h2>
        <p class="relative mx-auto mt-4 max-w-xl text-white/60"><?= e($sub) ?></p>
        <div class="relative mt-8 flex flex-col justify-center gap-3 sm:flex-row">
          <a href="<?= e(url('start-project/')) ?>" data-track="cta_click" data-label="band_start" class="btn-primary">Start a project <?= icon('arrow', 'h-4 w-4') ?></a>
          <a href="<?= e(url('services/free-consultation/')) ?>" class="btn-ghost">Book a free call</a>
        </div>
      </div>
    </section>
    <?php
}
