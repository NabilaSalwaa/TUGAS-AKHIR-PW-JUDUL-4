<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$contact_id = $_GET['id'];
$contacts_file = 'contacts_' . $_SESSION['user_id'] . '.txt';

// Load dan hapus kontak
if (file_exists($contacts_file)) {
    $contacts_data = file_get_contents($contacts_file);
    $contacts = unserialize($contacts_data);
    
    if (isset($contacts[$contact_id])) {
        unset($contacts[$contact_id]);
        file_put_contents($contacts_file, serialize($contacts));
    }
}

header("Location: dashboard.php");
exit();
?>