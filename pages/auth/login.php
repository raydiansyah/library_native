<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/session.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: /pages/admin/dashboard.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    if (!empty($username) && !empty($password)) {
        try {
            $db = new Database();
            $conn = $db->connect();

            $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
            $stmt->execute(['username' => $username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['role'] = $user['role'];

                header('Location: /pages/admin/dashboard.php');
                exit();
            } else {
                $error = 'Username atau password salah!';
            }
        } catch (PDOException $e) {
            $error = 'Koneksi database gagal: ' . $e->getMessage();
        }
    } else {
        $error = 'Silakan isi semua field!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Library Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .input-focus:focus {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-20px);
            }
        }

        .float-animation {
            animation: float 6s ease-in-out infinite;
        }
    </style>
</head>

<body class="gradient-bg min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Floating Icon -->
        <div class="text-center mb-8 float-animation">
            <div class="inline-block bg-white rounded-full p-6 shadow-2xl">
                <span class="text-6xl">📚</span>
            </div>
        </div>

        <!-- Login Card -->
        <div class="glass-effect rounded-2xl shadow-2xl p-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-slate-800 mb-2">Library System</h1>
                <p class="text-slate-600">Silakan login untuk melanjutkan</p>
            </div>

            <?php if ($error): ?>
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded">
                    <p class="text-red-700 text-sm"><?php echo htmlspecialchars($error); ?></p>
                </div>
            <?php endif; ?>

            <form method="POST" action="" class="space-y-6">
                <div>
                    <label for="username" class="block text-sm font-medium text-slate-700 mb-2">Username</label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        required
                        class="input-focus w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all duration-300"
                        placeholder="Masukkan username">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-2">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        class="input-focus w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all duration-300"
                        placeholder="Masukkan password">
                </div>

                <button
                    type="submit"
                    class="w-full bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold py-3 rounded-lg hover:from-purple-700 hover:to-indigo-700 transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-xl">
                    Login
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-slate-600 text-sm">
                    Demo: <span class="font-semibold">admin</span> / <span class="font-semibold">admin123</span>
                </p>
            </div>
        </div>

        <p class="text-center text-white text-sm mt-6">
            &copy; <?php echo date('Y'); ?> Library Management System
        </p>
    </div>
</body>

</html>