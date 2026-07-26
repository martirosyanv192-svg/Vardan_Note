<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class User {
    private $host;
    private $db_login;
    private $db_password;
    private $db_name;
    private $db_port;

    public $connect;

    public function __construct() {
        // Railway-ի կամ տեղային բազայի տվյալների ավտոմատ ստացում
        $this->host        = getenv('MYSQLHOST') ?: (getenv('DB_HOST') ?: "localhost");
        $this->db_login    = getenv('MYSQLUSER') ?: (getenv('DB_USER') ?: "root");
        $this->db_password = getenv('MYSQLPASSWORD') !== false ? getenv('MYSQLPASSWORD') : (getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : "root");
        $this->db_name     = getenv('MYSQLDATABASE') ?: (getenv('DB_NAME') ?: "lesson4");
        $this->db_port     = getenv('MYSQLPORT') ?: (getenv('DB_PORT') ?: 3306);

        try {
            // Միանում ենք բազային PDO-ով (որը միշտ հասանելի է PHP-ում)
            $dsn = "mysql:host={$this->host};port={$this->db_port};dbname={$this->db_name};charset=utf8mb4";
            $this->connect = new PDO($dsn, $this->db_login, $this->db_password);
            $this->connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            // Եթե առաջինը չստացվի, փորձում ենք տեղային MAMP/XAMPP ստանդարտ տվյալներով
            try {
                $dsn_local = "mysql:host=localhost;port=3306;dbname={$this->db_name};charset=utf8mb4";
                $this->connect = new PDO($dsn_local, "root", "root");
                $this->connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e2) {
                try {
                    $this->connect = new PDO($dsn_local, "root", "");
                    $this->connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                } catch (PDOException $e3) {
                    die("<div style='color: red; padding: 20px; font-family: sans-serif;'>
                           <h3>Տվյալների բազայի միացման սխալ (PDO):</h3>
                           <p>" . htmlspecialchars($e->getMessage()) . "</p>
                           <hr>
                           <small>Ստուգեք, արդյոք Railway-ում MySQL սերվերը միացված է և Variables-ները ճիշտ են փոխանցված։</small>
                         </div>");
                }
            }
        }
    }

    // Գրանցման ֆունկցիա (PDO)
    public function Register($firstName, $lastName, $email, $password) {
        $firstName = trim($firstName);
        $lastName  = trim($lastName);
        $email     = trim($email);

        if (empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
            return "<div class='alert alert-danger mb-3 py-2 small'>Խնդրում ենք լրացնել բոլոր դաշտերը։</div>";
        }

        try {
            $stmt = $this->connect->prepare("SELECT * FROM `users` WHERE `email` = ?");
            $stmt->execute([$email]);
            
            if ($stmt->rowCount() > 0) {
                return "<div class='alert alert-danger mb-3 py-2 small'>Այս էլ․ փոստն արդեն գրանցված է։</div>";
            }

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO `users` (`firstName`, `lastName`, `email`, `password`) VALUES (?, ?, ?, ?)";
            $insertStmt = $this->connect->prepare($sql);
            
            if ($insertStmt->execute([$firstName, $lastName, $email, $hashedPassword])) {
                return "<div class='alert alert-success mb-3 py-2 small'>Գրանցումն հաջողվեց։ <a href='login.php'>Մուտք գործել</a></div>";
            } else {
                return "<div class='alert alert-danger mb-3 py-2 small'>Գրանցման սխալ։</div>";
            }
        } catch (PDOException $e) {
            return "<div class='alert alert-danger mb-3 py-2 small'>Բազայի սխալ: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }

    // Մուտքի ֆունկցիա (PDO)
    public function Login($email, $password) {
        $email = trim($email);

        if (empty($email) || empty($password)) {
            return "<div class='alert alert-danger mb-3 py-2 small'>Խնդրում ենք լրացնել բոլոր դաշտերը։</div>";
        }

        try {
            $stmt = $this->connect->prepare("SELECT * FROM `users` WHERE `email` = ?");
            $stmt->execute([$email]);

            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['firstName'];
                    $_SESSION['user_email'] = $user['email'];

                    header("Location: dashboard.php");
                    exit();
                } else {
                    return "<div class='alert alert-danger mb-3 py-2 small'>Սխալ էլ. փոստ կամ գաղտնաբառ։</div>";
                }
            } else {
                return "<div class='alert alert-danger mb-3 py-2 small'>Այս էլ. փոստով օգտատեր չի գտնվել։</div>";
            }
        } catch (PDOException $e) {
            return "<div class='alert alert-danger mb-3 py-2 small'>Բազայի սխալ: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }
}
?>