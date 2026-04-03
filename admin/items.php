<?php
$pageTitle = 'Manage Items';
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

$action = $_GET['action'] ?? '';
$itemId = (int) ($_GET['id'] ?? 0);

if ($itemId && in_array($action, ['approve', 'reject', 'resolve'], true)) {
    $status = $action === 'approve' ? 'active' : ($action === 'reject' ? 'rejected' : 'resolved');
    setItemStatus($itemId, $status, (int) $_SESSION['user_id']);
    flash('success', 'Item status updated.');
    redirect('admin/items.php');
}

$items = getItems([], 100, 0, true);
include __DIR__ . '/../includes/header.php';
?>
<div class="section-card p-6">
    <h1 class="text-3xl font-bold mb-6">Manage Items</h1>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Location</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= e($item['title']) ?></td>
                        <td><?= e($item['type']) ?></td>
                        <td><span class="badge <?= statusBadge($item['status']) ?>"><?= e($item['status']) ?></span></td>
                        <td><?= e($item['location']) ?></td>
                        <td class="space-x-2">
                            <?php if ($item['status'] === 'pending'): ?>
                                <a class="text-blue-400" href="<?= base_url('admin/items.php?action=approve&id=' . $item['id']) ?>">Approve</a>
                                <a class="text-red-400" href="<?= base_url('admin/items.php?action=reject&id=' . $item['id']) ?>">Reject</a>
                            <?php elseif ($item['status'] === 'active'): ?>
                                <a class="text-emerald-400" href="<?= base_url('admin/items.php?action=resolve&id=' . $item['id']) ?>">Resolve</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
