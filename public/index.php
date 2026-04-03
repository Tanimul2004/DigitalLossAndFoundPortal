<?php
$pageTitle = 'Home';
require_once __DIR__ . '/../includes/functions.php';

$recentLostItems = getItems(['type' => 'lost'], 8, 0, false);
$recentFoundItems = getItems(['type' => 'found'], 8, 0, false);
$totalActive = countRows('items', "status='active'");
$totalUsers = countRows('users', "role='user'");
$totalClaims = countRows('claims');

include __DIR__ . '/../includes/header.php';
?>

<section class="hero-panel px-6 py-10 md:px-10 md:py-14 mb-10 overflow-hidden relative reveal">
    <div class="absolute inset-0 hero-orb opacity-80"></div>
    <div class="relative z-10 grid lg:grid-cols-2 gap-10 items-center">
        <div class="max-w-3xl">
            <div class="badge bg-blue-500/15 text-blue-300 border border-blue-500/20 mb-5">
                Capstone Project • Secure Lost &amp; Found Platform
            </div>
            <h1 class="text-4xl md:text-5xl xl:text-6xl font-extrabold leading-tight tracking-tight mb-5">
                Lost &amp; Found <span class="text-blue-400">Nexus</span>
            </h1>
            <p class="text-lg md:text-xl text-slate-300 mb-8 max-w-2xl">
                A modern dark-themed system for reporting lost items, posting found items,
                managing claims, and resolving cases with a professional admin workflow.
            </p>
            <div class="flex flex-wrap gap-4">
                <?php if (!isLoggedIn()): ?>
                    <a href="<?= base_url('public/register.php') ?>" class="btn btn-primary btn-lg">Get Started</a>
                <?php endif; ?>
                <a href="<?= base_url('items/search.php') ?>" class="btn btn-secondary btn-lg">Browse Items</a>
                <a href="<?= base_url('items/report.php?type=lost') ?>" class="btn btn-glass btn-lg">Report Lost</a>
                <a href="<?= base_url('items/report.php?type=found') ?>" class="btn btn-glass btn-lg">Report Found</a>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="stat-card reveal">
                <div class="text-4xl font-extrabold text-blue-400 mb-2"><?= $totalActive ?></div>
                <div class="text-slate-300 font-medium">Active Listings</div>
                <div class="text-sm text-slate-500 mt-2">Current lost and found reports visible to users</div>
            </div>
            <div class="stat-card reveal">
                <div class="text-4xl font-extrabold text-emerald-400 mb-2"><?= $totalUsers ?></div>
                <div class="text-slate-300 font-medium">Registered Users</div>
                <div class="text-sm text-slate-500 mt-2">People using the recovery system</div>
            </div>
            <div class="stat-card reveal">
                <div class="text-4xl font-extrabold text-amber-400 mb-2"><?= $totalClaims ?></div>
                <div class="text-slate-300 font-medium">Claims Submitted</div>
                <div class="text-sm text-slate-500 mt-2">Ownership requests reviewed by admin</div>
            </div>
        </div>
    </div>
</section>

<section class="space-y-8 mb-10">
    <div class="section-card p-6 md:p-7 reveal">
        <div class="flex items-center justify-between mb-5 gap-4 flex-wrap">
            <div>
                <div class="badge bg-red-500/15 text-red-300 border border-red-500/20 mb-3">Lost Items</div>
                <h2 class="text-2xl font-bold">Recently Reported Lost Items</h2>
                <p class="text-slate-400 mt-1">Lost items are shown first in small image cards.</p>
            </div>
            <a href="<?= base_url('items/search.php?type=lost') ?>" class="text-blue-400 hover:text-blue-300 font-semibold whitespace-nowrap">View All</a>
        </div>

        <?php if ($recentLostItems): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <?php foreach ($recentLostItems as $item): ?>
                    <article class="listing-card reveal">
                        <div class="flex gap-3 items-start">
                            <div class="thumb-wrap">
                                <?php if ($item['image']): ?>
                                    <img src="<?= base_url('assets/uploads/' . $item['image']) ?>" alt="<?= e($item['title']) ?>" class="thumb-img">
                                <?php else: ?>
                                    <div class="thumb-placeholder thumb-lost">L</div>
                                <?php endif; ?>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-start justify-between gap-3 mb-2">
                                    <span class="badge <?= itemTypeBadge($item['type']) ?>">Lost</span>
                                    <span class="text-[11px] text-slate-500 whitespace-nowrap"><?= e(date('M d, Y', strtotime($item['created_at']))) ?></span>
                                </div>
                                <h3 class="text-base font-semibold text-white mb-1 truncate"><?= e($item['title']) ?></h3>
                                <p class="text-slate-400 text-sm line-clamp-2 mb-2"><?= e($item['description']) ?></p>
                                <div class="flex items-center justify-between gap-3 text-sm">
                                    <div class="text-slate-500 truncate">
                                        <i class="fa-solid fa-location-dot mr-1"></i><?= e($item['location']) ?>
                                    </div>
                                    <a href="<?= base_url('items/view.php?id=' . $item['id']) ?>" class="text-blue-400 hover:text-blue-300 font-semibold whitespace-nowrap">Details</a>
                                </div>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">No lost items have been posted yet.</div>
        <?php endif; ?>
    </div>

    <div class="section-card p-6 md:p-7 reveal">
        <div class="flex items-center justify-between mb-5 gap-4 flex-wrap">
            <div>
                <div class="badge bg-emerald-500/15 text-emerald-300 border border-emerald-500/20 mb-3">Found Items</div>
                <h2 class="text-2xl font-bold">Recently Reported Found Items</h2>
                <p class="text-slate-400 mt-1">Found items are shown below in small image cards.</p>
            </div>
            <a href="<?= base_url('items/search.php?type=found') ?>" class="text-blue-400 hover:text-blue-300 font-semibold whitespace-nowrap">View All</a>
        </div>

        <?php if ($recentFoundItems): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <?php foreach ($recentFoundItems as $item): ?>
                    <article class="listing-card reveal">
                        <div class="flex gap-3 items-start">
                            <div class="thumb-wrap">
                                <?php if ($item['image']): ?>
                                    <img src="<?= base_url('assets/uploads/' . $item['image']) ?>" alt="<?= e($item['title']) ?>" class="thumb-img">
                                <?php else: ?>
                                    <div class="thumb-placeholder thumb-found">F</div>
                                <?php endif; ?>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-start justify-between gap-3 mb-2">
                                    <span class="badge <?= itemTypeBadge($item['type']) ?>">Found</span>
                                    <span class="text-[11px] text-slate-500 whitespace-nowrap"><?= e(date('M d, Y', strtotime($item['created_at']))) ?></span>
                                </div>
                                <h3 class="text-base font-semibold text-white mb-1 truncate"><?= e($item['title']) ?></h3>
                                <p class="text-slate-400 text-sm line-clamp-2 mb-2"><?= e($item['description']) ?></p>
                                <div class="flex items-center justify-between gap-3 text-sm">
                                    <div class="text-slate-500 truncate">
                                        <i class="fa-solid fa-location-dot mr-1"></i><?= e($item['location']) ?>
                                    </div>
                                    <a href="<?= base_url('items/view.php?id=' . $item['id']) ?>" class="text-blue-400 hover:text-blue-300 font-semibold whitespace-nowrap">Details</a>
                                </div>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">No found items have been posted yet.</div>
        <?php endif; ?>
    </div>
</section>

<section class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
    <div class="section-card p-6 reveal">
        <div class="icon-pill icon-red">1</div>
        <h3 class="text-xl font-bold mt-4 mb-2">Report Item</h3>
        <p class="text-slate-400">
            Post a lost or found item with title, description, image preview,
            and an exact location selected from the map.
        </p>
    </div>
    <div class="section-card p-6 reveal">
        <div class="icon-pill icon-amber">2</div>
        <h3 class="text-xl font-bold mt-4 mb-2">Search &amp; Match</h3>
        <p class="text-slate-400">
            Browse public listings with separate lost and found sections,
            keyword search, and location-based details.
        </p>
    </div>
    <div class="section-card p-6 reveal">
        <div class="icon-pill icon-emerald">3</div>
        <h3 class="text-xl font-bold mt-4 mb-2">Claim &amp; Resolve</h3>
        <p class="text-slate-400">
            Submit a secure claim, let the admin verify ownership,
            and resolve the case using claim match information.
        </p>
    </div>
</section>

<section class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
    <div class="section-card p-6 reveal app-soon-card app-soon-card--android">
        <div class="flex items-start gap-4">
            <div class="w-14 h-14 rounded-2xl bg-emerald-500/15 text-emerald-300 grid place-items-center text-2xl border border-emerald-500/20">
                <i class="fa-brands fa-android"></i>
            </div>
            <div>
                <div class="badge bg-emerald-500/15 text-emerald-300 border border-emerald-500/20 mb-3">Coming Soon</div>
                <h3 class="text-2xl font-bold mb-2">Android App</h3>
                <p class="text-slate-400">
                    Quick reporting, claim tracking, and real-time notifications will soon be available on Android.
                </p>
            </div>
        </div>
    </div>

    <div class="section-card p-6 reveal app-soon-card app-soon-card--ios">
        <div class="flex items-start gap-4">
            <div class="w-14 h-14 rounded-2xl bg-sky-500/15 text-sky-300 grid place-items-center text-2xl border border-sky-500/20">
                <i class="fa-brands fa-apple"></i>
            </div>
            <div>
                <div class="badge bg-sky-500/15 text-sky-300 border border-sky-500/20 mb-3">Coming Soon</div>
                <h3 class="text-2xl font-bold mb-2">iOS App</h3>
                <p class="text-slate-400">
                    The iPhone and iPad version will bring the same secure lost-and-found experience in a mobile app.
                </p>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
