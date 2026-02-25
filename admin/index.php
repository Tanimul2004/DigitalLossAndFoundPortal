<?php
$pageTitle = "Admin Dashboard";
require_once __DIR__ . '/../includes/functions.php';
if (!isLoggedIn() || !isAdmin()) redirect('public/login.php');

$pendingItems = admin_pending_items();
$pendingClaims = admin_pending_claims();
$recentActions = admin_recent_actions(8);

global $pdo;
$pendingItemsCount = (int)$pdo->query("SELECT COUNT(*) c FROM items WHERE status='pending'")->fetch()['c'];
$activeItemsCount = (int)$pdo->query("SELECT COUNT(*) c FROM items WHERE status='active'")->fetch()['c'];
$pendingClaimsCount = (int)$pdo->query("SELECT COUNT(*) c FROM claims WHERE status='pending'")->fetch()['c'];

include __DIR__ . '/../includes/header.php';
?>
<section class="rounded-2xl p-7 md:p-10 bg-gradient-to-r from-gray-900 via-gray-900 to-gray-800 border border-gray-800 overflow-hidden relative">
  <div class="absolute -top-24 -right-24 w-72 h-72 bg-primary/20 blur-3xl rounded-full"></div>
  <div class="absolute -bottom-24 -left-24 w-72 h-72 bg-emerald-500/10 blur-3xl rounded-full"></div>

  <div class="relative flex items-start justify-between gap-4 flex-wrap">
    <div>
      <div class="inline-flex items-center gap-2 text-xs px-3 py-1 rounded-full bg-gray-800/60 border border-gray-700 text-gray-200">
        <i class="fa-solid fa-shield-halved text-primary"></i>
        Admin Console
      </div>
      <h1 class="text-3xl md:text-4xl font-extrabold mt-3">Admin Dashboard</h1>
      <p class="text-gray-300 mt-2">Approve items, review claims, and close resolved cases.</p>
    </div>

    <div class="text-sm text-gray-300">
      Logged in as <span class="text-primary font-semibold"><?= e($_SESSION['user_name']) ?></span>
      <div class="mt-2 flex gap-2">
        <a href="<?= BASE_URL ?>admin/items.php" class="px-4 py-2 rounded-xl bg-gray-800/60 border border-gray-700 hover:border-warning text-sm font-semibold">
          <i class="fa-solid fa-list-check mr-2 text-warning"></i>Pending Items
        </a>
        <a href="<?= BASE_URL ?>admin/claims.php" class="px-4 py-2 rounded-xl bg-gray-800/60 border border-gray-700 hover:border-primary text-sm font-semibold">
          <i class="fa-solid fa-handshake-angle mr-2 text-primary"></i>Pending Claims
        </a>
      </div>
    </div>
  </div>
</section>

<div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
  <a href="<?= BASE_URL ?>admin/items.php" class="group bg-card border border-gray-800 hover:border-warning rounded-2xl p-5">
    <div class="flex items-start justify-between">
      <div>
        <div class="text-sm text-gray-400">Pending items</div>
        <div class="text-3xl font-bold mt-2 text-warning"><?= $pendingItemsCount ?></div>
        <div class="text-xs text-gray-500 mt-2">Approve or reject</div>
      </div>
      <div class="w-11 h-11 rounded-2xl bg-warning/15 grid place-items-center border border-warning/20">
        <i class="fa-solid fa-clipboard-check text-warning"></i>
      </div>
    </div>
  </a>

  <a href="<?= BASE_URL ?>admin/claims.php" class="group bg-card border border-gray-800 hover:border-primary rounded-2xl p-5">
    <div class="flex items-start justify-between">
      <div>
        <div class="text-sm text-gray-400">Pending claims</div>
        <div class="text-3xl font-bold mt-2 text-primary"><?= $pendingClaimsCount ?></div>
        <div class="text-xs text-gray-500 mt-2">Approve one claimant</div>
      </div>
      <div class="w-11 h-11 rounded-2xl bg-primary/15 grid place-items-center border border-primary/20">
        <i class="fa-solid fa-handshake-angle text-primary"></i>
      </div>
    </div>
  </a>

  <div class="bg-card border border-gray-800 rounded-2xl p-5">
    <div class="flex items-start justify-between">
      <div>
        <div class="text-sm text-gray-400">Active items</div>
        <div class="text-3xl font-bold mt-2 text-emerald-300"><?= $activeItemsCount ?></div>
        <div class="text-xs text-gray-500 mt-2">Visible to public</div>
      </div>
      <div class="w-11 h-11 rounded-2xl bg-emerald-500/15 grid place-items-center border border-emerald-500/20">
        <i class="fa-solid fa-eye text-emerald-300"></i>
      </div>
    </div>
  </div>

  <div class="bg-card border border-gray-800 rounded-2xl p-5">
    <div class="flex items-start justify-between">
      <div>
        <div class="text-sm text-gray-400">Quick safety</div>
        <div class="text-lg font-bold mt-2">Privacy lock</div>
        <div class="text-xs text-gray-500 mt-2">Contacts shown only after approval</div>
      </div>
      <div class="w-11 h-11 rounded-2xl bg-gray-800 grid place-items-center border border-gray-700">
        <i class="fa-solid fa-lock text-gray-300"></i>
      </div>
    </div>
  </div>
</div>

<div class="mt-8 grid grid-cols-1 xl:grid-cols-3 gap-6">
  <div class="bg-card border border-gray-800 rounded-2xl p-6">
    <div class="flex items-center justify-between mb-4">
      <div class="font-bold text-lg">Pending Items</div>
      <a class="text-primary text-sm hover:text-blue-300" href="<?= BASE_URL ?>admin/items.php">Open</a>
    </div>
    <?php if (!$pendingItems): ?>
      <div class="text-gray-500 text-sm">No pending items.</div>
    <?php else: ?>
      <div class="space-y-3">
        <?php foreach (array_slice($pendingItems, 0, 6) as $it): ?>
          <div class="bg-gray-800/40 border border-gray-800 rounded-xl p-4 flex items-start justify-between gap-3">
            <div>
              <div class="font-semibold"><?= e($it['title']) ?></div>
              <div class="text-xs text-gray-500 mt-1">
                <i class="fa-solid fa-user mr-1"></i><?= e($it['reporter_name']) ?>
                <span class="mx-2">•</span>
                <i class="fa-solid fa-location-dot mr-1"></i><?= e($it['location']) ?>
              </div>
            </div>
            <div class="flex gap-2">
              <a class="px-3 py-2 rounded-xl bg-success hover:bg-emerald-700 text-sm font-semibold"
                 href="<?= BASE_URL ?>admin/items.php?action=approve&id=<?= (int)$it['id'] ?>">Approve</a>
              <a class="px-3 py-2 rounded-xl bg-danger hover:bg-red-700 text-sm font-semibold"
                 href="<?= BASE_URL ?>admin/items.php?action=reject&id=<?= (int)$it['id'] ?>">Reject</a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

  <div class="bg-card border border-gray-800 rounded-2xl p-6">
    <div class="flex items-center justify-between mb-4">
      <div class="font-bold text-lg">Pending Claims</div>
      <a class="text-primary text-sm hover:text-blue-300" href="<?= BASE_URL ?>admin/claims.php">Open</a>
    </div>
    <?php if (!$pendingClaims): ?>
      <div class="text-gray-500 text-sm">No pending claims.</div>
    <?php else: ?>
      <div class="space-y-3">
        <?php foreach (array_slice($pendingClaims, 0, 6) as $c): ?>
          <div class="bg-gray-800/40 border border-gray-800 rounded-xl p-4">
            <div class="flex items-start justify-between gap-2">
              <div class="font-semibold"><?= e($c['item_title']) ?></div>
              <div class="text-xs text-gray-500"><?= date('M d', strtotime($c['created_at'])) ?></div>
            </div>
            <div class="text-sm text-gray-400 mt-1">Claimant: <?= e($c['claimant_name']) ?></div>
            <a class="text-primary text-sm hover:text-blue-300 mt-3 inline-block"
               href="<?= BASE_URL ?>admin/claims.php?action=view&id=<?= (int)$c['id'] ?>">Review</a>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

  <div class="bg-card border border-gray-800 rounded-2xl p-6">
    <div class="flex items-center justify-between mb-4">
      <div class="font-bold text-lg">Recent Admin Actions</div>
      <span class="text-xs text-gray-500">Audit log</span>
    </div>
    <?php if (!$recentActions): ?>
      <div class="text-gray-500 text-sm">No actions yet.</div>
    <?php else: ?>
      <div class="space-y-3">
        <?php foreach ($recentActions as $a): ?>
          <div class="bg-gray-800/40 border border-gray-800 rounded-xl p-4">
            <div class="flex items-start justify-between gap-2">
              <div class="font-semibold text-sm">
                <?= e($a['action_type']) ?>
                <span class="text-gray-500">#<?= (int)$a['target_id'] ?></span>
              </div>
              <div class="text-xs text-gray-500"><?= date('M d, H:i', strtotime($a['timestamp'])) ?></div>
            </div>
            <div class="text-xs text-gray-500 mt-1">By <?= e($a['admin_name']) ?></div>
            <?php if (!empty($a['details'])): ?>
              <div class="text-sm text-gray-300 mt-2 line-clamp-2"><?= e($a['details']) ?></div>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
