<?php
session_start();
require 'koneksi.php';

$identifier = mysqli_real_escape_string($conn, $_POST['identifier']); 
$password = mysqli_real_escape_string($conn, $_POST['password']);
$role     = mysqli_real_escape_string($conn, $_POST['role']);


$query = "SELECT * FROM users WHERE (username='$identifier' OR id='$identifier') AND password='$password' AND role='$role'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) === 1) {
    $_SESSION['username'] = $identifier; 
    $_SESSION['role']     = $role;

    
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
