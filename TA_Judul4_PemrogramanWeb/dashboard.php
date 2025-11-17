<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Load contacts dari file
$contacts = [];
$contacts_file = 'contacts_' . $_SESSION['user_id'] . '.txt';
if (file_exists($contacts_file)) {
    $contacts_data = file_get_contents($contacts_file);
    $contacts = unserialize($contacts_data);
    if ($contacts === false) $contacts = [];
}

// Search/Filter functionality
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filtered_contacts = $contacts;

if (!empty($search)) {
    $filtered_contacts = array_filter($contacts, function($contact) use ($search) {
        $search_lower = strtolower($search);
        return stripos(strtolower($contact['name']), $search_lower) !== false ||
               stripos(strtolower($contact['email']), $search_lower) !== false ||
               stripos(strtolower($contact['phone']), $search_lower) !== false;
    });
}

// Set waktu real-time
$current_time = date('Y-m-d H:i:s');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistem Manajemen Kontak</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script>
        // Function untuk update waktu real-time
        function updateRealTime() {
            const now = new Date();
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            
            const currentTime = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
            
            // Update last updated time
            const lastUpdatedElement = document.getElementById('last-updated');
            if (lastUpdatedElement) {
                lastUpdatedElement.textContent = `Last updated: ${currentTime}`;
            }
            
            // Update login time di header
            const loginTimeElement = document.getElementById('login-time');
            if (loginTimeElement) {
                loginTimeElement.textContent = currentTime;
            }
            
            // Update login time di system info
            const systemLoginTimeElement = document.getElementById('system-login-time');
            if (systemLoginTimeElement) {
                systemLoginTimeElement.textContent = currentTime;
            }
        }

        // Update waktu setiap detik
        setInterval(updateRealTime, 1000);

        // Jalankan sekali saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            updateRealTime();
        });
    </script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex flex-col">
    <!-- Header -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <div class="bg-blue-500 p-2 rounded-lg">
                        <i class="fas fa-address-book text-white text-xl"></i>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-800">ContactManager</h1>
                </div>
                
                <div class="flex items-center space-x-4">
                    <div class="text-right">
                        <p class="text-sm font-medium text-gray-700"><?php echo htmlspecialchars($_SESSION['username']); ?></p>
                        <p class="text-xs text-gray-500" id="login-time"><?php echo $current_time; ?></p>
                    </div>
                    <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                        <span class="text-white font-bold text-sm"><?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?></span>
                    </div>
                    <a href="logout.php" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg font-medium transition duration-200 flex items-center space-x-2">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 py-8 flex-1">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Kontak</p>
                        <p class="text-3xl font-bold text-gray-800"><?php echo count($contacts); ?></p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-users text-blue-500 text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">User Aktif</p>
                        <p class="text-3xl font-bold text-gray-800">1</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-user-check text-green-500 text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Status</p>
                        <p class="text-lg font-bold text-purple-600">Online</p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-full">
                        <i class="fas fa-wifi text-purple-500 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
            <form method="GET" action="dashboard.php" class="flex gap-4">
                <div class="flex-1 relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400 text-lg"></i>
                    </div>
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                           class="w-full pl-12 pr-4 py-3 text-lg border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                           placeholder="Cari kontak berdasarkan nama, email, atau telepon...">
                </div>
                <button type="submit" class="bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white px-8 py-3 rounded-xl font-semibold transition duration-200 flex items-center space-x-2 shadow-lg">
                    <i class="fas fa-search"></i>
                    <span>Cari</span>
                </button>
                <?php if (!empty($search)): ?>
                    <a href="dashboard.php" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-xl font-semibold transition duration-200 flex items-center space-x-2 shadow-lg">
                        <i class="fas fa-times"></i>
                        <span>Reset</span>
                    </a>
                <?php endif; ?>
            </form>
            <?php if (!empty($search)): ?>
                <div class="mt-4 text-sm text-gray-600">
                    <i class="fas fa-info-circle mr-2"></i>
                    Menampilkan <span class="font-semibold"><?php echo count($filtered_contacts); ?></span> hasil dari <span class="font-semibold"><?php echo count($contacts); ?></span> kontak untuk pencarian: <span class="font-semibold text-blue-600">"<?php echo htmlspecialchars($search); ?>"</span>
                </div>
            <?php endif; ?>
        </div>

        <!-- Main Card -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-8">
            <!-- Card Header -->
            <div class="bg-gradient-to-r from-blue-600 to-purple-700 px-6 py-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-white">Daftar Kontak</h2>
                        <p class="text-blue-100 mt-1">Kelola semua kontak Anda di satu tempat</p>
                    </div>
                    <a href="add_contact.php" class="mt-4 md:mt-0 bg-white text-blue-600 hover:bg-blue-50 px-6 py-3 rounded-lg font-semibold transition duration-200 flex items-center space-x-2 shadow-lg">
                        <i class="fas fa-plus-circle"></i>
                        <span>Tambah Kontak Baru</span>
                    </a>
                </div>
            </div>

            <!-- Card Body -->
            <div class="p-6">
                <?php if (empty($filtered_contacts)): ?>
                    <?php if (!empty($search)): ?>
                        <!-- No Search Results -->
                        <div class="text-center py-12">
                            <div class="bg-gray-100 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-search text-gray-400 text-3xl"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-700 mb-2">Tidak ada hasil yang ditemukan</h3>
                            <p class="text-gray-500 mb-6">Coba kata kunci pencarian yang berbeda</p>
                            <a href="dashboard.php" class="bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white px-8 py-3 rounded-lg font-semibold inline-flex items-center space-x-2 transition duration-200 shadow-lg">
                                <i class="fas fa-arrow-left"></i>
                                <span>Kembali ke Semua Kontak</span>
                            </a>
                        </div>
                    <?php elseif (empty($contacts)): ?>
                    <!-- Empty State -->
                    <div class="text-center py-12">
                        <div class="bg-gray-100 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-address-book text-gray-400 text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-700 mb-2">Belum ada kontak</h3>
                        <p class="text-gray-500 mb-6">Mulai dengan menambahkan kontak pertama Anda</p>
                        <a href="add_contact.php" class="bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white px-8 py-3 rounded-lg font-semibold inline-flex items-center space-x-2 transition duration-200 shadow-lg">
                            <i class="fas fa-plus"></i>
                            <span>Tambah Kontak Pertama</span>
                        </a>
                    </div>
                    <?php endif; ?>
                <?php else: ?>
                    <!-- Contacts Table -->
                    <div class="overflow-x-auto rounded-lg border border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Telepon</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($filtered_contacts as $id => $contact): ?>
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <?php if (!empty($contact['photo']) && file_exists($contact['photo'])): ?>
                                                <img src="<?php echo htmlspecialchars($contact['photo']); ?>" alt="<?php echo htmlspecialchars($contact['name']); ?>" class="w-12 h-12 rounded-full object-cover mr-3 border-2 border-blue-200">
                                            <?php else: ?>
                                                <div class="bg-blue-100 w-12 h-12 rounded-full flex items-center justify-center mr-3">
                                                    <i class="fas fa-user text-blue-600"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($contact['name']); ?></div>
                                                <div class="text-xs text-gray-500">Added: <?php echo date('d M Y', strtotime($contact['created_at'])); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900"><?php echo htmlspecialchars($contact['email']); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900"><?php echo htmlspecialchars($contact['phone']); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="edit_contact.php?id=<?php echo $id; ?>" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center space-x-2">
                                                <i class="fas fa-edit text-xs"></i>
                                                <span>Edit</span>
                                            </a>
                                            <a href="delete_contact.php?id=<?php echo $id; ?>" onclick="return confirm('Hapus kontak <?php echo addslashes($contact['name']); ?>?')" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center space-x-2">
                                                <i class="fas fa-trash text-xs"></i>
                                                <span>Hapus</span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Footer Stats -->
                    <div class="mt-6 flex justify-between items-center text-sm text-gray-600">
                        <div class="flex items-center space-x-4">
                            <span class="flex items-center space-x-1">
                                <i class="fas fa-info-circle"></i>
                                <span>Menampilkan: <?php echo count($filtered_contacts); ?> kontak<?php if (!empty($search)) echo ' (dari ' . count($contacts) . ' total)'; ?></span>
                            </span>
                        </div>
                        <div class="text-xs text-gray-500" id="last-updated">
                            Last updated: <?php echo $current_time; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- System Info Only -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">System Info</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-gray-600">User ID</span>
                    <span class="font-mono text-sm bg-gray-100 px-2 py-1 rounded"><?php echo $_SESSION['user_id']; ?></span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-gray-600">Login Time</span>
                    <span class="text-sm" id="system-login-time"><?php echo $current_time; ?></span>
                </div>
                <div class="flex justify-between items-center py-2">
                    <span class="text-gray-600">Session Status</span>
                    <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Active</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-auto">
        <div class="max-w-7xl mx-auto px-4 py-6">
            <div class="text-center text-gray-600">
                <p>&copy; 2025 ContactManager. Sistem Management Kontak Sederhana.</p>
            </div>
        </div>
    </footer>
</body>
</html>