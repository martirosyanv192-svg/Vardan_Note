<?php
session_start();
// Կանխում ենք cache-ավորումը
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$isLoggedIn = !empty($_SESSION['user_id']);
$userName = $isLoggedIn ? $_SESSION['user_name'] : '';
?>

<!DOCTYPE html>
<html lang="hy">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NoteApp | Օնլայն Նոթատետր</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        :root {
            --bg-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --card-bg: rgba(255, 255, 255, 0.95);
            --text-color: #212529;
            --muted-text: #6c757d;
            --navbar-bg: rgba(255, 255, 255, 0.9);
            --hero-text: #ffffff;
            --icon-color: #764ba2;
        }

        [data-theme="dark"] {
            --bg-gradient: linear-gradient(135deg, #0b0f19 0%, #171e31 100%);
            --card-bg: #1e293b;
            --text-color: #ffffff;
            --muted-text: #cbd5e1;
            --navbar-bg: rgba(30, 41, 59, 0.95);
            --hero-text: #ffffff;
            --icon-color: #a78bfa;
        }

        body {
            background: var(--bg-gradient);
            color: var(--hero-text);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .navbar-custom {
            background: var(--navbar-bg);
            backdrop-filter: blur(12px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }

        .hero-section {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px 0;
        }

        .hero-title {
            font-size: 3rem;
            font-weight: 800;
            line-height: 1.2;
            color: var(--hero-text);
        }

        .hero-subtitle {
            font-size: 1.25rem;
            color: rgba(255, 255, 255, 0.9);
        }

        .feature-card {
            background: var(--card-bg) !important;
            color: var(--text-color) !important;
            border-radius: 16px;
            border: 1px solid rgba(255,255,255,0.15);
            box-shadow: 0 10px 30px rgba(0,0,0,0.25);
            transition: transform 0.3s ease;
        }

        .feature-card p {
            color: var(--muted-text) !important;
        }

        .feature-card:hover {
            transform: translateY(-8px);
        }

        .feature-icon {
            font-size: 2.5rem;
            color: var(--icon-color);
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-custom sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary fs-4" href="index.php">
            <i class="bi bi-journal-check me-2"></i>NoteApp
        </a>

        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-outline-secondary rounded-circle" id="themeToggle" style="width: 40px; height: 40px; padding: 0;">
                <i class="bi bi-moon-stars" id="themeIcon"></i>
            </button>

            <button class="btn btn-outline-primary fw-bold" id="langToggle" onclick="toggleLanguage()">EN</button>

            <?php if ($isLoggedIn): ?>
                <a href="dashboard.php" class="btn btn-primary fw-semibold" id="dashboardBtn">
                    <i class="bi bi-journal-text me-1"></i>Իմ նշումները
                </a>
                <a href="logout.php" class="btn btn-outline-danger fw-semibold" id="logoutBtn">
                    <i class="bi bi-box-arrow-right"></i>
                </a>
            <?php else: ?>
                <a href="login.php" class="btn btn-outline-primary fw-semibold" id="loginBtn">Մուտք</a>
                <a href="reg.php" class="btn btn-primary fw-semibold" id="registerBtn">Գրանցվել</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<section class="hero-section">
    <div class="container">
        <div class="row align-items-center justify-content-center text-center">
            <div class="col-lg-9 col-xl-8">
                
                <span class="badge bg-light text-dark fs-6 px-3 py-2 rounded-pill mb-3 shadow-sm" id="welcomeBadge">
                    ✨ Ձեր անձնական թվային տիրույթը
                </span>
                
                <h1 class="hero-title mb-4" id="heroTitle">
                    <?php echo $isLoggedIn ? "Բարի գալուստ, " . htmlspecialchars($userName) . " 👋" : "Բարի գալուստ ձեր օնլայն նոթատետր"; ?>
                </h1>
                
                <p class="hero-subtitle mb-5" id="heroSubtitle">
                    Պահպանեք ձեր մտքերը, պլանավորեք աշխատանքը և կառավարեք կարևոր նշումները ցանկացած վայրից՝ հարմար և ապահով։
                </p>

                <?php if ($isLoggedIn): ?>
                    <div class="mb-5">
                        <a href="dashboard.php" class="btn btn-light btn-lg rounded-pill px-5 py-3 fw-bold shadow text-primary" id="mainNotesBtn">
                            Անցնել նշումներին <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                    </div>
                <?php endif; ?>

                <div class="row g-4 text-start">
                    <div class="col-md-4">
                        <div class="card feature-card p-4 h-100">
                            <i class="bi bi-shield-lock feature-icon mb-3"></i>
                            <h5 class="fw-bold" id="feat1Title">Ապահով պահպանում</h5>
                            <p class="small mb-0" id="feat1Desc">Ձեր նոթերը հասանելի են միայն ձեզ՝ անձնական հաշվի միջոցով։</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card feature-card p-4 h-100">
                            <i class="bi bi-phone feature-icon mb-3"></i>
                            <h5 class="fw-bold" id="feat2Title">Հարմարվող դիզայն</h5>
                            <p class="small mb-0" id="feat2Desc">Աշխատեք հեռախոսով, պլանշետով կամ համակարգչով առանց խոչընդոտի։</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card feature-card p-4 h-100">
                            <i class="bi bi-lightning-charge feature-icon mb-3"></i>
                            <h5 class="fw-bold" id="feat3Title">Արագ և պարզ</h5>
                            <p class="small mb-0" id="feat3Desc">Ավելացրեք, թարմացրեք կամ ջնջեք նոթերը վայրկյանների ընթացքում։</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    let currentTheme = localStorage.getItem('noteAppTheme') || 'light';
    let currentLang = localStorage.getItem('noteAppLang') || 'hy';

    const themeToggle = document.getElementById('themeToggle');
    const themeIcon = document.getElementById('themeIcon');

    function applyTheme(theme) {
        if (theme === 'dark') {
            document.body.setAttribute('data-theme', 'dark');
            themeIcon.className = 'bi bi-sun';
        } else {
            document.body.removeAttribute('data-theme');
            themeIcon.className = 'bi bi-moon-stars';
        }
    }

    applyTheme(currentTheme);

    themeToggle.addEventListener('click', () => {
        currentTheme = currentTheme === 'dark' ? 'light' : 'dark';
        localStorage.setItem('noteAppTheme', currentTheme);
        applyTheme(currentTheme);
    });

    const translations = {
        hy: {
            welcomeBadge: "✨ Ձեր անձնական թվային տիրույթը",
            heroTitle: "<?php echo $isLoggedIn ? 'Բարի գալուստ, ' . htmlspecialchars($userName) . ' 👋' : 'Բարի գալուստ ձեր օնլայն նոթատետր'; ?>",
            heroSubtitle: "Պահպանեք ձեր մտքերը, պլանավորեք աշխատանքը և կառավարեք կարևոր նշումները ցանկացած վայրից՝ հարմար և ապահով։",
            login: "Մուտք",
            register: "Գրանցվել",
            dashboard: "Իմ նշումները",
            mainNotesBtn: "Անցնել նշումներին <i class='bi bi-arrow-right ms-2'></i>",
            feat1Title: "Ապահով պահպանում",
            feat1Desc: "Ձեր նոթերը հասանելի են միայն ձեզ՝ անձնական հաշվի միջոցով։",
            feat2Title: "Հարմարվող դիզայն",
            feat2Desc: "Աշխատեք հեռախոսով, պլանշետով կամ համակարգչով առանց խոչընդոտի։",
            feat3Title: "Արագ և պարզ",
            feat3Desc: "Ավելացրեք, թարմացրեք կամ ջնջեք նոթերը վայրկյանների ընթացքում։"
        },
        en: {
            welcomeBadge: "✨ Your Personal Digital Space",
            heroTitle: "<?php echo $isLoggedIn ? 'Welcome back, ' . htmlspecialchars($userName) . ' 👋' : 'Welcome to Your Online Notepad'; ?>",
            heroSubtitle: "Save your thoughts, plan your work, and manage important notes from anywhere safely and comfortably.",
            login: "Login",
            register: "Register",
            dashboard: "My Notes",
            mainNotesBtn: "Go to Notes <i class='bi bi-arrow-right ms-2'></i>",
            feat1Title: "Secure Storage",
            feat1Desc: "Your notes are accessible only to you via your personal account.",
            feat2Title: "Responsive Design",
            feat2Desc: "Work seamlessly on mobile, tablet, or desktop devices.",
            feat3Title: "Fast & Simple",
            feat3Desc: "Add, update, or delete notes in just a matter of seconds."
        }
    };

    function applyLanguage() {
        document.getElementById('langToggle').innerText = currentLang === 'hy' ? 'EN' : 'HY';
        document.getElementById('welcomeBadge').innerText = translations[currentLang].welcomeBadge;
        document.getElementById('heroTitle').innerText = translations[currentLang].heroTitle;
        document.getElementById('heroSubtitle').innerText = translations[currentLang].heroSubtitle;
        
        if (document.getElementById('loginBtn')) document.getElementById('loginBtn').innerText = translations[currentLang].login;
        if (document.getElementById('registerBtn')) document.getElementById('registerBtn').innerText = translations[currentLang].register;
        if (document.getElementById('dashboardBtn')) document.getElementById('dashboardBtn').innerHTML = `<i class="bi bi-journal-text me-1"></i>${translations[currentLang].dashboard}`;
        if (document.getElementById('mainNotesBtn')) document.getElementById('mainNotesBtn').innerHTML = translations[currentLang].mainNotesBtn;

        document.getElementById('feat1Title').innerText = translations[currentLang].feat1Title;
        document.getElementById('feat1Desc').innerText = translations[currentLang].feat1Desc;
        document.getElementById('feat2Title').innerText = translations[currentLang].feat2Title;
        document.getElementById('feat2Desc').innerText = translations[currentLang].feat2Desc;
        document.getElementById('feat3Title').innerText = translations[currentLang].feat3Title;
        document.getElementById('feat3Desc').innerText = translations[currentLang].feat3Desc;
    }

    function toggleLanguage() {
        currentLang = currentLang === 'hy' ? 'en' : 'hy';
        localStorage.setItem('noteAppLang', currentLang);
        applyLanguage();
    }

    applyLanguage();
</script>

</body>
</html>