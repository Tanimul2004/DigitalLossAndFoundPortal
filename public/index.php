<?php
$pageTitle = "Home";
require_once __DIR__ . '/../includes/functions.php';

$lostRecent  = items_list(['type' => 'lost'], 6, 0);
$foundRecent = items_list(['type' => 'found'], 6, 0);

include __DIR__ . '/../includes/header.php';

function item_img_url($filename): string {
  if (!$filename) return '';
  return BASE_URL . 'assets/uploads/' . $filename;
}
?>

<!-- Background ornaments -->
<div class="pointer-events-none fixed inset-0 -z-10 opacity-70">
  <div class="absolute -top-24 -left-24 w-80 h-80 rounded-full blur-3xl bg-blue-500/20"></div>
  <div class="absolute top-24 -right-24 w-96 h-96 rounded-full blur-3xl bg-emerald-500/15"></div>
  <div class="absolute bottom-0 left-1/3 w-[28rem] h-[28rem] rounded-full blur-3xl bg-amber-500/10"></div>
</div>

<!-- Hero -->
<section class="reveal glow-border glass-strong rounded-3xl p-8 md:p-12 overflow-hidden relative">
  <div class="absolute inset-0 opacity-40" style="background: radial-gradient(800px 280px at 20% 20%, rgba(59,130,246,.35), transparent 60%),
                                                  radial-gradient(900px 320px at 80% 0%, rgba(16,185,129,.25), transparent 55%);"></div>

  <div class="relative grid grid-cols-1 md:grid-cols-2 gap-10 items-center">
    <div>
      <div class="inline-flex items-center gap-2 text-xs px-3 py-1 rounded-full glass border border-white/10">
        <i class="fa-solid fa-shield-halved text-emerald-300"></i>
        Privacy-first • contact shared after admin approval
      </div>
      <h1 class="mt-4 text-3xl md:text-5xl font-extrabold tracking-tight">
        Lost &amp; Found, <span class="text-primary">connected</span> — securely.
      </h1>
      <p class="mt-4 text-gray-300 max-w-xl">
        Report lost/found items, browse verified posts, and claim with confidence.
        When a claim is approved, both parties can securely exchange contact details.
      </p>

      <div class="mt-6 flex flex-wrap gap-3">
        <a href="<?= BASE_URL ?>items/report.php?type=lost"
           class="glass hover:bg-white/5 border border-white/10 px-5 py-3 rounded-2xl font-semibold hover-lift">
          <i class="fa-solid fa-triangle-exclamation text-amber-300 mr-2"></i>Report Lost
        </a>
        <a href="<?= BASE_URL ?>items/report.php?type=found"
           class="glass hover:bg-white/5 border border-white/10 px-5 py-3 rounded-2xl font-semibold hover-lift">
          <i class="fa-solid fa-hand-holding-heart text-emerald-300 mr-2"></i>Report Found
        </a>
        <a href="<?= BASE_URL ?>items/search.php"
           class="bg-primary/90 hover:bg-primary px-5 py-3 rounded-2xl font-semibold hover-lift">
          <i class="fa-solid fa-magnifying-glass mr-2"></i>Browse Items
        </a>
      </div>

      <div class="mt-6 text-xs text-gray-400">
        Tip: add a clear photo to increase the chance of a quick match.
      </div>
    </div>

    <div class="relative">
      <div class="glass rounded-3xl border border-white/10 p-6 md:p-8 glow-border hover-lift">
        <div class="flex items-center justify-between">
          <div>
            <div class="text-sm text-gray-400">Fast actions</div>
            <div class="text-xl font-extrabold mt-1">One place for every report</div>
          </div>
          <div class="w-12 h-12 rounded-2xl glass grid place-items-center border border-white/10 floaty">
            <i class="fa-solid fa-compass text-primary text-xl"></i>
          </div>
        </div>

        <div class="mt-6 grid grid-cols-2 gap-3 text-sm">
          <div class="glass rounded-2xl p-4 border border-white/10">
            <div class="text-gray-400 text-xs">Secure claims</div>
            <div class="font-bold mt-1">Admin verification</div>
          </div>
          <div class="glass rounded-2xl p-4 border border-white/10">
            <div class="text-gray-400 text-xs">Privacy</div>
            <div class="font-bold mt-1">Contact after approval</div>
          </div>
          <div class="glass rounded-2xl p-4 border border-white/10">
            <div class="text-gray-400 text-xs">Search</div>
            <div class="font-bold mt-1">Filters + keywords</div>
          </div>
          <div class="glass rounded-2xl p-4 border border-white/10">
            <div class="text-gray-400 text-xs">Dashboard</div>
            <div class="font-bold mt-1">Track status</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php
function render_item_card(array $it): void {
  $img = !empty($it['image']) ? item_img_url($it['image']) : '';
  $badge = $it['type'] === 'lost'
    ? '<span class="text-xs px-2 py-0.5 rounded-full bg-amber-500/15 border border-amber-500/20 text-amber-200">Lost</span>'
    : '<span class="text-xs px-2 py-0.5 rounded-full bg-emerald-500/15 border border-emerald-500/20 text-emerald-200">Found</span>';
?>
  <a class="reveal glass glow-border hover-lift rounded-3xl overflow-hidden border border-white/10 block"
     href="<?= BASE_URL ?>items/view.php?id=<?= (int)$it['id'] ?>">
    <div class="relative h-44 bg-black/20">
      <?php if ($img): ?>
        <img src="<?= e($img) ?>" alt="Item photo" class="w-full h-full object-cover opacity-95" />
      <?php else: ?>
        <div class="w-full h-full grid place-items-center">
          <div class="text-center">
            <div class="w-12 h-12 rounded-2xl glass border border-white/10 grid place-items-center mx-auto">
              <i class="fa-solid fa-image text-gray-200"></i>
            </div>
            <div class="mt-2 text-xs text-gray-400">No photo uploaded</div>
          </div>
        </div>
      <?php endif; ?>
      <div class="absolute inset-x-0 bottom-0 p-3 bg-gradient-to-t from-black/60 to-transparent">
        <div class="flex items-center justify-between gap-2">
          <div class="flex items-center gap-2">
            <?= $badge ?>
            <span class="text-[11px] text-gray-200/80"><?= e(date('M d, Y', strtotime($it['created_at']))) ?></span>
          </div>
          <span class="text-[11px] px-2 py-0.5 rounded-full glass border border-white/10 text-gray-200/90">
            <?= e($it['category'] ?: 'General') ?>
          </span>
        </div>
      </div>
    </div>

    <div class="p-5">
      <div class="font-extrabold text-lg truncate"><?= e($it['title']) ?></div>
      <div class="mt-2 text-sm text-gray-300 line-clamp-2"><?= e($it['description']) ?></div>
      <div class="mt-4 flex items-center justify-between text-xs text-gray-400">
        <div class="truncate"><i class="fa-solid fa-location-dot mr-1"></i><?= e($it['location']) ?></div>
        <div class="text-primary font-semibold">View <i class="fa-solid fa-arrow-right ml-1"></i></div>
      </div>
    </div>
  </a>
<?php } ?>

<!-- Recently Lost -->
<section class="mt-10">
  <div class="flex items-end justify-between gap-4">
    <div>
      <h2 class="text-2xl font-extrabold">Recently Lost</h2>
      <p class="text-sm text-gray-400 mt-1">Latest verified lost reports with photos (when available).</p>
    </div>
    <a class="text-sm text-gray-300 hover:text-white" href="<?= BASE_URL ?>items/search.php?type=lost">
      View all <i class="fa-solid fa-arrow-right ml-1"></i>
    </a>
  </div>

  <?php if (empty($lostRecent)): ?>
    <div class="mt-5 glass rounded-3xl border border-white/10 p-6 text-gray-300">No lost items yet.</div>
  <?php else: ?>
    <div class="mt-5 grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
      <?php foreach ($lostRecent as $it): render_item_card($it); endforeach; ?>
    </div>
  <?php endif; ?>
</section>

<!-- Recently Found -->
<section class="mt-12">
  <div class="flex items-end justify-between gap-4">
    <div>
      <h2 class="text-2xl font-extrabold">Recently Found</h2>
      <p class="text-sm text-gray-400 mt-1">Latest verified found reports with photos (when available).</p>
    </div>
    <a class="text-sm text-gray-300 hover:text-white" href="<?= BASE_URL ?>items/search.php?type=found">
      View all <i class="fa-solid fa-arrow-right ml-1"></i>
    </a>
  </div>

  <?php if (empty($foundRecent)): ?>
    <div class="mt-5 glass rounded-3xl border border-white/10 p-6 text-gray-300">No found items yet.</div>
  <?php else: ?>
    <div class="mt-5 grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
      <?php foreach ($foundRecent as $it): render_item_card($it); endforeach; ?>
    </div>
  <?php endif; ?>
</section>

<!-- Mobile Apps (Coming soon) — above footer -->
<section class="mt-14">
  <div class="reveal glass-strong glow-border rounded-3xl p-7 md:p-10 border border-white/10 overflow-hidden relative">
    <div class="absolute inset-0 opacity-30" style="background: radial-gradient(900px 260px at 20% 30%, rgba(59,130,246,.35), transparent 55%),
                                                    radial-gradient(900px 320px at 80% 40%, rgba(245,158,11,.22), transparent 55%);"></div>
    <div class="relative">
      <div class="flex items-center justify-between flex-wrap gap-4">
        <div>
          <h3 class="text-2xl font-extrabold">Mobile Apps (Coming Soon)</h3>
          <p class="text-gray-300 mt-1 text-sm max-w-2xl">We’ll add Android & iOS apps here when development starts. This section is reserved for future releases.</p>
        </div>
        <div class="text-xs px-3 py-1 rounded-full glass border border-white/10 text-gray-200/90">
          Future roadmap
        </div>
      </div>

      <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-5">
        <div class="glass hover-lift rounded-3xl border border-white/10 p-6">
          <div class="flex items-start justify-between">
            <div>
              <div class="text-emerald-300 text-sm font-bold"><i class="fa-brands fa-android mr-2"></i>Android App</div>
              <div class="mt-2 text-gray-300 text-sm">Push notifications, photo capture, quick claim verification.</div>
            </div>
            <div class="text-xs px-2 py-1 rounded-full bg-white/5 border border-white/10">Coming soon</div>
          </div>
        </div>

        <div class="glass hover-lift rounded-3xl border border-white/10 p-6">
          <div class="flex items-start justify-between">
            <div>
              <div class="text-sky-300 text-sm font-bold"><i class="fa-brands fa-apple mr-2"></i>iOS App</div>
              <div class="mt-2 text-gray-300 text-sm">Face ID login, rich search, secure contact exchange.</div>
            </div>
            <div class="text-xs px-2 py-1 rounded-full bg-white/5 border border-white/10">Coming soon</div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
