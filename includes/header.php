<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/session.php';
requireLogin();
$user = getUserData();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>Library Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .sidebar-link {
            transition: all 0.3s ease;
        }

        .sidebar-link:hover {
            transform: translateX(4px);
        }

        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        /* Mobile sidebar transitions */
        .sidebar {
            transition: transform 0.3s ease-in-out;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }
        }

        /* Hamburger menu animation */
        .hamburger span {
            transition: all 0.3s ease;
        }

        .hamburger.active span:nth-child(1) {
            transform: rotate(45deg) translate(5px, 5px);
        }

        .hamburger.active span:nth-child(2) {
            opacity: 0;
        }

        .hamburger.active span:nth-child(3) {
            transform: rotate(-45deg) translate(7px, -6px);
        }
    </style>
</head>

<body class="bg-gradient-to-br from-slate-50 to-slate-100 min-h-screen">
    <div class="flex h-screen overflow-hidden">
        <!-- Mobile Overlay -->
        <div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 z-20 hidden md:hidden"></div>

        <!-- Sidebar -->
        <aside id="sidebar" class="sidebar fixed md:static inset-y-0 left-0 z-30 w-64 bg-gradient-to-b from-slate-900 to-slate-800 text-white flex-shrink-0 shadow-2xl">
            <div class="flex flex-col h-full">
                <!-- Sidebar Header -->
                <div class="p-6 border-b border-slate-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold bg-gradient-to-r from-blue-400 to-cyan-300 bg-clip-text text-transparent">
                                📚 Library System
                            </h1>
                            <p class="text-slate-400 text-sm mt-1">Management Dashboard</p>
                        </div>
                        <!-- Close button for mobile -->
                        <button id="closeSidebar" class="md:hidden text-slate-400 hover:text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
                    <a href="/pages/admin/dashboard.php" class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-slate-700/50 <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'bg-slate-700' : ''; ?>">
                        <span class="text-xl">📊</span>
                        <span>Dashboard</span>
                    </a>
                    <a href="/pages/admin/books.php" class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-slate-700/50 <?php echo basename($_SERVER['PHP_SELF']) == 'books.php' ? 'bg-slate-700' : ''; ?>">
                        <span class="text-xl">📖</span>
                        <span>Buku</span>
                    </a>
                    <a href="/pages/admin/members.php" class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-slate-700/50 <?php echo basename($_SERVER['PHP_SELF']) == 'members.php' ? 'bg-slate-700' : ''; ?>">
                        <span class="text-xl">👥</span>
                        <span>Anggota</span>
                    </a>
                    <a href="/pages/admin/transactions.php" class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-slate-700/50 <?php echo basename($_SERVER['PHP_SELF']) == 'transactions.php' ? 'bg-slate-700' : ''; ?>">
                        <span class="text-xl">📝</span>
                        <span>Transaksi Peminjaman</span>
                    </a>
                </nav>

                <!-- User Profile & Logout -->
                <div class="p-4 border-t border-slate-700">
                    <div class="flex items-center space-x-3 mb-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-cyan-300 flex items-center justify-center text-slate-900 font-bold flex-shrink-0">
                            <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-sm truncate"><?php echo htmlspecialchars($user['name']); ?></p>
                            <p class="text-slate-400 text-xs truncate"><?php echo htmlspecialchars($user['role']); ?></p>
                        </div>
                    </div>
                    <a href="/pages/auth/logout.php" class="sidebar-link flex items-center justify-center space-x-2 px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white w-full">
                        <span>🚪</span>
                        <span>Logout</span>
                    </a>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Bar -->
            <header class="bg-white shadow-md z-10">
                <div class="px-4 md:px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <!-- Hamburger Menu Button -->
                        <button id="menuToggle" class="hamburger md:hidden text-slate-600 hover:text-slate-900 focus:outline-none">
                            <div class="w-6 h-5 flex flex-col justify-between">
                                <span class="w-full h-0.5 bg-current block"></span>
                                <span class="w-full h-0.5 bg-current block"></span>
                                <span class="w-full h-0.5 bg-current block"></span>
                            </div>
                        </button>
                        <h2 class="text-xl md:text-2xl font-bold text-slate-800"><?php echo isset($pageTitle) ? $pageTitle : 'Dashboard'; ?></h2>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="hidden sm:block text-slate-600 text-xs md:text-sm">
                            <?php echo date('l, d F Y'); ?>
                        </span>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-4 md:p-6">
                <script>
                    // Mobile menu toggle functionality
                    document.addEventListener('DOMContentLoaded', function() {
                        const menuToggle = document.getElementById('menuToggle');
                        const sidebar = document.getElementById('sidebar');
                        const overlay = document.getElementById('overlay');
                        const closeSidebar = document.getElementById('closeSidebar');

                        function openSidebar() {
                            sidebar.classList.add('active');
                            overlay.classList.remove('hidden');
                            menuToggle.classList.add('active');
                            document.body.style.overflow = 'hidden';
                        }

                        function closeSidebarFunc() {
                            sidebar.classList.remove('active');
                            overlay.classList.add('hidden');
                            menuToggle.classList.remove('active');
                            document.body.style.overflow = '';
                        }

                        menuToggle.addEventListener('click', function() {
                            if (sidebar.classList.contains('active')) {
                                closeSidebarFunc();
                            } else {
                                openSidebar();
                            }
                        });

                        overlay.addEventListener('click', closeSidebarFunc);
                        closeSidebar.addEventListener('click', closeSidebarFunc);

                        // Close sidebar when clicking on a link (mobile only)
                        const sidebarLinks = sidebar.querySelectorAll('a');
                        sidebarLinks.forEach(link => {
                            link.addEventListener('click', function() {
                                if (window.innerWidth < 768) {
                                    closeSidebarFunc();
                                }
                            });
                        });

                        // Handle window resize
                        window.addEventListener('resize', function() {
                            if (window.innerWidth >= 768) {
                                closeSidebarFunc();
                            }
                        });
                    });
                </script>