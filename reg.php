<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'user.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $userObj = new User();
    
    // Բազայի միացումը վերցնելը
    $connect = isset($userObj->connect) ? $userObj->connect : null;

    if (!$connect) {
        $connect = mysqli_connect("localhost", "root", "root", "lesson4");
    }

    // ?? '' ապահովում է, որ եթե դաշտը չկա, null-ի փոխարեն լինի դատարկ տեքստ
    $firstName = mysqli_real_escape_string($connect, trim($_POST['firstName'] ?? ''));
    $lastName  = mysqli_real_escape_string($connect, trim($_POST['lastName'] ?? ''));
    $email     = mysqli_real_escape_string($connect, trim($_POST['email'] ?? ''));
    $password  = trim($_POST['password'] ?? '');

    if (!empty($firstName) && !empty($lastName) && !empty($email) && !empty($password)) {
        // Ստուգում ենք՝ արդյոք էլ․ փոստը արդեն գրանցված է
        $checkEmail = mysqli_query($connect, "SELECT * FROM `users` WHERE `email` = '$email'");
        
        if ($checkEmail && mysqli_num_rows($checkEmail) > 0) {
            $error = "Այս էլ․ փոստով օգտատեր արդեն գրանցված է:";
        } else {
            // Գաղտնաբառի հեշավորում
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO `users` (`firstName`, `lastName`, `email`, `password`) 
                    VALUES ('$firstName', '$lastName', '$email', '$hashedPassword')";

            if (mysqli_query($connect, $sql)) {
                header("Location: login.php");
                exit();
            } else {
                $error = "Գրանցման սխալ տեղի ունեցավ: " . mysqli_error($connect);
            }
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
    <title>NoteApp | Գրանցում</title>
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
            padding: 20px 0;
        }
        .auth-card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 450px;
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
        <p class="text-muted small">Ստեղծեք նոր հաշիվ</p>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger py-2 small text-center"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form action="reg.php" method="POST" autocomplete="off">
        <div class="mb-3">
            <label class="form-label small fw-semibold text-secondary">Անուն</label>
            <input type="text" name="firstName" class="form-control" placeholder="Անուն" required>
        </div>

        <div class="mb-3">
            <label class="form-label small fw-semibold text-secondary">Ազգանուն</label>
            <input type="text" name="lastName" class="form-control" placeholder="Ազգանուն" required>
        </div>

        <div class="mb-3">
            <label class="form-label small fw-semibold text-secondary">Էլ. փոստ</label>
            <input type="email" name="email" class="form-control" placeholder="example@mail.com" required>
        </div>

        <div class="mb-4">
            <label class="form-label small fw-semibold text-secondary">Գաղտնաբառ</label>
            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
        </div>

        <button type="submit" name="register" class="btn btn-primary w-100 py-2 fw-bold">Գրանցվել</button>
    </form>

    <div class="text-center mt-4">
        <small class="text-muted">Արդեն ունե՞ք հաշիվ: <a href="login.php" class="text-primary fw-bold text-decoration-none">Մուտք գործել</a></small>
    </div>
</div>

</body>
</html>