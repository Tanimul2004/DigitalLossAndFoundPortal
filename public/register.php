<?php
$pageTitle = 'Register';
require_once __DIR__ . '/../includes/functions.php';

if (isLoggedIn()) {
    redirect('public/dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid request.';
    } else {
        $name = sanitize($_POST['name'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (!$name || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6) {
            $error = 'Provide valid name, email, and a password of at least 6 characters.';
        } elseif ($password !== $confirmPassword) {
            $error = 'Passwords do not match.';
        } elseif (getUserByEmail($email)) {
            $error = 'Email already registered.';
        } else {
            createUser($name, $email, $password, $phone);
            flash('success', 'Registration completed. Please login.');
            redirect('public/login.php');
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>
<div class="max-w-lg mx-auto section-card p-8 reveal">
    <h1 class="text-3xl font-bold mb-2">Create Account</h1>
    <p class="text-slate-400 mb-6">Join the platform to report and claim items securely.</p>

    <?php if ($error): ?>
        <div class="mb-5 rounded-xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-red-300">
            <?= e($error) ?>
        </div>
    <?php endif; ?>

    <form method="post" class="grid md:grid-cols-2 gap-4">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

        <div class="md:col-span-2">
            <label class="block mb-2 text-sm font-medium">Full Name</label>
            <input class="input" name="name" required>
        </div>

        <div>
            <label class="block mb-2 text-sm font-medium">Email</label>
            <input class="input" type="email" name="email" required>
        </div>

        <div>
            <label class="block mb-2 text-sm font-medium">Phone</label>
            <input class="input" name="phone">
        </div>

        <div>
            <label class="block mb-2 text-sm font-medium">Password</label>
            <input class="input" type="password" name="password" required>
        </div>

        <div>
            <label class="block mb-2 text-sm font-medium">Confirm Password</label>
            <input class="input" type="password" name="confirm_password" required>
        </div>

        <div class="md:col-span-2">
            <button class="btn btn-primary w-full">Register</button>
        </div>
    </form>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
