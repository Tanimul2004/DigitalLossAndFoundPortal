<?php
$pageTitle = "Manage Items";
require_once __DIR__ . '/../includes/functions.php';
if (!isLoggedIn() || !isAdmin()) redirect('public/login.php');

$action = sanitize($_GET['action'] ?? '');
$id = (int)($_GET['id'] ?? 0);

try {
  if ($action === 'approve' && $id > 0) {
    admin_item_approve($id, (int)$_SESSION['user_id']);
    flash_set('success', 'Item approved and published.');
    redirect('admin/items.php');
  }
  if ($action === 'reject' && $id > 0) {
    admin_item_reject($id, (int)$_SESSION['user_id']);
    flash_set('success', 'Item rejected and removed.');
    redirect('admin/items.php');
  }
} catch (Throwable $e) {
  flash_set('error', 'Action failed.');
  redirect('admin/items.php');
}

$pending = admin_pending_items();

include __DIR__ . '/../includes/header.php';
?>
<div class="flex items-end justify-between gap-4 flex-wrap">
  <div>
    <h1 class="text-3xl font-extrabold">Pending Items</h1>
    <p class="text-gray-400 mt-1">Approve items to make them public.</p>
  </div>
  <a class="text-gray-400 hover:text-gray-200" href="<?= BASE_URL ?>admin/index.php"><i class="fa-solid fa-arrow-left mr-2"></i>Admin</a>
</div>

<div class="mt-6 bg-card border border-gray-800 rounded-2xl p-6">
  <?php if (!$pending): ?>
    <div class="text-gray-500">No pending items.</div>
  <?php else: ?>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="text-gray-400">
          <tr class="border-b border-gray-800">
            <th class="py-2 text-left">Item</th>
            <th class="py-2 text-left">Type</th>
            <th class="py-2 text-left">Reporter</th>
            <th class="py-2 text-left">Location</th>
            <th class="py-2 text-left">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($pending as $it): ?>
            <tr class="border-b border-gray-800/60 hover:bg-gray-800/40">
              <td class="py-3"><a class="hover:text-primary" href="<?= BASE_URL ?>items/view.php?id=<?= (int)$it['id'] ?>"><?= e($it['title']) ?></a></td>
              <td class="py-3"><?= strtoupper(e($it['type'])) ?></td>
              <td class="py-3"><?= e($it['reporter_name']) ?></td>
              <td class="py-3"><?= e($it['location']) ?></td>
              <td class="py-3">
                <a class="px-3 py-2 rounded-xl bg-success hover:bg-emerald-700 font-semibold"
                   href="<?= BASE_URL ?>admin/items.php?action=approve&id=<?= (int)$it['id'] ?>">Approve</a>
                <a class="ml-2 px-3 py-2 rounded-xl bg-danger hover:bg-red-700 font-semibold"
                   href="<?= BASE_URL ?>admin/items.php?action=reject&id=<?= (int)$it['id'] ?>">Reject</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
