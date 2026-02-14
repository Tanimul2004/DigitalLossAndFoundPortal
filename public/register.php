<?php
$pageTitle = "Register";
require_once __DIR__ . '/../includes/functions.php';
if (isLoggedIn()) redirect('public/dashboard.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!csrf_validate($_POST['csrf'] ?? null)) {
    flash_set('error', 'Security check failed. Please try again.');
    redirect('public/register.php');
  }

  $name = sanitize($_POST['name'] ?? '');
  $email = sanitize($_POST['email'] ?? '');
  $phone = sanitize($_POST['phone'] ?? '');
  $accept = isset($_POST['accept_terms']) ? 'yes' : 'no';
  if ($accept !== 'yes') {
    flash_set('error', 'You must accept the Terms & Conditions to register.');
    redirect('public/register.php');
  }
  $password = $_POST['password'] ?? '';
  $confirm = $_POST['confirm_password'] ?? '';

  if ($name==='' || $email==='' || $password==='') {
    flash_set('error', 'Please fill all required fields.');
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    flash_set('error', 'Invalid email address.');
  } elseif (strlen($password) < 6) {
    flash_set('error', 'Password must be at least 6 characters.');
  } elseif ($password !== $confirm) {
    flash_set('error', 'Passwords do not match.');
  } elseif (user_by_email($email)) {
    flash_set('error', 'Email is already registered.');
  } else {
    global $pdo;
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $st = $pdo->prepare("INSERT INTO users (name,email,password,phone) VALUES (?,?,?,?)");
    $st->execute([$name, $email, $hash, $phone ?: null]);
    flash_set('success', 'Registration successful. Please login.');
    redirect('public/login.php');
  }
}
include __DIR__ . '/../includes/header.php';
?>
<div class="max-w-md mx-auto">
  <div class="glass-strong glow-border border border-white/10 rounded-3xl p-7 md:p-8 reveal overflow-hidden relative">
    <div class="absolute inset-0 opacity-40" style="background: radial-gradient(800px 300px at 10% 0%, rgba(59,130,246,.25), transparent 60%),
                                                        radial-gradient(700px 280px at 90% 10%, rgba(16,185,129,.16), transparent 60%),
                                                        radial-gradient(800px 320px at 50% 110%, rgba(245,158,11,.12), transparent 60%);"></div>
    <div class="relative">
      <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full glass border border-white/10 text-xs text-gray-300">
        <i class="fa-solid fa-user-plus text-primary"></i>
        Create your Nexus account
      </div>
      <h1 class="text-3xl font-extrabold mt-4">Register</h1>
      <p class="text-gray-300 mt-1">Report items, submit claims, and track progress — securely.</p>

    <form class="mt-6 space-y-4" method="post">
      <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>" />
      <div>
        <label class="text-sm text-gray-300">Full name *</label>
        <input name="name" required class="mt-2 w-full glass border border-white/10 rounded-2xl px-4 py-3 focus:border-primary focus:outline-none" />
      </div>
      <div>
        <label class="text-sm text-gray-300">Email *</label>
        <input name="email" type="email" required class="mt-2 w-full glass border border-white/10 rounded-2xl px-4 py-3 focus:border-primary focus:outline-none" />
      </div>
      <div>
        <label class="text-sm text-gray-300">Phone (optional)</label>
        <input name="phone" class="mt-2 w-full glass border border-white/10 rounded-2xl px-4 py-3 focus:border-primary focus:outline-none" />
      </div>
      <div>
        <label class="text-sm text-gray-300">Password *</label>
        <input name="password" type="password" required class="mt-2 w-full glass border border-white/10 rounded-2xl px-4 py-3 focus:border-primary focus:outline-none" />
      </div>
      <div>
        <label class="text-sm text-gray-300">Confirm password *</label>
        <input name="confirm_password" type="password" required class="mt-2 w-full glass border border-white/10 rounded-2xl px-4 py-3 focus:border-primary focus:outline-none" />
      </div>

      <div class="glass border border-white/10 rounded-2xl p-4">
        <label class="flex items-start gap-3 cursor-pointer">
          <input type="checkbox" name="accept_terms" required class="mt-1 w-5 h-5 accent-blue-500" />
          <div class="text-sm text-gray-200">
            I agree to the
            <a href="<?= BASE_URL ?>public/terms.php" class="text-primary hover:text-blue-300 font-semibold" target="_blank" rel="noopener">Terms &amp; Conditions</a>.
            <div class="text-xs text-gray-400 mt-1">Contact details are shared only after admin approval.</div>
          </div>
        </label>
      </div>

      <button class="w-full bg-primary hover:bg-blue-700 font-semibold py-3 rounded-2xl shadow-lg shadow-blue-500/15">
        Create account
      </button>

      <p class="text-sm text-gray-400 text-center">
        Already have an account?
        <a class="text-primary hover:text-blue-300" href="<?= BASE_URL ?>public/login.php">Login</a>
      </p>
    </form>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
