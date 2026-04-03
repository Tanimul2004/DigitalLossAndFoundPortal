<?php
$pageTitle = 'Profile';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$user = getUserById((int) $_SESSION['user_id']);
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid request.';
    } else {
        $name = sanitize($_POST['name'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');

        db()->prepare('UPDATE users SET name = ?, phone = ? WHERE id = ?')
            ->execute([$name, $phone, $_SESSION['user_id']]);

        $_SESSION['user_name'] = $name;
        flash('success', 'Profile updated.');
        redirect('profile/index.php');
    }
}

include __DIR__ . '/../includes/header.php';
?>
<div class="max-w-3xl mx-auto section-card p-8">
    <h1 class="text-3xl font-bold mb-6">Your Profile</h1>

    <?php if ($error): ?>
        <div class="mb-5 rounded-xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-red-300">
            <?= e($error) ?>
        </div>
    <?php endif; ?>

    <form method="post" class="grid md:grid-cols-2 gap-4">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

        <div class="md:col-span-2">
            <label class="block mb-2 text-sm font-medium">Full Name</label>
            <input class="input" name="name" value="<?= e($user['name']) ?>">
        </div>

        <div>
            <label class="block mb-2 text-sm font-medium">Email</label>
            <input class="input" value="<?= e($user['email']) ?>" readonly>
        </div>

        <div>
            <label class="block mb-2 text-sm font-medium">Phone</label>
            <input class="input" name="phone" value="<?= e($user['phone']) ?>">
        </div>

        <div class="md:col-span-2">
            <button class="btn btn-primary">Save Changes</button>
        </div>
    </form>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
