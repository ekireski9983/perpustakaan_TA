<?php
session_start();
require 'koneksi.php';

$identifier = mysqli_real_escape_string($conn, $_POST['identifier']); // This will accept either username or ID
$password = mysqli_real_escape_string($conn, $_POST['password']);
$role     = mysqli_real_escape_string($conn, $_POST['role']);

// Cek kombinasi ID/username, password, dan role
$query = "SELECT * FROM users WHERE (username='$identifier' OR id='$identifier') AND password='$password' AND role='$role'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) === 1) {
    $_SESSION['username'] = $identifier; // Store the identifier (username or ID)
    $_SESSION['role']     = $role;

    // Redirect ke dashboard umum atau ke halaman berbeda berdasarkan role
    if ($role === 'admin') {
        header("Location: dashboard_admin.php");
    } else {
        header("Location: dashboard_user.php");
    }
    exit;
} else {
    echo "<script>alert('Login gagal. Cek username, password, atau role.'); window.location='login.php';</script>";
}
?>
