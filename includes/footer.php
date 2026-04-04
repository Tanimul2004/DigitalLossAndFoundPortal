    </main>

    <footer class="mt-14">
        <div class="max-w-6xl mx-auto px-4">
            <div class="glass-strong glow-border border border-white/10 rounded-3xl overflow-hidden">
                <div class="p-8 md:p-10 grid grid-cols-1 md:grid-cols-4 gap-8 relative">
                    <div class="absolute inset-0 opacity-35" style="background: radial-gradient(900px 320px at 10% 20%, rgba(59,130,246,.35), transparent 60%), radial-gradient(800px 300px at 90% 10%, rgba(16,185,129,.22), transparent 55%), radial-gradient(900px 360px at 70% 90%, rgba(245,158,11,.18), transparent 60%);"></div>

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
                            A capstone-ready platform to report lost and found items, submit claims, and track statuses.
                            Reporter information is revealed only after admin approval.
                        </p>
                    </div>

                    <div class="relative">
                        <div class="font-bold">Quick Links</div>
                        <div class="mt-3 space-y-2 text-sm">
                            <a class="block text-gray-300 hover:text-white" href="<?= base_url() ?>">Home</a>
                            <a class="block text-gray-300 hover:text-white" href="<?= base_url('items/search.php') ?>">Browse Items</a>
                            <a class="block text-gray-300 hover:text-white" href="<?= base_url('items/report.php?type=lost') ?>">Report Lost</a>
                            <a class="block text-gray-300 hover:text-white" href="<?= base_url('items/report.php?type=found') ?>">Report Found</a>
                        </div>
                    </div>

                    <div class="relative">
                        <div class="font-bold">Connect</div>
                        <div class="mt-3 space-y-2 text-sm text-gray-300">
                            <div class="glass border border-white/10 rounded-2xl p-4">
                                <div class="text-xs text-gray-400">Email</div>
                                <div class="mt-1 font-semibold">support@nexus.local</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative border-t border-white/10 px-8 py-5 flex flex-col md:flex-row items-center justify-between gap-3 text-sm">
                    <div class="text-gray-400">© <?= date('Y') ?> Lost &amp; Found Portal • Built with PHP, MySQL, Tailwind CSS</div>
                    <div class="flex items-center gap-4 text-gray-400">
                        <a class="hover:text-white" href="<?= base_url('public/terms.php') ?>">Terms</a>
                        <span class="opacity-80">Privacy-first claims</span>
                    </div>
                </div>
            </div>

            <div class="h-10"></div>
        </div>
    </footer>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="<?= base_url('assets/js/script.js?v=2.1') ?>"></script>
</body>
</html>
