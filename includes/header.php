<?php
require_once __DIR__ . '/functions.php';
$flash = flash_get();
?><!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= e(APP_NAME) ?><?= isset($pageTitle) ? ' • ' . e($pageTitle) : '' ?></title>

    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?= base_url('assets/css/custom.css?v=2.1') ?>" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#3b82f6',
                        success: '#10b981',
                        warning: '#f59e0b',
                        danger: '#ef4444',
                        card: '#1f2937',
                        bg: '#111827'
                    }
                }
            }
        };
    </script>
</head>
<body class="nexus-bg bg-slate-950 text-gray-100 min-h-screen">
    <nav class="glass border-b border-white/10 sticky top-0 z-40">
        <div class="max-w-6xl mx-auto px-4">
            <div class="h-16 flex items-center justify-between">
                <a href="<?= base_url() ?>" class="flex items-center gap-2">
                    <div class="w-9 h-9 bg-primary rounded-xl grid place-items-center">
                        <i class="fa-solid fa-compass text-white"></i>
                    </div>
                    <div class="leading-tight">
                        <div class="font-bold">Portal</div>
                        <div class="text-xs text-gray-400 -mt-0.5">Lost &amp; Found</div>
                    </div>
                </a>

                <button id="mobileBtn" class="md:hidden p-2 rounded-lg hover:bg-gray-700/40">
                    <i class="fa-solid fa-bars text-xl"></i>
                </button>

                <div class="hidden md:flex items-center gap-6">
                    <a class="hover:text-primary transition" href="<?= base_url() ?>">Home</a>
                    <a class="hover:text-primary transition" href="<?= base_url('items/search.php') ?>">Browse</a>

                    <div class="relative group">
                        <button type="button" class="hover:text-primary flex items-center gap-2 transition">
                            Report
                            <i class="fa-solid fa-chevron-down text-xs opacity-80"></i>
                        </button>
                        <div class="absolute left-0 top-full pt-2 w-52 opacity-0 invisible pointer-events-none group-hover:opacity-100 group-hover:visible group-hover:pointer-events-auto transition duration-150">
                            <div class="glass border border-white/10 rounded-2xl p-2 shadow-xl">
                                <a class="block px-3 py-2 rounded-xl hover:bg-white/5" href="<?= base_url('items/report.php?type=lost') ?>">
                                    <i class="fa-solid fa-circle-minus text-amber-300 mr-2"></i>Report Lost
                                </a>
                                <a class="block px-3 py-2 rounded-xl hover:bg-white/5" href="<?= base_url('items/report.php?type=found') ?>">
                                    <i class="fa-solid fa-circle-plus text-emerald-300 mr-2"></i>Report Found
                                </a>
                            </div>
                        </div>
                    </div>

                    <?php if (isLoggedIn()): ?>
                        <a class="hover:text-primary transition" href="<?= base_url('public/dashboard.php') ?>">Dashboard</a>
                        <?php if (isAdmin()): ?>
                            <a class="hover:text-primary transition" href="<?= base_url('admin/index.php') ?>">Admin</a>
                        <?php endif; ?>
                        <a href="<?= base_url('profile/index.php') ?>" class="flex items-center gap-2 px-3 py-1.5 rounded-full glass hover:bg-white/5 border border-white/10 transition">
                            <div class="w-8 h-8 rounded-full grid place-items-center bg-white/5 border border-white/10">
                                <i class="fa-solid fa-user text-gray-200 text-sm"></i>
                            </div>
                            <div class="hidden lg:block text-sm font-semibold"><?= e($_SESSION['user_name'] ?? 'User') ?></div>
                        </a>
                        <a class="bg-danger hover:bg-red-700 px-4 py-2 rounded-lg transition" href="<?= base_url('public/logout.php') ?>">Logout</a>
                    <?php else: ?>
                        <a class="hover:text-primary transition" href="<?= base_url('public/login.php') ?>">Login</a>
                        <a class="bg-primary hover:bg-blue-700 px-4 py-2 rounded-lg transition" href="<?= base_url('public/register.php') ?>">Register</a>
                    <?php endif; ?>
                </div>
            </div>

            <div id="mobileMenu" class="md:hidden hidden pb-4">
                <div class="flex flex-col gap-2 text-sm">
                    <a class="px-3 py-2 rounded-lg hover:bg-gray-700/40" href="<?= base_url() ?>">Home</a>
                    <a class="px-3 py-2 rounded-lg hover:bg-gray-700/40" href="<?= base_url('items/search.php') ?>">Browse</a>
                    <a class="px-3 py-2 rounded-lg hover:bg-gray-700/40" href="<?= base_url('items/report.php?type=lost') ?>">Report Lost</a>
                    <a class="px-3 py-2 rounded-lg hover:bg-gray-700/40" href="<?= base_url('items/report.php?type=found') ?>">Report Found</a>
                    <?php if (isLoggedIn()): ?>
                        <a class="px-3 py-2 rounded-lg hover:bg-gray-700/40" href="<?= base_url('public/dashboard.php') ?>">Dashboard</a>
                        <?php if (isAdmin()): ?>
                            <a class="px-3 py-2 rounded-lg hover:bg-gray-700/40" href="<?= base_url('admin/index.php') ?>">Admin</a>
                        <?php endif; ?>
                        <a class="px-3 py-2 rounded-lg hover:bg-gray-700/40" href="<?= base_url('profile/index.php') ?>">Profile</a>
                        <a class="px-3 py-2 rounded-lg text-red-300 hover:bg-gray-700/40" href="<?= base_url('public/logout.php') ?>">Logout</a>
                    <?php else: ?>
                        <a class="px-3 py-2 rounded-lg hover:bg-gray-700/40" href="<?= base_url('public/login.php') ?>">Login</a>
                        <a class="px-3 py-2 rounded-lg text-blue-300 hover:bg-gray-700/40" href="<?= base_url('public/register.php') ?>">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-6xl mx-auto px-4 py-6">
        <?php if ($flash): ?>
            <div class="mb-6 rounded-xl border px-4 py-3 <?= $flash['type'] === 'success' ? 'border-emerald-500/40 bg-emerald-500/10 text-emerald-200' : ($flash['type'] === 'error' ? 'border-red-500/40 bg-red-500/10 text-red-200' : 'border-amber-500/40 bg-amber-500/10 text-amber-200') ?>">
                <?= e($flash['message']) ?>
            </div>
        <?php endif; ?>
