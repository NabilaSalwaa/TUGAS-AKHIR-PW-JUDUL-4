📚 Sistem Manajemen Kontak Sederhana

📋 Deskripsi
Sistem Manajemen Kontak Sederhana adalah aplikasi web berbasis PHP yang memungkinkan pengguna untuk mengelola daftar kontak dengan fitur CRUD (Create, Read, Update, Delete) yang lengkap. Aplikasi ini dikembangkan sebagai tugas akhir modul praktikum Pemrograman Web.

✨ Fitur Utama

🔧 Fungsi Dasar
✅ Tambah Kontak - Form dengan validasi lengkap
✅ Lihat Daftar Kontak - Tampilan tabel yang terorganisir
✅ Edit Kontak - Update data kontak yang sudah ada
✅ Hapus Kontak - Hapus kontak dengan konfirmasi
✅ Session Management - Sistem login dan manajemen sesi

🚀 Fitur Tambahan
📸 Upload Foto Profil - Unggah foto untuk setiap kontak
🔍 Filter & Pencarian - Filter berdasarkan email dan nama

📁 Struktur Folder Sistem Manajemen Kontak
sistem-manajemen-kontak/
│
├── 📄 index.php                 # Halaman utama/landing page
├── 🔐 login.php                 # Halaman login user
├── 📝 register.php              # Halaman registrasi user baru
├── 🏠 dashboard.php             # Dashboard utama setelah login
│
├── 👥 add_contact.php           # Form tambah kontak baru
├── ✏️ edit_contact.php          # Form edit kontak yang ada
├── 🗑️ delete_contact.php        # Proses hapus kontak
│
├── 👤 update_user.php           # Update profil user
├── 🔄 reset_user.php            # Reset data user
├── 🚪 logout.php                # Proses logout user
│
├── 📁 uploads/                  # Folder penyimpanan file upload
│   ├── 🖼️ profile/              # Foto profil kontak (jika ada)
│   
├── 📄 contacts_salwa001.txt     # Database kontak (format: ID|nama|email|telepon|alamat|foto)
├── 📄 users.txt                 # Database user (format: username|password|email|role)
│
└── 📚 README.md                 # Dokumentasi sistem
