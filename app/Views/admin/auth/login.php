<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - UpWatch</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
    </style>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        gray: {
                            950: '#0a0a0a',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-900 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full px-4">
        <div class="bg-gray-800/50 border border-white/10 rounded-lg shadow-xl p-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-indigo-400 mb-2">UpWatch</h1>
                <p class="text-gray-400">Admin Dashboard</p>
            </div>

            <?php if (session()->has('error')): ?>
            <div class="bg-red-900/50 border border-red-700 text-red-300 px-4 py-3 rounded-lg mb-4">
                <?= session('error') ?>
            </div>
            <?php endif; ?>

            <form method="post" action="/admin/login">
                <?= csrf_field() ?>
                
                <div class="mb-4">
                    <label class="block text-gray-300 font-medium mb-2">Email</label>
                    <input type="email" 
                           name="email" 
                           required
                           class="w-full px-4 py-2 bg-gray-900 border border-white/10 text-white rounded-lg focus:outline-none focus:outline-none focus:border-indigo-500">
                </div>

                <div class="mb-6">
                    <label class="block text-gray-300 font-medium mb-2">Password</label>
                    <input type="password" 
                           name="password" 
                           required
                           class="w-full px-4 py-2 bg-gray-900 border border-white/10 text-white rounded-lg focus:outline-none focus:outline-none focus:border-indigo-500">
                </div>

                <button type="submit" 
                        class="w-full bg-indigo-600 text-white py-2.5 rounded-lg hover:bg-indigo-500 transition font-medium">
                    Sign in
                </button>
            </form>

            <div class="mt-6 text-center text-sm text-gray-400">
                <p>Default: admin@upwatch.local / admin123</p>
            </div>
        </div>

        <div class="text-center mt-6 text-gray-400">
            <a href="/" class="hover:text-indigo-400 transition">← Back to Status Page</a>
        </div>
    </div>
</body>
</html>
