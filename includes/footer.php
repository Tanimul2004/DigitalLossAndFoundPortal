    </main>

  <footer class="mt-14">
    <div class="max-w-6xl mx-auto px-4">
      <div class="glass-strong glow-border border border-white/10 rounded-3xl overflow-hidden">
        <div class="p-8 md:p-10 grid grid-cols-1 md:grid-cols-4 gap-8 relative">
          <div class="absolute inset-0 opacity-35" style="background: radial-gradient(900px 320px at 10% 20%, rgba(59,130,246,.35), transparent 60%),
                                                          radial-gradient(800px 300px at 90% 10%, rgba(16,185,129,.22), transparent 55%),
                                                          radial-gradient(900px 360px at 70% 90%, rgba(245,158,11,.18), transparent 60%);"></div>

          <div class="relative md:col-span-2">
            <div class="flex items-center gap-3">
              <div class="w-11 h-11 rounded-2xl glass border border-white/10 grid place-items-center">
                <i class="fa-solid fa-compass text-primary text-xl"></i>
              </div>
              <div>
                <div class="font-extrabold text-lg">Lost &amp; Found Portal</div>
                <div class="text-xs text-gray-400 -mt-0.5">Secure Lost &amp; Found Management</div>
              </div>
            </div>

            <p class="mt-4 text-sm text-gray-300 max-w-xl">
              A capstone-ready platform to report lost/found items, submit claims, and track statuses.
              Personal contact is shared only after admin approval to protect user privacy.
            </p>

            <div class="mt-5 flex flex-wrap gap-3 text-sm">
              <span class="glass border border-white/10 px-3 py-2 rounded-2xl">
                <i class="fa-solid fa-shield-halved text-emerald-300 mr-2"></i>Privacy-first
              </span>
              <span class="glass border border-white/10 px-3 py-2 rounded-2xl">
                <i class="fa-solid fa-bolt text-amber-300 mr-2"></i>Fast reporting
              </span>
              <span class="glass border border-white/10 px-3 py-2 rounded-2xl">
                <i class="fa-solid fa-magnifying-glass text-sky-300 mr-2"></i>Smart search
              </span>
            </div>
          </div>

          <div class="relative">
            <div class="font-bold">Quick Links</div>
            <div class="mt-3 space-y-2 text-sm">
              <a class="block text-gray-300 hover:text-white" href="<?= BASE_URL ?>">Home</a>
              <a class="block text-gray-300 hover:text-white" href="<?= BASE_URL ?>items/search.php">Browse Items</a>
              <a class="block text-gray-300 hover:text-white" href="<?= BASE_URL ?>items/report.php?type=lost">Report Lost</a>
              <a class="block text-gray-300 hover:text-white" href="<?= BASE_URL ?>items/report.php?type=found">Report Found</a>
              <?php if (isLoggedIn()): ?>
                <a class="block text-gray-300 hover:text-white" href="<?= BASE_URL ?>public/dashboard.php">Dashboard</a>
              <?php else: ?>
                <a class="block text-gray-300 hover:text-white" href="<?= BASE_URL ?>public/login.php">Login</a>
              <?php endif; ?>
            </div>
          </div>

          <div class="relative">
            <div class="font-bold">Connect</div>
            <div class="mt-3 space-y-2 text-sm text-gray-300">
              <div class="glass border border-white/10 rounded-2xl p-4">
                <div class="text-xs text-gray-400">Email</div>
                <div class="mt-1 font-semibold">support@nexus.local</div>
              </div>

              <div class="flex items-center gap-3 mt-3">
                <a class="w-11 h-11 rounded-2xl glass border border-white/10 grid place-items-center hover:bg-white/5" href="#" aria-label="Facebook">
                  <i class="fa-brands fa-facebook-f"></i>
                </a>
                <a class="w-11 h-11 rounded-2xl glass border border-white/10 grid place-items-center hover:bg-white/5" href="#" aria-label="Instagram">
                  <i class="fa-brands fa-instagram"></i>
                </a>
                <a class="w-11 h-11 rounded-2xl glass border border-white/10 grid place-items-center hover:bg-white/5" href="#" aria-label="X">
                  <i class="fa-brands fa-x-twitter"></i>
                </a>
                <a class="w-11 h-11 rounded-2xl glass border border-white/10 grid place-items-center hover:bg-white/5" href="#" aria-label="GitHub">
                  <i class="fa-brands fa-github"></i>
                </a>
              </div>

              <div class="text-xs text-gray-500 mt-3">
                Social links are placeholders for your capstone.
              </div>
            </div>
          </div>
        </div>

        <div class="relative border-t border-white/10 px-8 py-5 flex flex-col md:flex-row items-center justify-between gap-3 text-sm">
          <div class="text-gray-400">
            © <?= date('Y') ?> Lost &amp; Found Portal • Built with ...........
          </div>
          <div class="flex items-center gap-4 text-gray-400">
            <span class="hidden md:inline">•</span>
            <a class="hover:text-white" href="<?= BASE_URL ?>public/terms.php">Terms</a>
            <span class="hidden md:inline">•</span>
            <span class="opacity-80">Privacy</span>
          </div>
        </div>
      </div>

      <div class="h-10"></div>
    </div>
  </footer>

  <script src="<?= BASE_URL ?>assets/js/script.js"></script>
</body>
</html>
