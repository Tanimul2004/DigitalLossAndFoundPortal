<?php
$pageTitle = 'Manage Users';
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

$users = db()->query('SELECT id, name, email, role, phone, created_at FROM users ORDER BY created_at DESC')->fetchAll();

include __DIR__ . '/../includes/header.php';
?>
<div class="section-card p-6">
    <h1 class="text-3xl font-bold mb-6">Manage Users</h1>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Phone</th>
                    <th>Joined</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= e($user['name']) ?></td>
                        <td><?= e($user['email']) ?></td>
                        <td><?= e($user['role']) ?></td>
                        <td><?= e($user['phone']) ?></td>
                        <td><?= e(date('M d, Y', strtotime($user['created_at']))) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
