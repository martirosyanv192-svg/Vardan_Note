<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// user.php-ն է միանում բազային
require_once 'user.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $userObj = new User();
    $connect = $userObj->connect;

    $email    = mysqli_real_escape_string($connect, trim($_POST['email']));
    $password = trim($_POST['password']);

    if (!empty($email) && !empty($password)) {
        $sql = "SELECT * FROM `users` WHERE `email` = '$email'";
        $result = mysqli_query($connect, $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                
                // Վերցնում ենք անունը (անախորժություններից խուսափելու համար)
                if (!empty($user['name'])) {
                    $_SESSION['user_name'] = $user['name'];
                } elseif (!empty($user['first_name'])) {
                    $_SESSION['user_name'] = $user['first_name'];
                } else {
                    $_SESSION['user_name'] = 'Օգտատեր';
                }
                
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Սխալ գաղտնաբառ:";
            }
        } else {
            $error = "Այս էլ. փոստով օգտատեր չի գտնվել:";
        }
    } else {
        $error = "Խնդրում ենք լրացնել բոլոր դաշտերը:";
    }
}
?>

<!DOCTYPE html>
<html lang="hy">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NoteApp | Մուտք</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }
        .auth-card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 420px;
            padding: 40px 30px;
        }
    </style>
</head>
<body>

<div class="auth-card">
    <div class="text-center mb-4">
        <a href="index.php" class="text-decoration-none text-dark">
            <h3 class="fw-bold"><i class="bi bi-journal-check text-primary me-2"></i>NoteApp</h3>
        </a>
        <p class="text-muted small">Մուտք գործեք ձեր հաշիվ</p>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger py-2 small text-center"><?= $error ?></div>
    <?php endif; ?>

    <form action="login.php" method="POST" autocomplete="off">
        <input type="text" style="display:none;" autocomplete="false">
        <input type="password" style="display:none;" autocomplete="false">

        <div class="mb-3">
            <label class="form-label small fw-semibold text-secondary">Էլ. փոստ</label>
            <input type="email" name="email" class="form-control" placeholder="example@mail.com" autocomplete="one-time-code" required>
        </div>

        <div class="mb-4">
            <label class="form-label small fw-semibold text-secondary">Գաղտնաբառ</label>
            <input type="password" name="password" class="form-control" placeholder="••••••••" autocomplete="new-password" required>
        </div>

        <button type="submit" name="login" class="btn btn-primary w-100 py-2 fw-bold">Մուտք</button>
    </form>

    <div class="text-center mt-4">
        <small class="text-muted">Չունե՞ք հաշիվ: <a href="reg.php" class="text-primary fw-bold text-decoration-none">Գրանցվել</a></small>
    </div>
</div>

</body>
</html>