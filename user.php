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
        $this->host        = getenv('MYSQLHOST') ?: "localhost";
        $this->db_login    = getenv('MYSQLUSER') ?: "root";
        $this->db_password = getenv('MYSQLPASSWORD') !== false ? getenv('MYSQLPASSWORD') : "root";
        $this->db_name     = getenv('MYSQLDATABASE') ?: "lesson4";
        $this->db_port     = getenv('MYSQLPORT') ?: 3306;

        try {
            // Օգտագործում ենք PDO, որը սերվերում միշտ աշխատում է առանց սխալների
            $dsn = "mysql:host={$this->host};port={$this->db_port};dbname={$this->db_name};charset=utf8mb4";
            $this->connect = new PDO($dsn, $this->db_login, $this->db_password);
            $this->connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("<div style='color: red; padding: 20px;'>Բազայի միացման սխալ (PDO): " . $e->getMessage() . "</div>");
        }
    }

    public function Register($firstName, $lastName, $email, $password) {
        if (empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
            return "<div class='alert alert-danger mb-3 py-2 small'>Խնդրում ենք լրացնել բոլոր դաշտերը։</div>";
        }

        $stmt = $this->connect->prepare("SELECT * FROM `users` WHERE `email` = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            return "<div class='alert alert-danger mb-3 py-2 small'>Այս էլ․ փոստն արդեն գրանցված է։</div>";
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->connect->prepare("INSERT INTO `users` (`firstName`, `lastName`, `email`, `password`) VALUES (?, ?, ?, ?)");
        
        if ($stmt->execute([$firstName, $lastName, $email, $hashedPassword])) {
            return "<div class='alert alert-success mb-3 py-2 small'>Գրանցումն հաջողվեց։ <a href='login.php'>Մուտք գործել</a></div>";
        } else {
            return "<div class='alert alert-danger mb-3 py-2 small'>Գրանցման սխալ։</div>";
        }
    }

    public function Login($email, $password) {
        if (empty($email) || empty($password)) {
            return "<div class='alert alert-danger mb-3 py-2 small'>Խնդրում ենք լրացնել բոլոր դաշտերը։</div>";
        }

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
    }
}
?>