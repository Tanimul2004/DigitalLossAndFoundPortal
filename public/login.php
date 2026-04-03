<?php
$pageTitle = 'Login';
require_once __DIR__ . '/../includes/functions.php';

if (isLoggedIn()) {
    redirect('public/dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid request.';
    } else {
        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $user = getUserByEmail($email);

        if ($user && password_verify($password, $user['password'])) {
            loginUser($user);
            flash('success', 'Welcome back, ' . $user['name'] . '!');
            redirect($user['role'] === 'admin' ? 'admin/index.php' : 'public/dashboard.php');
        }

        $error = 'Invalid email or password.';
    }
}

include __DIR__ . '/../includes/header.php';
?>
<div class="max-w-md mx-auto section-card p-8 reveal">
    <h1 class="text-3xl font-bold mb-2">Welcome Back</h1>
    <p class="text-slate-400 mb-6">Login to your Lost &amp; Found Nexus account.</p>

    <?php if ($error): ?>
        <div class="mb-5 rounded-xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-red-300">
            <?= e($error) ?>
        </div>
    <?php endif; ?>

    <form method="post" class="space-y-4">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

        <div>
            <label class="block mb-2 text-sm font-medium">Email</label>
            <input class="input" type="email" name="email" required>
        </div>

        <div>
            <label class="block mb-2 text-sm font-medium">Password</label>
            <input class="input" type="password" name="password" required>
        </div>

        <button class="btn btn-primary w-full">Login</button>
    </form>

    <p class="text-slate-400 mt-5 text-sm">
        Admin: admin@nexus.com / Admin123!<br>
        User: user@example.com / User123!
    </p>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
