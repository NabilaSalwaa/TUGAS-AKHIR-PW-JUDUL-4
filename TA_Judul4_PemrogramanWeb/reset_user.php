<?php
// Script untuk reset ke default
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

// Rename file kontak kembali jika ada
if (file_exists('contacts_salwa001.txt')) {
    rename('contacts_salwa001.txt', 'contacts_admin001.txt');
}

echo "User berhasil diupdate!<br>";
echo "Username: nabila salwa<br>";
echo "Password: salwa123<br>";
echo "User ID: admin001<br>";
echo "<br><a href='login.php'>Ke halaman login</a>";
?>
