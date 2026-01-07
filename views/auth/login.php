<?php

session_start();
require_once '../../config/database.php';

if (isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username = :username LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && $password == $user['password']) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nama'] = $user['nama'];
        $_SESSION['role'] = $user['role'];

        header("Location: ../../index.php");
        exit();
    } else {
        $error = "Username atau Password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Sewa Motor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; display: flex; align-items: center; min-height: 100vh; }
        .login-card { border: none; border-radius: 1.2rem; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="text-center mb-4">
                <i class="bi bi-bicycle text-primary" style="font-size: 3rem;"></i>
                <h3 class="fw-bold">Sewa Motor</h3>
            </div>
            <div class="card login-card p-4">
                <div class="card-body">
                    <h5 class="mb-4 fw-bold">Login Admin</h5>
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger small"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">USERNAME</label>
                            <input type="text" name="username" class="form-control bg-light border-0 py-2" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-bold">PASSWORD</label>
                            <input type="password" name="password" class="form-control bg-light border-0 py-2" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2 rounded-3 fw-bold">MASUK</button>
                    </form>
                </div>
            </div>
            <p class="text-center text-muted mt-4 small">&copy; 2026 Universitas Pelita Bangsa</p>
        </div>
    </div>
</div>
</body>
</html>