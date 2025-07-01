<?php
session_start();
if (isset($_SESSION['username'])) {
    // Redirect to the appropriate dashboard based on the role
    if ($_SESSION['role'] === 'admin') {
        header("Location: dashboard_admin.php");
    } else {
        header("Location: dashboard_user.php");
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login Page</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .login-container {
            max-width: 420px;
            width: 100%;
        }

        .login-card {
            border-radius: 1rem;
        }

        .login-title {
            font-size: 1.8rem;
            font-weight: bold;
        }

        img {
            width: 120px;
            height: auto;
        }
    </style>
</head>
<body class="bg-light">

<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card login-container shadow-lg p-4 login-card">
        <div class="text-center mb-4">
            <img src="assets/logo_sekolah.png" alt="Logo">
        </div>
        <h4 class="text-center login-title mb-4">Login</h4>

        <form action="auth.php" method="POST">
            <div class="mb-3">
                <label for="identifier" class="form-label">Username</label>
                <input type="text" name="identifier" class="form-control" id="identifier" placeholder="Masukkan username" required autofocus>
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" class="form-control" id="password" placeholder="Masukkan password" required>
            </div>

            <div class="mb-4 w-50">
                <label for="role" class="form-label">Pilih level</label>
                <select class="form-select" name="role" id="role" required>
                    <option value="" selected disabled>Pilih level</option>
                    <option value="admin">Admin</option>
                    <option value="user">User </option>
                </select>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Login</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>
