<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class User {
    private $host = "localhost";
    private $db_login = "root";
    private $db_password = "root"; // MAMP-ում root է, XAMPP-ում՝ "" (դատարկ)
    private $db_name = "lesson4";  // Ձեր բազայի անունը
    private $db_port = "3306";

    public $connect;

    public function __construct() {
        try {
            $dsn = "mysql:host={$this->host};port={$this->db_port};dbname={$this->db_name};charset=utf8mb4";
            $this->connect = new PDO($dsn, $this->db_login, $this->db_password);
            $this->connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            // Եթե root/root-ով չաշխատի, փորձում ենք դատարկ գաղտնաբառով (XAMPP-ի համար)
            try {
                $dsn_alt = "mysql:host=localhost;port=3306;dbname={$this->db_name};charset=utf8mb4";
                $this->connect = new PDO($dsn_alt, "root", "");
                $this->connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e2) {
                die("<div style='color: red; padding: 20px;'>Բազայի միացման սխալ (PDO): " . $e->getMessage() . "</div>");
            }
        }
    }

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
            return "<div class='alert alert-danger mb-3 py-2 small'>Բազայի սխալ: " . $e->getMessage() . "</div>";
        }
    }

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
            return "<div class='alert alert-danger mb-3 py-2 small'>Բազայի սխալ: " . $e->getMessage() . "</div>";
        }
    }
}
?>