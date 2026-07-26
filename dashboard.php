<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'user.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userObj = new User();
$connect = $userObj->connect;

$user_id = (int)$_SESSION['user_id'];

// Վերցնում ենք օգտատիրոջ տվյալները բազայից
$userQuery = mysqli_query($connect, "SELECT * FROM `users` WHERE `id` = '$user_id'");
$userData  = mysqli_fetch_assoc($userQuery);

if (!empty($userData['firstName'])) {
    $user_name = $userData['firstName'];
} elseif (!empty($_SESSION['user_name']) && $_SESSION['user_name'] !== 'Օգտատեր') {
    $user_name = $_SESSION['user_name'];
} else {
    $user_name = 'Օգտատեր';
}

// 1. CREATE - Նշում ավելացնելը
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_note'])) {
    $title = mysqli_real_escape_string($connect, trim($_POST['title']));
    $text  = mysqli_real_escape_string($connect, trim($_POST['text']));

    if (!empty($title) && !empty($text)) {
        $sql = "INSERT INTO `notes` (`user_id`, `title`, `text`) VALUES ('$user_id', '$title', '$text')";
        mysqli_query($connect, $sql);
        header("Location: dashboard.php");
        exit();
    }
}

// 2. UPDATE - Նշումը թարմացնելը/խմբագրելը
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_note'])) {
    $note_id = (int)$_POST['note_id'];
    $title   = mysqli_real_escape_string($connect, trim($_POST['title']));
    $text    = mysqli_real_escape_string($connect, trim($_POST['text']));

    if (!empty($title) && !empty($text)) {
        $sql = "UPDATE `notes` SET `title` = '$title', `text` = '$text' WHERE `id` = '$note_id' AND `user_id` = '$user_id'";
        mysqli_query($connect, $sql);
        header("Location: dashboard.php");
        exit();
    }
}

// 3. DELETE - Նշում ջնջելը
if (isset($_GET['delete'])) {
    $note_id = (int)$_GET['delete'];
    $sql = "DELETE FROM `notes` WHERE `id` = '$note_id' AND `user_id` = '$user_id'";
    mysqli_query($connect, $sql);
    header("Location: dashboard.php");
    exit();
}

// 4. READ - Օգտատիրոջ նշումները կարդալը
$sql = "SELECT * FROM `notes` WHERE `user_id` = '$user_id' ORDER BY `id` DESC";
$notesQuery = mysqli_query($connect, $sql);
?>

<!DOCTYPE html>
<html lang="hy" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NoteApp | Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg-color: #f4f6f9;
            --card-bg: #ffffff;
            --text-color: #2b2f3e;
            --text-muted: #6c757d;
            --border-color: #e2e8f0;
            --nav-bg: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            --shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
            --input-bg: #ffffff;
            --btn-more-bg: rgba(99, 102, 241, 0.08);
            --btn-more-color: #6366f1;
            --btn-more-hover-bg: #6366f1;
            --btn-more-hover-color: #ffffff;
        }

        [data-theme="dark"] {
            --bg-color: #0f172a;
            --card-bg: #1e293b;
            --text-color: #f8fafc;
            --text-muted: #94a3b8;
            --border-color: #334155;
            --nav-bg: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);
            --shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
            --input-bg: #0f172a;
            --btn-more-bg: rgba(99, 102, 241, 0.15);
            --btn-more-color: #818cf8;
            --btn-more-hover-bg: #6366f1;
            --btn-more-hover-color: #ffffff;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            transition: background-color 0.3s ease, color 0.3s ease;
            min-height: 100vh;
            top: 0 !important;
        }

        .navbar-custom { background: var(--nav-bg); }

        .control-btn {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
            border-radius: 12px;
            padding: 6px 14px;
            transition: all 0.3s ease;
        }
        .control-btn:hover { background: rgba(255, 255, 255, 0.25); color: #fff; }

        .glass-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
        }

        .note-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px -5px rgba(99, 102, 241, 0.15);
        }

        .form-control-custom {
            background-color: var(--input-bg);
            border: 1px solid var(--border-color);
            color: var(--text-color);
            border-radius: 12px;
            padding: 12px;
        }

        .form-control-custom:focus {
            background-color: var(--input-bg);
            color: var(--text-color);
            border-color: #6366f1;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
        }

        .btn-gradient {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            border: none;
            color: #fff;
            border-radius: 12px;
            padding: 12px 24px;
            font-weight: 600;
        }

        .btn-logout {
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid rgba(239, 68, 68, 0.4);
            color: #fff;
            border-radius: 10px;
            padding: 6px 14px;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .btn-logout:hover {
            background: #ef4444;
            color: #fff;
        }

        .action-btn { opacity: 0.7; transition: all 0.2s ease; cursor: pointer; }
        .action-btn:hover { opacity: 1; transform: scale(1.1); }
        .edit-btn { color: #6366f1; }
        .delete-btn { color: #ef4444; }

        .modal-content-custom {
            background-color: var(--card-bg);
            color: var(--text-color);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .note-preview-text {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            word-break: break-word;
        }

        .view-more-btn {
            background-color: var(--btn-more-bg);
            color: var(--btn-more-color);
            border: 1px solid rgba(99, 102, 241, 0.2);
            font-weight: 600;
            font-size: 0.85rem;
            padding: 8px 16px;
            border-radius: 10px;
            cursor: pointer;
            margin-top: 12px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            width: fit-content;
        }

        .view-more-btn i {
            font-size: 1rem;
            transition: transform 0.3s ease;
        }

        .view-more-btn:hover {
            background-color: var(--btn-more-hover-bg);
            color: var(--btn-more-hover-color);
            border-color: var(--btn-more-hover-bg);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
            transform: translateY(-2px);
        }

        .view-more-btn:hover i {
            transform: translateX(4px);
        }

        .view-text-area {
            max-height: 280px;
            overflow-y: auto;
            background-color: var(--input-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 15px;
        }

        /* Թաքցնում ենք Google Translate-ի վերևի անպետք շերտը */
        #google_translate_element { display: none; }
        .goog-te-banner-frame { display: none !important; }
        .skiptranslate { display: none !important; }
    </style>
</head>
<body>

<div id="google_translate_element"></div>

<nav class="navbar navbar-expand-lg navbar-dark navbar-custom shadow-sm mb-4 py-3">
    <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center gap-2 fs-4" href="dashboard.php">
            <i class="bi bi-journal-code text-warning fs-3"></i> NoteApp
        </a>
        
        <div class="d-flex align-items-center gap-2">
            <button class="btn control-btn text-uppercase fw-bold" onclick="toggleLanguage()">
                <i class="bi bi-translate me-1"></i> <span id="langText">EN</span>
            </button>

            <button class="btn control-btn" onclick="toggleTheme()">
                <i class="bi bi-moon-stars-fill" id="themeIcon"></i>
            </button>

            <div class="ms-2 d-flex align-items-center gap-3">
                <span class="text-white d-none d-md-inline" style="font-size: 0.95rem;">
                    <span class="lang-txt" data-hy="Բարև," data-en="Hello,">Բարև,</span> <b><?php echo htmlspecialchars($user_name); ?></b> 👋
                </span>
                
                <a href="logout.php" class="btn-logout d-flex align-items-center gap-1">
                    <i class="bi bi-box-arrow-right"></i> 
                    <span class="lang-txt" data-hy="Դուրս գալ" data-en="Logout">Դուրս գալ</span>
                </a>
            </div>
        </div>
    </div>
</nav>

<div class="container pb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 mb-5">
            <div class="glass-card p-4 p-md-5">
                <h4 class="fw-bold mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-plus-circle-fill text-primary"></i> 
                    <span class="lang-txt" data-hy="Ավելացնել նոր նշում" data-en="Create New Note">Ավելացնել նոր նշում</span>
                </h4>
                <form action="dashboard.php" method="POST">
                    <div class="mb-3">
                        <input type="text" name="title" class="form-control form-control-custom lang-ph" 
                               placeholder="Վերնագիր..." data-hy="Վերնագիր..." data-en="Title..." required>
                    </div>
                    <div class="mb-3">
                        <textarea name="text" class="form-control form-control-custom lang-ph" rows="3" 
                                  placeholder="Գրեք նշումը այստեղ..." data-hy="Գրեք նշումը այստեղ..." data-en="Write your note here..." required></textarea>
                    </div>
                    <button type="submit" name="add_note" class="btn btn-gradient w-100">
                        <i class="bi bi-check-lg me-1"></i> 
                        <span class="lang-txt" data-hy="Պահպանել" data-en="Save Note">Պահպանել</span>
                    </button>
                </form>
            </div>
        </div>

        <div class="col-12">
            <h4 class="fw-bold mb-4 d-flex align-items-center gap-2">
                <i class="bi bi-stickies-fill text-warning"></i>
                <span class="lang-txt" data-hy="Իմ նշումները 📝" data-en="My Notes 📝">Իմ նշումները 📝</span>
            </h4>

            <div class="row g-4 d-flex align-items-stretch">
                <?php if ($notesQuery && mysqli_num_rows($notesQuery) > 0): ?>
                    <?php while ($note = mysqli_fetch_assoc($notesQuery)): ?>
                        <?php 
                            $titleEscaped = addslashes(htmlspecialchars($note['title']));
                            $textEscaped  = addslashes(htmlspecialchars($note['text']));
                            $dateFormatted = isset($note['created_at']) ? $note['created_at'] : '';
                        ?>
                        <div class="col-md-6 col-lg-4 note-card">
                            <div class="glass-card h-100 p-4 d-flex flex-column justify-content-between">
                                <div class="d-flex flex-column h-100 justify-content-between">
                                    <div>
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h5 class="fw-bold text-truncate mb-0 pe-2" style="color: var(--text-color);">
                                                <?php echo htmlspecialchars($note['title']); ?>
                                            </h5>
                                            <div class="d-flex gap-2 align-items-center">
                                                <span class="action-btn edit-btn fs-5" 
                                                      onclick="openEditModal(<?php echo $note['id']; ?>, '<?php echo $titleEscaped; ?>', '<?php echo $textEscaped; ?>')">
                                                    <i class="bi bi-pencil-square"></i>
                                                </span>
                                                <a href="dashboard.php?delete=<?php echo $note['id']; ?>" class="action-btn delete-btn fs-5" 
                                                   onclick="return confirm(currentLang === 'hy' ? 'Վստա՞հ ես, որ ուզում ես ջնջել:' : 'Are you sure you want to delete?');">
                                                    <i class="bi bi-trash3-fill"></i>
                                                </a>
                                            </div>
                                        </div>

                                        <p class="small mb-0 note-preview-text" style="color: var(--text-muted); white-space: pre-wrap; line-height: 1.6;">
                                            <?php echo htmlspecialchars($note['text']); ?>
                                        </p>
                                    </div>

                                    <div>
                                        <button class="view-more-btn" 
                                                onclick="openViewModal(<?php echo $note['id']; ?>, '<?php echo $titleEscaped; ?>', '<?php echo $textEscaped; ?>', '<?php echo $dateFormatted; ?>')">
                                            <span class="lang-txt" data-hy="Տեսնել ավելին" data-en="Read More">Տեսնել ավելին</span>
                                            <i class="bi bi-arrow-right-short"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="pt-3 mt-3 border-top d-flex align-items-center justify-content-between" style="border-color: var(--border-color) !important;">
                                    <small class="text-muted" style="font-size: 0.75rem;">
                                        <i class="bi bi-clock me-1"></i><?php echo $dateFormatted; ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <div class="glass-card p-5 mx-auto" style="max-width: 500px;">
                            <i class="bi bi-inbox text-muted display-1 mb-3"></i>
                            <p class="text-muted mb-0 lang-txt" 
                               data-hy="Դեռ ոչ մի նշում չունեք։ Ավելացրեք առաջինը վերևում։" 
                               data-en="No notes found yet. Create your first note above!">
                                Դեռ ոչ մի նշում չունեք։ Ավելացրեք առաջինը վերևում։
                            </p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="viewNoteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-content-custom p-3 p-md-4">
            <div class="modal-header border-0 pb-0">
                <h4 class="fw-bold m-0" id="view-title" style="color: var(--text-color);"></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-3">
                <div class="view-text-area">
                    <p id="view-text" class="m-0" style="color: var(--text-color); white-space: pre-wrap; line-height: 1.7; font-size: 1rem;"></p>
                </div>
                <div class="mt-3">
                    <small class="text-muted" style="font-size: 0.85rem;">
                        <i class="bi bi-clock me-1"></i><span id="view-date"></span>
                    </small>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 d-flex gap-2">
                <button type="button" class="btn btn-primary rounded-3 flex-grow-1 py-2" id="btn-edit-from-view">
                    <i class="bi bi-pencil-square me-1"></i> 
                    <span class="lang-txt" data-hy="Խմբագրել" data-en="Edit">Խմբագրել</span>
                </button>
                <button type="button" class="btn btn-secondary rounded-3 px-4 py-2" data-bs-dismiss="modal">
                    <span class="lang-txt" data-hy="Փակել" data-en="Close">Փակել</span>
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-content-custom p-3">
            <div class="modal-header border-0">
                <h5 class="modal-header-title fw-bold m-0">
                    <i class="bi bi-pencil-square text-primary me-2"></i>
                    <span class="lang-txt" data-hy="Խմբագրել նշումը" data-en="Edit Note">Խմբագրել նշումը</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="dashboard.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="note_id" id="edit-id">
                    
                    <div class="mb-3">
                        <label class="form-label small fw-semibold lang-txt" data-hy="Վերնագիր" data-en="Title">Վերնագիր</label>
                        <input type="text" name="title" id="edit-title" class="form-control form-control-custom" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-semibold lang-txt" data-hy="Տեքստ" data-en="Text">Տեքստ</label>
                        <textarea name="text" id="edit-text" class="form-control form-control-custom" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">
                        <span class="lang-txt" data-hy="Չեղարկել" data-en="Cancel">Չեղարկել</span>
                    </button>
                    <button type="submit" name="update_note" class="btn btn-gradient">
                        <span class="lang-txt" data-hy="Թարմացնել" data-en="Update">Թարմացնել</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script type="text/javascript">
function googleTranslateElementInit() {
  new google.translate.TranslateElement({
    pageLanguage: 'hy',
    includedLanguages: 'en,hy',
    autoDisplay: false
  }, 'google_translate_element');
}
</script>
<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

<script>
    let viewModalInstance = null;
    let editModalInstance = null;

    document.addEventListener('DOMContentLoaded', function() {
        viewModalInstance = new bootstrap.Modal(document.getElementById('viewNoteModal'));
        editModalInstance = new bootstrap.Modal(document.getElementById('editModal'));
    });

    // 1. View Note Modal
    function openViewModal(id, title, text, date) {
        document.getElementById('view-title').innerText = title;
        document.getElementById('view-text').innerText = text;
        document.getElementById('view-date').innerText = date;

        document.getElementById('btn-edit-from-view').onclick = function() {
            viewModalInstance.hide();
            openEditModal(id, title, text);
        };

        viewModalInstance.show();
    }

    // 2. Edit Modal
    function openEditModal(id, title, text) {
        document.getElementById('edit-id').value = id;
        document.getElementById('edit-title').value = title;
        document.getElementById('edit-text').value = text;
        
        editModalInstance.show();
    }

    // 3. Theme Switcher
    let currentTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-theme', currentTheme);
    updateThemeIcon();

    function toggleTheme() {
        currentTheme = currentTheme === 'light' ? 'dark' : 'light';
        document.documentElement.setAttribute('data-theme', currentTheme);
        localStorage.setItem('theme', currentTheme);
        updateThemeIcon();
    }

    function updateThemeIcon() {
        const icon = document.getElementById('themeIcon');
        if (currentTheme === 'dark') {
            icon.className = 'bi bi-sun-fill text-warning';
        } else {
            icon.className = 'bi bi-moon-stars-fill';
        }
    }

    // 4. Language Switcher (Գործարկում է նաև Google Translate-ը)
    let currentLang = localStorage.getItem('lang') || 'hy';

    function triggerGoogleTranslate(lang) {
        const select = document.querySelector('.goog-te-combo');
        if (select) {
            select.value = lang;
            select.dispatchEvent(new Event('change'));
        }
    }

    function toggleLanguage() {
        currentLang = currentLang === 'hy' ? 'en' : 'hy';
        localStorage.setItem('lang', currentLang);
        applyLanguage(currentLang);
    }

    function applyLanguage(lang) {
        document.getElementById('langText').innerText = lang === 'hy' ? 'EN' : 'HY';

        // Տեքստերի փոփոխում
        document.querySelectorAll('.lang-txt').forEach(el => {
            const attrVal = el.getAttribute(`data-${lang}`);
            if (attrVal) {
                el.innerText = attrVal;
            }
        });

        // Placeholders
        document.querySelectorAll('.lang-ph').forEach(el => {
            const attrVal = el.getAttribute(`data-${lang}`);
            if (attrVal) {
                el.placeholder = attrVal;
            }
        });

        // Թարգմանում ենք ամբողջ էջի տեքստերը (այդ թվում բազայի նշումները)
        triggerGoogleTranslate(lang);
    }

    // Էջը բեռնվելուց հետո կիրառում ենք լեզուն
    window.onload = function() {
        setTimeout(function() {
            applyLanguage(currentLang);
        }, 500);
    };
</script>

</body>
</html>