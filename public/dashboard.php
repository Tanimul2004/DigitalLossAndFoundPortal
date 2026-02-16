<?php
$pageTitle = "Dashboard";
require_once __DIR__ . '/../includes/functions.php';
if (!isLoggedIn()) redirect('public/login.php');

$user = user_public_by_id((int)$_SESSION['user_id']);
$lost = user_items((int)$_SESSION['user_id'], 'lost');
$found = user_items((int)$_SESSION['user_id'], 'found');
$claims = claims_for_user((int)$_SESSION['user_id']);

include __DIR__ . '/../includes/header.php';
?>
<div class="flex items-start justify-between gap-4 flex-wrap">
  <div>
    <h1 class="text-3xl font-extrabold">Dashboard</h1>
    <p class="text-gray-400 mt-1">Welcome, <?= e($_SESSION['user_name'] ?? 'User') ?>.</p>
  </div>
  <div class="flex gap-2">
    <a class="bg-primary hover:bg-blue-700 px-4 py-2 rounded-xl font-semibold" href="<?= BASE_URL ?>items/report.php?type=lost"><i class="fa-solid fa-plus mr-2"></i>Report</a>
    <a class="bg-gray-700 hover:bg-gray-600 px-4 py-2 rounded-xl font-semibold" href="<?= BASE_URL ?>items/search.php"><i class="fa-solid fa-magnifying-glass mr-2"></i>Browse</a>
  </div>
</div>



  <div class="bg-card border border-gray-800 rounded-2xl p-6">
    <div class="flex items-center justify-between mb-4">
      <h2 class="font-bold text-lg">Your Items</h2>
      <a class="text-primary text-sm hover:text-blue-300" href="<?= BASE_URL ?>items/search.php?mine=1">View</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div class="bg-gray-800/40 border border-gray-800 rounded-xl p-4">
        <div class="font-semibold">Lost</div>
        <div class="mt-2 text-sm text-gray-400">
          <?= $lost ? e($lost[0]['title']) . " (latest)" : "No lost items yet." ?>
        </div>
      </div>
      <div class="bg-gray-800/40 border border-gray-800 rounded-xl p-4">
        <div class="font-semibold">Found</div>
        <div class="mt-2 text-sm text-gray-400">
          <?= $found ? e($found[0]['title']) . " (latest)" : "No found items yet." ?>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
