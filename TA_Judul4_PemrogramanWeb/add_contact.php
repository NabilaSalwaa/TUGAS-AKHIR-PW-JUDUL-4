<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$errors = [];
$data = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validasi
    if (empty($_POST["name"])) {
        $errors[] = "Nama harus diisi";
    } else {
        $data['name'] = trim($_POST["name"]);
        if (!preg_match("/^[a-zA-Z\s]+$/", $data['name'])) {
            $errors[] = "Nama hanya boleh mengandung huruf dan spasi";
        }
    }

    if (empty($_POST["email"])) {
        $errors[] = "Email harus diisi";
    } else {
        $data['email'] = trim($_POST["email"]);
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Format email tidak valid";
        }
    }

    if (empty($_POST["phone"])) {
        $errors[] = "Telepon harus diisi";
    } else {
        $data['phone'] = trim($_POST["phone"]);
        if (!preg_match("/^[0-9+\-\s]+$/", $data['phone'])) {
            $errors[] = "Format telepon tidak valid";
        }
    }

    // Handle photo upload
    $photo_path = '';
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $max_size = 5 * 1024 * 1024; // 5MB
        
        if (!in_array($_FILES['photo']['type'], $allowed_types)) {
            $errors[] = "Format foto harus JPG, JPEG, PNG, atau GIF";
        } elseif ($_FILES['photo']['size'] > $max_size) {
            $errors[] = "Ukuran foto maksimal 5MB";
        } else {
            $upload_dir = 'uploads/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
            $new_filename = uniqid('contact_') . '.' . $file_extension;
            $photo_path = $upload_dir . $new_filename;
            
            if (!move_uploaded_file($_FILES['photo']['tmp_name'], $photo_path)) {
                $errors[] = "Gagal mengupload foto";
                $photo_path = '';
            }
        }
    }

    // Simpan kontak
    if (empty($errors)) {
        $contacts = [];
        $contacts_file = 'contacts_' . $_SESSION['user_id'] . '.txt';
        if (file_exists($contacts_file)) {
            $contacts_data = file_get_contents($contacts_file);
            $contacts = unserialize($contacts_data);
        }
        
        $new_id = uniqid();
        $contacts[$new_id] = [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'photo' => $photo_path,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        file_put_contents($contacts_file, serialize($contacts));
        header("Location: dashboard.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Kontak - Sistem Manajemen Kontak</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <!-- Header -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <a href="dashboard.php" class="bg-blue-500 hover:bg-blue-600 p-3 rounded-xl transition duration-200">
                        <i class="fas fa-arrow-left text-white text-lg"></i>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Tambah Kontak Baru</h1>
                        <p class="text-gray-600 text-sm">Tambahkan kontak ke daftar Anda</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-right">
                        <p class="text-sm font-medium text-gray-700"><?php echo htmlspecialchars($_SESSION['username']); ?></p>
                        <p class="text-xs text-gray-500"><?php echo $_SESSION['login_time']; ?></p>
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
    <div class="max-w-4xl mx-auto px-4 py-8">
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <div class="flex items-center space-x-4 mb-8">
                <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-4 rounded-2xl shadow-lg">
                    <i class="fas fa-user-plus text-white text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-3xl font-bold text-gray-800">Form Tambah Kontak</h2>
                    <p class="text-gray-600 text-lg">Isi informasi kontak di bawah ini dengan lengkap</p>
                </div>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 px-6 py-4 rounded-xl mb-8 shadow-md">
                    <h3 class="font-bold text-lg mb-3 flex items-center">
                        <i class="fas fa-exclamation-triangle mr-3 text-xl"></i>Perhatian:
                    </h3>
                    <ul class="list-disc list-inside space-y-2 text-lg">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
                <div class="space-y-6">
                    <!-- Nama Field -->
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 rounded-2xl border border-blue-100">
                        <label class="block text-gray-700 text-xl font-bold mb-4 flex items-center">
                            <i class="fas fa-user text-blue-500 mr-3"></i>
                            Nama Lengkap *
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-id-card text-blue-400 text-lg"></i>
                            </div>
                            <input type="text" name="name" required 
                                   value="<?php echo isset($data['name']) ? htmlspecialchars($data['name']) : ''; ?>"
                                   class="w-full pl-12 pr-4 py-4 text-lg border-2 border-blue-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                                   placeholder="Contoh: John Doe">
                        </div>
                    </div>

                    <!-- Email Field -->
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 rounded-2xl border border-blue-100">
                        <label class="block text-gray-700 text-xl font-bold mb-4 flex items-center">
                            <i class="fas fa-envelope text-blue-500 mr-3"></i>
                            Alamat Email *
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-at text-blue-400 text-lg"></i>
                            </div>
                            <input type="email" name="email" required 
                                   value="<?php echo isset($data['email']) ? htmlspecialchars($data['email']) : ''; ?>"
                                   class="w-full pl-12 pr-4 py-4 text-lg border-2 border-blue-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                                   placeholder="Contoh: john@example.com">
                        </div>
                    </div>

                    <!-- Telepon Field -->
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 rounded-2xl border border-blue-100">
                        <label class="block text-gray-700 text-xl font-bold mb-4 flex items-center">
                            <i class="fas fa-phone text-blue-500 mr-3"></i>
                            Nomor Telepon *
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-mobile-alt text-blue-400 text-lg"></i>
                            </div>
                            <input type="tel" name="phone" required 
                                   value="<?php echo isset($data['phone']) ? htmlspecialchars($data['phone']) : ''; ?>"
                                   class="w-full pl-12 pr-4 py-4 text-lg border-2 border-blue-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                                   placeholder="Contoh: 08123456789">
                        </div>
                    </div>

                    <!-- Photo Upload Field -->
                    <div class="bg-gradient-to-r from-purple-50 to-pink-50 p-6 rounded-2xl border border-purple-100">
                        <label class="block text-gray-700 text-xl font-bold mb-4 flex items-center">
                            <i class="fas fa-camera text-purple-500 mr-3"></i>
                            Foto Kontak (Opsional)
                        </label>
                        <div class="space-y-4">
                            <div class="flex items-center justify-center w-full">
                                <label class="flex flex-col items-center justify-center w-full h-48 border-2 border-purple-300 border-dashed rounded-xl cursor-pointer bg-white hover:bg-purple-50 transition duration-200">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <i class="fas fa-cloud-upload-alt text-purple-400 text-4xl mb-3"></i>
                                        <p class="mb-2 text-sm text-gray-600"><span class="font-semibold">Click to upload</span> atau drag and drop</p>
                                        <p class="text-xs text-gray-500">PNG, JPG, JPEG atau GIF (MAX. 5MB)</p>
                                    </div>
                                    <input id="photo-upload" type="file" name="photo" accept="image/*" class="hidden" onchange="previewImage(event)" />
                                </label>
                            </div>
                            <div id="preview-container" class="hidden">
                                <img id="preview-image" class="w-32 h-32 rounded-full object-cover mx-auto border-4 border-purple-200" />
                                <p class="text-center text-sm text-gray-600 mt-2" id="preview-name"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-between mt-12 pt-8 border-t border-gray-200">
                    <a href="dashboard.php" class="bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white font-bold py-4 px-8 rounded-xl transition duration-200 flex items-center space-x-3 shadow-lg text-lg">
                        <i class="fas fa-arrow-left"></i>
                        <span>Kembali ke Dashboard</span>
                    </a>
                    <button type="submit" class="bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-bold py-4 px-8 rounded-xl transition duration-200 flex items-center space-x-3 shadow-lg text-lg">
                        <i class="fas fa-save"></i>
                        <span>Simpan Kontak Baru</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview-image').src = e.target.result;
                    document.getElementById('preview-name').textContent = file.name;
                    document.getElementById('preview-container').classList.remove('hidden');
                }
                reader.readAsDataURL(file);
            }
        }
    </script>
</body>
</html>