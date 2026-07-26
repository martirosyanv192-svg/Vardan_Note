<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'user.php';

// 1. ԱԴՄԻՆԻ ԳԱՂՏՆԱԲԱՌԸ (կարող ես փոխել այստեղ)
$ADMIN_PASSWORD = 'admin1234';
$error = '';

// 2. ԴՈՒՐՍ ԳԱԼՈՒ LՕԳԻԿԱ (Ադմինի sesson-ը ջնջելու համար)
if (isset($_GET['action']) && $_GET['action'] === 'admin_logout') {
    unset($_SESSION['is_admin_logged_in']);
    header("Location: admin.php");
    exit();
}

// 3. ԳԱՂՏՆԱԲԱՌԻ ՍՏՈՒԳՈՒՄ (POST Request)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_login'])) {
    $entered_pass = trim($_POST['admin_password']);

    if ($entered_pass === $ADMIN_PASSWORD) {
        $_SESSION['is_admin_logged_in'] = true;
        header("Location: admin.php");
        exit();
    } else {
        $error = "Սխալ ադմինիստրատիվ գաղտնաբառ:";
    }
}

// 4. ԵԹԵ ԱԴՄԻՆԸ ՄՈՒՏՔ ՉԻ ԳՈՐԾԵԼ, ՑՈՒՅՑ ՏԱԼ ՄԻԱՅՆ ԳԱՂՏՆԱԲԱՌԻ ԷՋԸ
if (!isset($_SESSION['is_admin_logged_in']) || $_SESSION['is_admin_logged_in'] !== true):
?>
<!DOCTYPE html>
<html lang="hy">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Մուտք</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }
        .admin-card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
            width: 100%;
            max-width: 400px;
            padding: 40px 30px;
        }
    </style>
</head>
<body>

<div class="admin-card text-center">
    <div class="mb-4">
        <div class="bg-primary bg-opacity-10 text-primary d-inline-block p-3 rounded-circle mb-3">
            <i class="bi bi-shield-lock-fill fs-1"></i>
        </div>
        <h4 class="fw-bold">Admin Access</h4>
        <p class="text-muted small">Մուտքագրեք գաղտնաբառը՝ Admin Panel մտնելու համար</p>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger py-2 small"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form action="admin.php" method="POST">
        <div class="mb-4">
            <input type="password" name="admin_password" class="form-control text-center py-2" placeholder="••••••••" required autofocus>
        </div>
        <button type="submit" name="admin_login" class="btn btn-primary w-100 py-2 fw-bold">Մուտք</button>
    </form>

    <div class="mt-4">
    </div>
</div>

</body>
</html>
<?php 
exit(); // Կանգնեցնում ենք կոդը, որպեսզի ներքևի աղյուսակը չերևա
endif; 

// -------------------------------------------------------------
// 5. ԵԹԵ ԳԱՂՏՆԱԲԱՌԸ ՃԻՇՏ Է, ՑՈՒՑԱԴՐՎՈՒՄ Է ԱԴՄԻՆ ՊԱՆԵԼԸ
// -------------------------------------------------------------

$userObj = new User();
$connect = $userObj->connect;

// Կարդում ենք բոլոր գրանցված օգտատերերին
$sql = "SELECT `id`, `firstName`, `lastName`, `email`, `created_at` FROM `users` ORDER BY `id` DESC";
$usersQuery = mysqli_query($connect, $sql);
$totalUsers = mysqli_num_rows($usersQuery);
?>

<!DOCTYPE html>
<html lang="hy" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NoteApp | Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg-color: #f4f6f9;
            --card-bg: #ffffff;
            --text-color: #2b2f3e;
            --border-color: #e2e8f0;
            --nav-bg: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);
            --shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            min-height: 100vh;
        }

        .navbar-custom { background: var(--nav-bg); }

        .glass-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            box-shadow: var(--shadow);
        }

        .table-custom {
            color: var(--text-color);
            vertical-align: middle;
        }

        .badge-user {
            background-color: #e0e7ff;
            color: #4338ca;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 8px;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark navbar-custom shadow-sm mb-4 py-3">
    <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center gap-2 fs-4" href="admin.php">
            <i class="bi bi-shield-lock-fill text-warning fs-3"></i> Admin Panel
        </a>
        
        <div class="d-flex align-items-center gap-3">
            <a href="index.php" class="btn btn-danger btn-sm rounded-3">
                <i class="bi bi-box-arrow-right me-1"></i> Փակել Admin-ը
            </a>
        </div>
    </div>
</nav>

<div class="container pb-5">
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="glass-card p-4 d-flex align-items-center gap-3">
                <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary fs-3">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Գրանցված օգտատերեր</h6>
                    <h3 class="fw-bold mb-0"><?php echo $totalUsers; ?></h3>
                </div>
            </div>
        </div>
    </div>

    <div class="glass-card p-4">
        <h4 class="fw-bold mb-4 d-flex align-items-center gap-2">
            <i class="bi bi-person-lines-fill text-primary"></i> Օգտատերերի Ցուցակ
        </h4>

        <div class="table-responsive">
            <table class="table table-hover table-custom">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Անուն</th>
                        <th>Ազգանուն</th>
                        <th>Էլ․ փոստ</th>
                        <th>Գրանցման ամսաթիվ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($totalUsers > 0): ?>
                        <?php while ($user = mysqli_fetch_assoc($usersQuery)): ?>
                            <tr>
                                <td><span class="badge-user">#<?php echo $user['id']; ?></span></td>
                                <td class="fw-semibold"><?php echo htmlspecialchars($user['firstName']); ?></td>
                                <td><?php echo htmlspecialchars($user['lastName']); ?></td>
                                <td>
                                    <i class="bi bi-envelope me-1 text-muted"></i>
                                    <?php echo htmlspecialchars($user['email']); ?>
                                </td>
                                <td class="small text-muted">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    <?php echo isset($user['created_at']) ? $user['created_at'] : '—'; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Գրանցված օգտատերեր չկան:</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>