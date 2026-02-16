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



<!-- Hero -->

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


<!-- Recently Found -->


<!-- Mobile Apps (Coming soon) — above footer -->

<?php include __DIR__ . '/../includes/footer.php'; ?>
