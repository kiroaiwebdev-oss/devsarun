<?php
declare(strict_types=1);
require dirname(__DIR__) . '/app/bootstrap.php';

$slug = preg_replace('/[^a-z0-9-]/', '', (string)($_GET['slug'] ?? ''));

if ($slug !== '') {
    $s = service_by_slug($slug);
    if (!$s) {
        http_response_code(404);
        require ROOT . '/404.php';
        exit;
    }
    track_event('service_view', $s['slug'], (int)$s['id']);
    $deliverables = json_list($s['deliverables']);
    $process = json_list($s['process']);
    $faqs = json_list($s['faqs']);
    $ideal = json_list($s['ideal_for']);
    $notIncluded = json_list($s['not_included']);
    $features = json_list($s['features']);
    $related = array_slice(array_values(array_filter(services_active(), static fn($x) => $x['category'] === $s['category'] && $x['id'] !== $s['id'])), 0, 3);

    $schema = [
        '@context' => 'https://schema.org',
        '@graph' => array_values(array_filter([
            ['@type' => 'Service', 'name' => $s['title'], 'description' => $s['summary'], 'provider' => ['@type' => 'ProfessionalService', 'name' => setting('site_name')], 'areaServed' => 'Worldwide',
                'offers' => $s['price_from'] ? ['@type' => 'Offer', 'priceCurrency' => 'INR', 'price' => (int)$s['price_from'], 'description' => 'Starting price, negotiable'] : null],
            $faqs ? ['@type' => 'FAQPage', 'mainEntity' => array_map(static fn($f) => ['@type' => 'Question', 'name' => $f['q'], 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f['a']]], $faqs)] : null,
        ])),
    ];
    page_start(['title' => $s['title'], 'description' => $s['summary'], 'active' => 'services', 'schema' => $schema]);
    ?>
    <section class="relative overflow-hidden pt-32 sm:pt-40">
      <div class="grid-bg pointer-events-none absolute inset-0"></div>
      <div class="relative mx-auto max-w-6xl px-5 sm:px-6">
        <nav class="mb-8 flex items-center gap-2 text-sm text-white/40" aria-label="Breadcrumb"><a href="<?= e(url('services/')) ?>" class="hover:text-white">Services</a><span>/</span><span class="text-white/70"><?= e($s['title']) ?></span></nav>
        <div class="grid gap-10 lg:grid-cols-[1.3fr_.7fr]">
          <div>
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-neo-green/10 text-neo-green"><?= icon($s['icon'], 'h-7 w-7') ?></div>
            <p class="kicker mt-6"><?= e(SERVICE_CATEGORIES[$s['category']] ?? '') ?></p>
            <h1 class="mt-3 text-4xl font-extrabold tracking-tight sm:text-5xl"><?= e($s['title']) ?></h1>
            <p class="mt-3 text-lg text-neo-green/90"><?= e($s['tagline']) ?></p>
            <p class="mt-6 max-w-2xl text-lg leading-relaxed text-white/60"><?= nl2br(e($s['description'] ?: $s['summary'])) ?></p>
            <ul class="mt-8 grid gap-3 sm:grid-cols-2">
              <?php foreach ($features as $f): ?><li class="flex gap-3 text-white/75"><span class="mt-0.5 flex h-6 w-6 flex-none items-center justify-center rounded-full bg-neo-green/10 text-neo-green"><?= icon('check', 'h-3.5 w-3.5') ?></span><?= e($f) ?></li><?php endforeach; ?>
            </ul>
          </div>
          <aside class="card h-fit p-7 lg:sticky lg:top-28">
            <p class="font-mono text-[11px] uppercase tracking-widest text-white/40"><?= $s['price_from'] ? 'Starting from' : 'Pricing' ?></p>
            <p class="mt-2 text-4xl font-extrabold tracking-tight text-neo-green"><?= e(price_text($s)) ?></p>
            <?php if ($s['price_usd']): ?><p class="mt-1 text-sm text-white/50"><?= e($s['price_usd']) ?></p><?php endif; ?>
            <p class="mt-3 rounded-2xl bg-white/[0.04] px-4 py-3 text-sm text-white/60">🤝 <strong class="text-white">Negotiable.</strong> Final quote depends on your scope — nothing is fixed.</p>
            <?php if ($s['timeline']): ?><p class="mt-4 flex items-center gap-2 text-sm text-white/60"><?= icon('clock', 'h-4 w-4 text-neo-green') ?> Timeline: <span class="text-white"><?= e($s['timeline']) ?></span></p><?php endif; ?>
            <div class="mt-6 grid gap-2.5">
              <a href="<?= e(url('start-project/?service=' . $s['slug'])) ?>" class="btn-primary" data-track="cta_click" data-label="service_start:<?= e($s['slug']) ?>">Discuss this project <?= icon('arrow', 'h-4 w-4') ?></a>
              <a href="<?= e(url('brochure/' . $s['slug'] . '/')) ?>" class="btn-ghost" data-track="brochure_view" data-label="<?= e($s['slug']) ?>"><?= icon('download', 'h-4 w-4') ?> Brochure (PDF)</a>
              <?php if ((int)$s['is_buyable'] && (int)$s['buy_price'] > 0): ?>
                <a href="<?= e(url('checkout/?service=' . $s['slug'])) ?>" class="mt-1 text-center text-sm text-white/50 underline-offset-4 hover:text-neo-green hover:underline" data-track="checkout_open" data-label="<?= e($s['slug']) ?>"><?= icon('lock', 'mr-1 inline h-3.5 w-3.5') ?>Prefer to book online? <?= e($s['buy_label'] ?: 'Book now') ?> · <?= e(inr((int)$s['buy_price'])) ?></a>
              <?php endif; ?>
            </div>
          </aside>
        </div>
      </div>
    </section>

    <?php if ($ideal): ?>
    <section class="mx-auto mt-24 max-w-6xl px-5 sm:px-6">
      <?php section_head('ideal for', 'Who this is <span class="text-neo-green">for</span>', '', 'left'); ?>
      <div class="mt-8 flex flex-wrap gap-2.5"><?php foreach ($ideal as $i): ?><span class="rounded-full border border-white/10 bg-white/[0.03] px-4 py-2.5 text-sm text-white/75"><?= e($i) ?></span><?php endforeach; ?></div>
    </section>
    <?php endif; ?>

    <?php if ($deliverables): ?>
    <section class="mx-auto mt-24 max-w-6xl px-5 sm:px-6">
      <?php section_head('what you get', 'Deliverables, <span class="text-neo-green">clearly listed</span>', '', 'left'); ?>
      <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <?php foreach ($deliverables as $i => $d): ?>
          <div class="card reveal p-6"><span class="font-mono text-sm text-neo-green"><?= sprintf('%02d', $i + 1) ?></span><h3 class="mt-3 font-semibold"><?= e($d['title'] ?? '') ?></h3><p class="mt-2 text-sm leading-relaxed text-white/55"><?= e($d['desc'] ?? '') ?></p></div>
        <?php endforeach; ?>
      </div>
    </section>
    <?php endif; ?>

    <?php if ($process): ?>
    <section class="mx-auto mt-24 max-w-6xl px-5 sm:px-6">
      <?php section_head('process', 'A simple, <span class="text-neo-green">reviewable</span> process', '', 'left'); ?>
      <div class="card mt-10 overflow-x-auto p-0">
        <table class="w-full min-w-[640px] text-left text-sm">
          <thead><tr class="border-b border-white/[0.06] font-mono text-[11px] uppercase tracking-wider text-white/40"><th class="px-6 py-4">Stage</th><th class="px-6 py-4">The work</th><th class="px-6 py-4">Your input</th><th class="px-6 py-4">You receive</th></tr></thead>
          <tbody>
            <?php foreach ($process as $p): ?>
              <tr class="border-b border-white/[0.04] last:border-0"><td class="px-6 py-4 font-semibold text-white"><?= e($p['stage'] ?? '') ?></td><td class="px-6 py-4 text-white/65"><?= e($p['work'] ?? '') ?></td><td class="px-6 py-4 text-white/65"><?= e($p['input'] ?? '') ?></td><td class="px-6 py-4 text-neo-green/90"><?= e($p['receive'] ?? '') ?></td></tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>
    <?php endif; ?>

    <?php if ($notIncluded): ?>
    <section class="mx-auto mt-24 max-w-6xl px-5 sm:px-6">
      <div class="grid gap-4 md:grid-cols-2">
        <div class="card p-7"><p class="kicker">// clear scope</p><h3 class="mt-3 text-xl font-semibold">What I commit to</h3><p class="mt-3 text-sm leading-relaxed text-white/60">Reviewable work, agreed scope, timing and access, and honest communication at every step. Any extra work is approved by you first — no surprise scope expansion.</p></div>
        <div class="card p-7"><p class="font-mono text-xs uppercase tracking-[.25em] text-white/40">// not included</p><h3 class="mt-3 text-xl font-semibold">Outside this scope</h3><ul class="mt-3 space-y-2 text-sm text-white/60"><?php foreach ($notIncluded as $n): ?><li class="flex gap-2"><span class="text-white/30">—</span><?= e($n) ?></li><?php endforeach; ?></ul></div>
      </div>
    </section>
    <?php endif; ?>

    <?php if ($faqs): ?>
    <section class="mx-auto mt-24 max-w-3xl px-5 sm:px-6">
      <?php section_head('faq', 'Questions about <span class="text-neo-green">' . e($s['title']) . '</span>'); ?>
      <div class="mt-10 space-y-3">
        <?php foreach ($faqs as $f): ?>
          <details class="card group px-6 py-5 [&_summary::-webkit-details-marker]:hidden"><summary class="flex cursor-pointer list-none items-center justify-between gap-4 font-medium"><?= e($f['q'] ?? '') ?><span class="flex h-8 w-8 flex-none items-center justify-center rounded-full border border-white/10 text-neo-green transition group-open:rotate-45">+</span></summary><p class="mt-3 text-sm leading-relaxed text-white/60"><?= e($f['a'] ?? '') ?></p></details>
        <?php endforeach; ?>
      </div>
    </section>
    <?php endif; ?>

    <?php if ($related): ?>
    <section class="mx-auto mt-24 max-w-6xl px-5 sm:px-6">
      <?php section_head('related', 'You might also <span class="text-neo-green">need</span>', '', 'left'); ?>
      <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3"><?php foreach ($related as $r) service_card($r); ?></div>
    </section>
    <?php endif; ?>
    <?php
    cta_band('Ready to start your ' . $s['title'] . '?');
    page_end();
    exit;
}

/* -------- Services listing -------- */
page_start(['title' => 'Services', 'description' => 'Websites, ecommerce, web apps, Android apps, AI agents, automation, Chrome extensions, SEO and AEO/GEO — with downloadable brochures and negotiable pricing.', 'active' => 'services']);
$all = services_active();
?>
<section class="relative overflow-hidden pt-32 sm:pt-40">
  <div class="grid-bg pointer-events-none absolute inset-0"></div>
  <div class="relative mx-auto max-w-6xl px-5 text-center sm:px-6">
    <p class="kicker">// services</p>
    <h1 class="mx-auto mt-4 max-w-3xl text-balance text-4xl font-extrabold tracking-tight sm:text-6xl">Everything you need to <span class="text-neo-green">grow online</span></h1>
    <p class="mx-auto mt-6 max-w-2xl text-lg text-white/60">Pick a service to see deliverables, process, timeline and FAQs — and download its brochure. Every price is a starting point and fully negotiable.</p>
  </div>
</section>
<section class="mx-auto mt-14 max-w-6xl px-5 sm:px-6">
  <div class="flex flex-wrap justify-center gap-2" data-filter-group="#svc-grid">
    <button type="button" class="chip is-active" data-filter="all">All</button>
    <?php foreach (SERVICE_CATEGORIES as $k => $v): if (array_filter($all, static fn($s) => $s['category'] === $k)): ?>
      <button type="button" class="chip" data-filter="<?= e($k) ?>"><?= e($v) ?></button>
    <?php endif; endforeach; ?>
  </div>
  <div id="svc-grid" class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
    <?php foreach ($all as $s): ?><div data-type="<?= e($s['category']) ?>"><?php service_card($s); ?></div><?php endforeach; ?>
  </div>
</section>
<?php cta_band('Not sure which service fits?', 'Tell me about your business and goal. I will suggest the right-size scope for your budget — free.'); page_end();
