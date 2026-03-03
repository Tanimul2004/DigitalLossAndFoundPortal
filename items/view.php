<?php
$pageTitle = "Item Details";
require_once __DIR__ . '/../includes/functions.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) redirect('items/search.php');

$item = item_by_id($id);
if (!$item) redirect('items/search.php');

// only show if active OR owner/admin
if ($item['status'] !== 'active' && !(isLoggedIn() && (isAdmin() || (int)$item['user_id'] === (int)$_SESSION['user_id']))) {
  flash_set('error', 'This item is not public.');
  redirect('items/search.php');
}

$claims = claims_for_item($id);
$userClaim = null;

$approvedClaim = approved_claim_for_item($id);
$canSeeContactExchange = false;
if (isLoggedIn()) {
  $uid = (int)$_SESSION['user_id'];
  if (isAdmin()) {
    $canSeeContactExchange = true;
  } elseif ($approvedClaim) {
    $canSeeContactExchange = ($uid === (int)$item['user_id'] || $uid === (int)$approvedClaim['user_id']);
  }
}

if (isLoggedIn()) {
  foreach ($claims as $c) {
    if ((int)$c['user_id'] === (int)$_SESSION['user_id']) { $userClaim = $c; break; }
  }
}

include __DIR__ . '/../includes/header.php';
?>
<nav class="text-sm text-gray-500 mb-4">
  <a class="hover:text-gray-300" href="<?= BASE_URL ?>">Home</a>
  <span class="mx-2">/</span>
  <a class="hover:text-gray-300" href="<?= BASE_URL ?>items/search.php">Browse</a>
  <span class="mx-2">/</span>
  <span class="text-gray-300">Item</span>
</nav>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
  <div class="lg:col-span-2">
    <div class="bg-card border border-gray-800 rounded-2xl p-7">
      <div class="flex items-start justify-between gap-4 flex-wrap">
        <div class="flex items-center gap-2">
          <span class="text-xs px-3 py-1 rounded-full <?= $item['type']==='lost' ? 'bg-red-500/15 text-red-200' : 'bg-emerald-500/15 text-emerald-200' ?>">
            <?= strtoupper(e($item['type'])) ?>
          </span>
          <span class="text-xs px-3 py-1 rounded-full bg-blue-500/15 text-blue-200">
            <?= strtoupper(e($item['status'])) ?>
          </span>
        </div>
        <div class="text-xs text-gray-500">Reported: <?= date('F j, Y', strtotime($item['created_at'])) ?></div>
      </div>

      <h1 class="text-3xl font-extrabold mt-4"><?= e($item['title']) ?></h1>

      <?php if (!empty($item['image'])): ?>
        <img class="mt-5 w-full h-80 object-cover rounded-2xl border border-gray-800" src="<?= BASE_URL ?>assets/uploads/<?= e($item['image']) ?>" alt="">
      <?php endif; ?>

      <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
        <div class="bg-gray-800/40 border border-gray-800 rounded-xl p-4">
          <div class="text-gray-500 text-xs">Location</div>
          <div class="mt-1 font-semibold"><i class="fa-solid fa-location-dot text-primary mr-2"></i><?= e($item['location']) ?></div>
        </div>
        <div class="bg-gray-800/40 border border-gray-800 rounded-xl p-4">
          <div class="text-gray-500 text-xs">Date</div>
          <div class="mt-1 font-semibold"><?= date('F j, Y', strtotime($item['date_lost_found'])) ?></div>
        </div>
        <div class="bg-gray-800/40 border border-gray-800 rounded-xl p-4">
          <div class="text-gray-500 text-xs">Category</div>
          <div class="mt-1 font-semibold"><?= e($item['category'] ?: 'Not specified') ?></div>
        </div>
        <div class="bg-gray-800/40 border border-gray-800 rounded-xl p-4">
          <div class="text-gray-500 text-xs">Reporter</div>
          <div class="mt-1 font-semibold"><?= e($item['reporter_name']) ?></div>
        </div>
      </div>

      <div class="mt-6">
        <div class="font-bold mb-2">Description</div>
        <div class="bg-gray-800/40 border border-gray-800 rounded-xl p-4 text-gray-200 whitespace-pre-line">
          <?= e($item['description']) ?>
        </div>
      </div>

      <?php if ($userClaim): ?>
        <div class="mt-6 rounded-2xl border border-blue-500/30 bg-blue-500/10 p-4">
          <div class="font-semibold"><i class="fa-solid fa-circle-info text-primary mr-2"></i>You already claimed this item</div>
          <div class="text-sm text-gray-300 mt-1">Status: <span class="font-semibold"><?= strtoupper(e($userClaim['status'])) ?></span></div>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <div class="space-y-4">
    <?php if (!isLoggedIn()): ?>
      <div class="bg-card border border-gray-800 rounded-2xl p-6">
        <div class="font-bold text-lg">Claim this item</div>
        <div class="text-sm text-gray-400 mt-1">Login required to submit a claim.</div>
        <a class="mt-4 block text-center bg-primary hover:bg-blue-700 font-semibold py-3 rounded-xl"
           href="<?= BASE_URL ?>public/login.php">
          <i class="fa-solid fa-right-to-bracket mr-2"></i>Login
        </a>
      </div>
    <?php elseif ($item['status']==='active' && (int)$item['user_id'] !== (int)$_SESSION['user_id'] && !$userClaim): ?>
      <div class="bg-card border border-gray-800 rounded-2xl p-6">
        <div class="font-bold text-lg">Claim this item</div>
        <div class="text-sm text-gray-400 mt-1">Provide proof and details for admin review.</div>
        <a class="mt-4 block text-center bg-primary hover:bg-blue-700 font-semibold py-3 rounded-xl"
           href="<?= BASE_URL ?>claims/create.php?item_id=<?= (int)$item['id'] ?>">
          <i class="fa-solid fa-hand mr-2"></i>Submit Claim
        </a>
      </div>
    <?php endif; ?>

    <div class="bg-card border border-gray-800 rounded-2xl p-6">
      <div class="flex items-start justify-between gap-3">
        <div>
          <div class="font-bold">Contact details</div>
          <div class="text-sm text-gray-400 mt-1">
            For privacy, personal contact is shared only after an admin approves a claim.
          </div>
        </div>
        <div class="text-xs px-2 py-1 rounded-full bg-gray-800 border border-gray-700 text-gray-300">
          <i class="fa-solid fa-lock mr-1"></i>Private
        </div>
      </div>

      <?php if ($canSeeContactExchange && $approvedClaim): ?>
        <div class="mt-4 grid grid-cols-1 gap-3">
          <div class="bg-gray-800/40 border border-gray-800 rounded-xl p-4">
            <div class="text-xs text-gray-500">Reporter</div>
            <div class="mt-2 space-y-2 text-sm">
              <div><i class="fa-solid fa-user text-primary w-5"></i> <?= e($item['reporter_name']) ?></div>
              <?php if (!empty($item['reporter_phone'])): ?>
                <div><i class="fa-solid fa-phone text-primary w-5"></i> <?= e($item['reporter_phone']) ?></div>
              <?php endif; ?>
              <div><i class="fa-solid fa-envelope text-primary w-5"></i> <?= e($item['reporter_email']) ?></div>
            </div>
          </div>

          <div class="bg-gray-800/40 border border-gray-800 rounded-xl p-4">
            <div class="text-xs text-gray-500">Approved Claimant</div>
            <div class="mt-2 space-y-2 text-sm">
              <div><i class="fa-solid fa-user-check text-success w-5"></i> <?= e($approvedClaim['claimant_name']) ?></div>
              <?php if (!empty($approvedClaim['claimant_phone'])): ?>
                <div><i class="fa-solid fa-phone text-success w-5"></i> <?= e($approvedClaim['claimant_phone']) ?></div>
              <?php endif; ?>
              <div><i class="fa-solid fa-envelope text-success w-5"></i> <?= e($approvedClaim['claimant_email']) ?></div>
            </div>
          </div>
        </div>
      <?php else: ?>
        <div class="mt-4 text-sm text-gray-400">
          <i class="fa-solid fa-circle-info text-primary mr-2"></i>
          Contact info will appear here after a claim is approved by an admin.
        </div>
      <?php endif; ?>
    </div>

    <?php if (isAdmin()): ?>
      <div class="bg-card border border-gray-800 rounded-2xl p-6">
        <div class="font-bold">Claims for this item</div>
        <div class="text-sm text-gray-400 mt-1"><?= count($claims) ?> total</div>
        <?php if (!$claims): ?>
          <div class="text-sm text-gray-500 mt-4">No claims yet.</div>
        <?php else: ?>
          <div class="mt-4 space-y-3">
            <?php foreach ($claims as $c): ?>
              <div class="bg-gray-800/40 border border-gray-800 rounded-xl p-4">
                <div class="flex items-center justify-between">
                  <div class="font-semibold"><?= e($c['claimant_name']) ?></div>
                  <span class="text-xs px-2 py-1 rounded-full <?= $c['status']==='pending'?'bg-amber-500/15 text-amber-200':($c['status']==='approved'?'bg-emerald-500/15 text-emerald-200':'bg-red-500/15 text-red-200') ?>">
                    <?= strtoupper(e($c['status'])) ?>
                  </span>
                </div>
                <div class="text-sm text-gray-400 mt-2 line-clamp-2"><?= e($c['claim_details']) ?></div>
                <a class="text-xs text-primary hover:text-blue-300 mt-2 inline-block" href="<?= BASE_URL ?>admin/claims.php?action=view&id=<?= (int)$c['id'] ?>">Review</a>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
