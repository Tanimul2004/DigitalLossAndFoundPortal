<?php
$pageTitle = "Manage Claims";
require_once __DIR__ . '/../includes/functions.php';
if (!isLoggedIn() || !isAdmin()) redirect('public/login.php');

$action = sanitize($_GET['action'] ?? '');
$id = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!csrf_validate($_POST['csrf'] ?? null)) {
    flash_set('error', 'Security check failed.');
    redirect('admin/claims.php');
  }
  $claimId = (int)($_POST['claim_id'] ?? 0);
  $decision = sanitize($_POST['decision'] ?? '');
  $notes = sanitize($_POST['admin_notes'] ?? '');

  try {
    if ($decision === 'approve') {
      admin_claim_approve($claimId, (int)$_SESSION['user_id'], $notes);
      flash_set('success', 'Claim approved and item resolved. Contact info is now shared between both users.');
    } elseif ($decision === 'reject') {
      admin_claim_reject($claimId, (int)$_SESSION['user_id'], $notes);
      flash_set('success', 'Claim rejected.');
    }
  } catch (Throwable $e) {
    flash_set('error', 'Could not process claim.');
  }
  redirect('admin/claims.php');
}

include __DIR__ . '/../includes/header.php';

if ($action === 'view' && $id > 0) {
  $claim = claim_by_id($id);
  if (!$claim) {
    flash_set('error', 'Claim not found.');
    redirect('admin/claims.php');
  }
  $item = item_by_id((int)$claim['item_id']);
  if (!$item) {
    flash_set('error', 'Item not found.');
    redirect('admin/claims.php');
  }

  // --- Simple matching hints (heuristics, not “AI”) ---
  $claimText = strtolower((string)($claim['claim_details'] ?? '') . ' ' . (string)($claim['proof_docs'] ?? ''));
  $title = strtolower((string)$item['title']);
  $location = strtolower((string)$item['location']);
  $category = strtolower((string)$item['category']);

  $titleTokens = array_filter(preg_split('/\s+/', preg_replace('/[^a-z0-9\s]/i', ' ', $title)) ?: [], fn($w) => strlen($w) >= 4);
  $titleMatch = false;
  foreach ($titleTokens as $tok) { if (strpos($claimText, $tok) !== false) { $titleMatch = true; break; } }
  $locationMatch = $location && strpos($claimText, $location) !== false;
  $categoryMatch = $category && strpos($claimText, $category) !== false;

  $score = (int)$titleMatch + (int)$locationMatch + (int)$categoryMatch; // 0..3
  $scorePct = (int)round(($score / 3) * 100);
?>
  <div class="flex items-end justify-between gap-4 flex-wrap">
    <div>
      <div class="inline-flex items-center gap-2 text-xs px-3 py-1 rounded-full bg-gray-800/60 border border-gray-700 text-gray-200">
        <i class="fa-solid fa-scale-balanced text-primary"></i>
        Review &amp; verify (Original vs Claim)
      </div>
      <h1 class="text-3xl font-extrabold mt-3">Claim Review</h1>
      <p class="text-gray-400 mt-1">Compare the reported item details with the claimant’s proof, then decide.</p>
    </div>
    <a class="text-gray-400 hover:text-gray-200" href="<?= BASE_URL ?>admin/claims.php">
      <i class="fa-solid fa-arrow-left mr-2"></i>Back to claims
    </a>
  </div>

  <!-- Match Meter -->
  <div class="reveal mt-6 bg-card border border-gray-800 rounded-3xl p-5 overflow-hidden relative">
    <div class="absolute -right-20 -top-24 h-64 w-64 rounded-full bg-primary/10 blur-3xl"></div>
    <div class="flex items-center justify-between gap-4 flex-wrap relative">
      <div class="font-extrabold">Match Hints</div>
      <div class="text-xs text-gray-400">Auto-hints (not final decision)</div>
    </div>

    <div class="mt-3 h-2 bg-gray-800 rounded-full overflow-hidden">
      <div class="h-full bg-primary rounded-full" style="width: <?= (int)$scorePct ?>%"></div>
    </div>

    <div class="mt-4 grid sm:grid-cols-3 gap-3 relative">
      <div class="flex items-center gap-2 bg-gray-900/50 border border-gray-800 rounded-2xl p-3">
        <i class="fa-solid <?= $titleMatch ? 'fa-circle-check text-emerald-400' : 'fa-circle-xmark text-gray-500' ?>"></i>
        <div class="text-sm">
          <div class="font-semibold text-gray-100">Title keywords</div>
          <div class="text-xs text-gray-400"><?= $titleMatch ? 'Mentioned in claim text' : 'Not detected' ?></div>
        </div>
      </div>
      <div class="flex items-center gap-2 bg-gray-900/50 border border-gray-800 rounded-2xl p-3">
        <i class="fa-solid <?= $locationMatch ? 'fa-circle-check text-emerald-400' : 'fa-circle-xmark text-gray-500' ?>"></i>
        <div class="text-sm">
          <div class="font-semibold text-gray-100">Location</div>
          <div class="text-xs text-gray-400"><?= $locationMatch ? 'Location mentioned' : 'Not detected' ?></div>
        </div>
      </div>
      <div class="flex items-center gap-2 bg-gray-900/50 border border-gray-800 rounded-2xl p-3">
        <i class="fa-solid <?= $categoryMatch ? 'fa-circle-check text-emerald-400' : 'fa-circle-xmark text-gray-500' ?>"></i>
        <div class="text-sm">
          <div class="font-semibold text-gray-100">Category</div>
          <div class="text-xs text-gray-400"><?= $categoryMatch ? 'Category mentioned' : 'Not detected' ?></div>
        </div>
      </div>
    </div>
  </div>

  <div class="mt-6 grid lg:grid-cols-3 gap-6">
    <!-- Original item -->
    <div class="reveal lg:col-span-2">
      <div class="bg-card border border-gray-800 rounded-3xl p-6 overflow-hidden relative">
        <div class="absolute -left-16 -top-16 h-48 w-48 rounded-full bg-amber-500/10 blur-3xl"></div>
        <div class="relative">
          <div class="flex items-start justify-between gap-4 flex-wrap">
            <div>
              <div class="inline-flex items-center gap-2 text-xs px-2.5 py-1 rounded-full bg-amber-500/15 border border-amber-500/20 text-amber-200">
                <i class="fa-solid fa-file-circle-check"></i>Original Item Report
              </div>
              <div class="mt-3 text-xl font-extrabold"><?= e($item['title']) ?></div>
              <div class="mt-1 text-sm text-gray-400">
                <i class="fa-solid fa-location-dot mr-1"></i><?= e($item['location']) ?> •
                <span class="capitalize"><?= e($item['type']) ?></span> •
                <?= e($item['category']) ?>
              </div>
            </div>

            <a class="text-sm text-primary hover:text-blue-300" href="<?= BASE_URL ?>items/view.php?id=<?= (int)$item['id'] ?>">
              Open item page <i class="fa-solid fa-up-right-from-square ml-1"></i>
            </a>
          </div>

          <div class="mt-5 grid sm:grid-cols-2 gap-4">
            <div class="bg-gray-900/50 border border-gray-800 rounded-2xl p-4">
              <div class="text-xs text-gray-500">Reporter</div>
              <div class="mt-1 font-semibold text-gray-100"><?= e($claim['reporter_name']) ?></div>
              <div class="text-sm text-gray-400 mt-1"><i class="fa-solid fa-envelope mr-2"></i><?= e($claim['reporter_email'] ?? '') ?></div>
              <div class="text-sm text-gray-400 mt-1"><i class="fa-solid fa-phone mr-2"></i><?= e($claim['reporter_phone'] ?? '—') ?></div>
            </div>

            <div class="bg-gray-900/50 border border-gray-800 rounded-2xl p-4">
              <div class="text-xs text-gray-500">Reported</div>
              <div class="mt-1 text-sm text-gray-400"><i class="fa-solid fa-calendar mr-2"></i><?= e(date('M d, Y', strtotime($item['created_at']))) ?></div>
              <div class="mt-1 text-sm text-gray-400"><i class="fa-solid fa-circle-info mr-2"></i>Status: <span class="font-semibold text-gray-200"><?= e($item['status']) ?></span></div>
              <div class="mt-1 text-sm text-gray-400"><i class="fa-solid fa-hashtag mr-2"></i>Item ID: <span class="font-mono"><?= (int)$item['id'] ?></span></div>
            </div>
          </div>

          <div class="mt-5">
            <div class="font-bold text-gray-100">Description</div>
            <div class="mt-2 bg-gray-900/50 border border-gray-800 rounded-2xl p-4 whitespace-pre-line text-sm text-gray-300">
              <?= e($item['description']) ?>
            </div>
          </div>

          <?php if (!empty($item['image_path'])): ?>
            <div class="mt-5">
              <div class="font-bold text-gray-100">Image</div>
              <img class="mt-2 w-full max-h-96 object-cover rounded-2xl border border-gray-800"
                   src="<?= BASE_URL ?>assets/uploads/<?= e($item['image_path']) ?>" alt="Item image">
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Claim info -->
      <div class="reveal mt-6 bg-card border border-gray-800 rounded-3xl p-6 overflow-hidden relative">
        <div class="absolute -right-16 -bottom-16 h-48 w-48 rounded-full bg-emerald-500/10 blur-3xl"></div>
        <div class="relative">
          <div class="flex items-start justify-between gap-4 flex-wrap">
            <div>
              <div class="inline-flex items-center gap-2 text-xs px-2.5 py-1 rounded-full bg-emerald-500/15 border border-emerald-500/20 text-emerald-200">
                <i class="fa-solid fa-user-check"></i>Claim Submission
              </div>
              <div class="mt-3 text-xl font-extrabold"><?= e($claim['claimant_name']) ?></div>
              <div class="mt-1 text-sm text-gray-400">
                <i class="fa-solid fa-envelope mr-1"></i><?= e($claim['claimant_email'] ?? '') ?>
                <?php if (!empty($claim['claimant_phone'])): ?>
                  • <i class="fa-solid fa-phone ml-2 mr-1"></i><?= e($claim['claimant_phone']) ?>
                <?php endif; ?>
              </div>
            </div>
            <div class="text-right">
              <div class="text-xs text-gray-500">Submitted</div>
              <div class="text-sm text-gray-300"><?= e(date('M d, Y • h:i A', strtotime($claim['created_at']))) ?></div>
            </div>
          </div>

          <div class="mt-5">
            <div class="font-bold text-gray-100">Claim Details (Ownership Proof)</div>
            <div class="mt-2 bg-gray-900/50 border border-gray-800 rounded-2xl p-4 whitespace-pre-line text-sm text-gray-300">
              <?= e($claim['claim_details']) ?>
            </div>
          </div>

          <?php if (!empty($claim['proof_docs'])): ?>
            <div class="mt-5">
              <div class="font-bold text-gray-100">Additional Proof</div>
              <div class="mt-2 bg-gray-900/50 border border-gray-800 rounded-2xl p-4 whitespace-pre-line text-sm text-gray-300">
                <?= e($claim['proof_docs']) ?>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Decision -->
    <div class="reveal">
      <div class="bg-card border border-gray-800 rounded-3xl p-6 sticky top-6">
        <div class="flex items-center justify-between">
          <div class="font-extrabold text-lg">Decision</div>
          <span class="text-xs px-2 py-0.5 rounded-full bg-gray-800 border border-gray-700 text-gray-300">One winner only</span>
        </div>
        <p class="text-sm text-gray-400 mt-2">
          Approving this claim will <span class="text-gray-200 font-semibold">resolve</span> the item and automatically reject other pending claims.
        </p>

        <form class="mt-5 space-y-3" method="post">
          <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
          <input type="hidden" name="claim_id" value="<?= (int)$claim['id'] ?>">

          <label class="text-sm text-gray-300 block">Admin notes (optional)</label>
          <textarea name="admin_notes" rows="4"
                    class="w-full bg-gray-900/60 border border-gray-800 rounded-2xl p-3 focus:outline-none focus:ring-2 focus:ring-primary/40"
                    placeholder="Write why you approved/rejected (helps audit logs)."></textarea>

          <div class="grid grid-cols-2 gap-3 pt-2">
            <button name="decision" value="reject"
                    class="w-full bg-gray-700 hover:bg-gray-600 rounded-2xl px-4 py-3 font-semibold transition-all duration-300 hover:-translate-y-0.5">
              <i class="fa-solid fa-xmark mr-2"></i>Reject
            </button>
            <button name="decision" value="approve"
                    class="w-full bg-emerald-600 hover:bg-emerald-500 rounded-2xl px-4 py-3 font-semibold transition-all duration-300 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-emerald-500/20">
              <i class="fa-solid fa-check mr-2"></i>Approve
            </button>
          </div>
        </form>

        <div class="mt-5 text-xs text-gray-500 leading-relaxed">
          Tip: Ask claimant to mention unique identifiers (serial number, scratches, wallet contents, lockscreen wallpaper, etc.).
        </div>
      </div>
    </div>
  </div>

<?php
  include __DIR__ . '/../includes/footer.php';
  exit;
}

// Default list view
$pending = admin_pending_claims();
?>
<div class="flex items-end justify-between gap-4 flex-wrap">
  <div>
    <div class="inline-flex items-center gap-2 text-xs px-3 py-1 rounded-full bg-gray-800/60 border border-gray-700 text-gray-200">
      <i class="fa-solid fa-inbox text-primary"></i>
      Pending Claims
    </div>
    <h1 class="text-3xl font-extrabold mt-3">Claims Queue</h1>
    <p class="text-gray-400 mt-1">Review each claim against the original report, then approve one claimant.</p>
  </div>
</div>

<div class="mt-6">
  <?php if (empty($pending)): ?>
    <div class="bg-card border border-gray-800 rounded-3xl p-6 text-gray-400">No pending claims.</div>
  <?php else: ?>
    <div class="bg-card border border-gray-800 rounded-3xl overflow-hidden">
      <div class="p-5 border-b border-gray-800 flex items-center justify-between flex-wrap gap-3">
        <div class="font-bold text-gray-100">Pending list</div>
        <div class="text-xs text-gray-500">Click “Review” to compare original vs claim.</div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="text-gray-400">
            <tr class="border-b border-gray-800">
              <th class="py-3 px-5 text-left">Item</th>
              <th class="py-3 px-5 text-left">Reporter</th>
              <th class="py-3 px-5 text-left">Claimant</th>
              <th class="py-3 px-5 text-left">Submitted</th>
              <th class="py-3 px-5 text-left">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($pending as $c): ?>
              <tr class="border-b border-gray-800/60 hover:bg-gray-800/40">
                <td class="py-4 px-5">
                  <div class="font-semibold text-gray-100"><?= e($c['item_title']) ?></div>
                  <div class="text-xs text-gray-500 capitalize"><?= e($c['item_type']) ?></div>
                </td>
                <td class="py-4 px-5">
                  <div class="text-gray-200"><?= e($c['reporter_name']) ?></div>
                  <div class="text-xs text-gray-500"><?= e($c['reporter_email'] ?? '') ?></div>
                </td>
                <td class="py-4 px-5">
                  <div class="text-gray-200"><?= e($c['claimant_name']) ?></div>
                  <div class="text-xs text-gray-500"><?= e($c['claimant_email'] ?? '') ?></div>
                </td>
                <td class="py-4 px-5 text-gray-400"><?= e(date('M d, Y', strtotime($c['created_at']))) ?></td>
                <td class="py-4 px-5">
                  <a class="inline-flex items-center gap-2 text-primary hover:text-blue-300 font-semibold"
                     href="<?= BASE_URL ?>admin/claims.php?action=view&id=<?= (int)$c['id'] ?>">
                    Review <i class="fa-solid fa-up-right-from-square"></i>
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
