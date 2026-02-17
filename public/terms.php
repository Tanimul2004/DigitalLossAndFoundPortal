<?php
$pageTitle = "Terms & Conditions";
require_once __DIR__ . '/../includes/functions.php';
include __DIR__ . '/../includes/header.php';
?>

<div class="max-w-3xl mx-auto">
  <div class="glass-strong glow-border border border-white/10 rounded-3xl overflow-hidden reveal">
    <div class="p-8 md:p-10 relative">
      <div class="absolute inset-0 opacity-40" style="background: radial-gradient(900px 340px at 20% 10%, rgba(59,130,246,.28), transparent 60%),
                                                        radial-gradient(850px 360px at 90% 0%, rgba(16,185,129,.18), transparent 60%),
                                                        radial-gradient(900px 360px at 60% 100%, rgba(245,158,11,.14), transparent 60%);"></div>

      <div class="relative">
        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full glass border border-white/10 text-xs text-gray-300">
          <i class="fa-solid fa-shield-halved text-emerald-300"></i>
          Privacy-first • Capstone Demo Terms
        </div>

        <h1 class="mt-4 text-3xl md:text-4xl font-extrabold tracking-tight">Terms &amp; Conditions</h1>
        <p class="mt-2 text-gray-300">By creating an account or using Lost &amp; Found Nexus, you agree to the terms below.</p>

        <div class="mt-8 space-y-5 text-sm text-gray-200/90 leading-relaxed">
          <div class="glass border border-white/10 rounded-2xl p-5">
            <div class="font-bold">1) Purpose</div>
            <p class="mt-2 text-gray-300">This system helps users report lost/found items and submit claims. It is designed for educational/capstone use and can be adapted for real deployment.</p>
          </div>

          <div class="glass border border-white/10 rounded-2xl p-5">
            <div class="font-bold">2) User Responsibility</div>
            <ul class="mt-2 text-gray-300 list-disc pl-5 space-y-1">
              <li>Provide accurate information when reporting items or submitting claims.</li>
              <li>Do not post illegal, harmful, or misleading content.</li>
              <li>Respect other users and communicate responsibly.</li>
            </ul>
          </div>

          <div class="glass border border-white/10 rounded-2xl p-5">
            <div class="font-bold">3) Privacy & Contact Sharing</div>
            <p class="mt-2 text-gray-300">
              To protect privacy, personal contact details (email/phone) are <span class="text-emerald-200 font-semibold">hidden by default</span>.
              Contact details are only revealed to <span class="text-emerald-200 font-semibold">both parties</span> after the admin approves a claim (final approval).
            </p>
          </div>

          <div class="glass border border-white/10 rounded-2xl p-5">
            <div class="font-bold">4) Admin Decisions</div>
            <p class="mt-2 text-gray-300">Admin actions (approvals/rejections) are final for the purpose of the demo. In a real product, you can add appeal/dispute workflows.</p>
          </div>

          <div class="glass border border-white/10 rounded-2xl p-5">
            <div class="font-bold">5) Liability</div>
            <p class="mt-2 text-gray-300">This platform is provided “as-is” for capstone demonstration. The project team is not responsible for disputes or losses.</p>
          </div>
        </div>

        <div class="mt-8 flex flex-col sm:flex-row gap-3">
          <a href="<?= BASE_URL ?>public/register.php" class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-primary hover:bg-blue-700 font-semibold">
            <i class="fa-solid fa-user-plus"></i> Create an account
          </a>
          <a href="<?= BASE_URL ?>" class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl glass border border-white/10 hover:bg-white/5">
            <i class="fa-solid fa-house"></i> Back to Home
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
