<?php
session_start();
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: dashboard.php");
    exit();
}

// Auto-create admin user
if (!file_exists('users.txt')) {
    $users = [
        'nabila salwa' => [
            'id' => 'admin001',
            'username' => 'nabila salwa',
            'email' => 'admin@example.com',
            'password' => password_hash('salwa123', PASSWORD_DEFAULT),
            'created_at' => date('Y-m-d H:i:s')
        ]
    ];
    file_put_contents('users.txt', serialize($users));
}

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    if (empty($username) || empty($password)) {
        $error = "Username dan password harus diisi!";
    } else {
        $users = [];
        if (file_exists('users.txt')) {
            $users_data = file_get_contents('users.txt');
            $users = unserialize($users_data);
        }
        
        if (isset($users[$username]) && password_verify($password, $users[$username]['password'])) {
            $_SESSION['logged_in'] = true;
            $_SESSION['username'] = $username;
            $_SESSION['user_id'] = $users[$username]['id'];
            $_SESSION['login_time'] = date('Y-m-d H:i:s');
            
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Username atau password salah!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Manajemen Kontak</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-500 to-purple-600 min-h-screen flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-md">
        <div class="text-center mb-8">
            <div class="bg-blue-500 w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-address-book text-white text-2xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-800">ContactManager</h1>
            <p class="text-gray-600 mt-2">Masuk ke akun Anda</p>
        </div>
        
        <?php if ($error): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center">
                <i class="fas fa-exclamation-triangle mr-3"></i>
                <span><?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Username</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-user text-gray-400"></i>
                    </div>
                    <input type="text" name="username" required 
                           class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Masukkan username">
                </div>
            </div>
            
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-lock text-gray-400"></i>
                    </div>
                    <input type="password" name="password" required 
                           class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Masukkan password">
                </div>
            </div>
            
            <button type="submit" class="w-full bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200 shadow-lg">
                <i class="fas fa-sign-in-alt mr-2"></i>Masuk
            </button>
        </form>
        
        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
            <p class="text-sm text-gray-600 text-center">
                <strong>Akun Demo:</strong><br>
                Username: <span class="font-mono">nabila salwa</span><br>
                Password: <span class="font-mono">salwa123</span>
            </p>
        </div>
    </div>
</body>
</html>