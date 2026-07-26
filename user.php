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
        // Railway-ի կամ տեղային բազայի տվյալների ստացում
        $this->host        = getenv('MYSQLHOST') ?: (getenv('DB_HOST') ?: "localhost");
        $this->db_login    = getenv('MYSQLUSER') ?: (getenv('DB_USER') ?: "root");
        $this->db_password = getenv('MYSQLPASSWORD') !== false ? getenv('MYSQLPASSWORD') : (getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : "root");
        $this->db_name     = getenv('MYSQLDATABASE') ?: (getenv('DB_NAME') ?: "lesson4");
        $this->db_port     = getenv('MYSQLPORT') ?: (getenv('DB_PORT') ?: 3306);

        // Փորձում ենք միանալ տվյալների բազային
        $this->connect = @mysqli_connect(
            $this->host,
            $this->db_login,
            $this->db_password,
            $this->db_name,
            (int)$this->db_port
        );

        // Եթե առաջինը չստացվի, փորձում ենք տեղային MAMP/XAMPP ստանդարտ տվյալներով
        if (!$this->connect) {
            $this->connect = @mysqli_connect("localhost", "root", "root", $this->db_name, 3306);
        }
        if (!$this->connect) {
            $this->connect = @mysqli_connect("localhost", "root", "", $this->db_name, 3306);
        }

        if (!$this->connect) {
            die("<div style='color: red; padding: 20px; font-family: sans-serif;'>
                   <h3>Տվյալների բազայի միացման սխալ:</h3>
                   <p>" . mysqli_connect_error() . "</p>
                   <hr>
                   <small>Ստուգեք, արդյոք Railway-ում MySQL սերվերը միացված է և Variables-ները ճիշտ են փոխանցված։</small>
                 </div>");
        }

        mysqli_set_charset($this->connect, "utf8mb4");
    }

    public function Register($firstName, $lastName, $email, $password) {
        $connect = $this->connect;
        $firstName = mysqli_real_escape_string($connect, trim($firstName));
        $lastName  = mysqli_real_escape_string($connect, trim($lastName));
        $email     = mysqli_real_escape_string($connect, trim($email));

        if (empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
            return "<div class='alert alert-danger mb-3 py-2 small'>Խնդրում ենք լրացնել բոլոր դաշտերը։</div>";
        }

        $checkEmail = mysqli_query($connect, "SELECT * FROM `users` WHERE `email` = '$email'");
        if ($checkEmail && mysqli_num_rows($checkEmail) > 0) {
            return "<div class='alert alert-danger mb-3 py-2 small'>Այս էլ․ փոստն արդեն գրանցված է։</div>";
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO `users` (`firstName`, `lastName`, `email`, `password`) VALUES ('$firstName', '$lastName', '$email', '$hashedPassword')";
        
        if (mysqli_query($connect, $sql)) {
            return "<div class='alert alert-success mb-3 py-2 small'>Գրանցումն հաջողվեց։ <a href='login.php'>Մուտք գործել</a></div>";
        } else {
            return "<div class='alert alert-danger mb-3 py-2 small'>Գրանցման սխալ: " . mysqli_error($connect) . "</div>";
        }
    }

    public function Login($email, $password) {
        $connect = $this->connect;
        $email = mysqli_real_escape_string($connect, trim($email));

        if (empty($email) || empty($password)) {
            return "<div class='alert alert-danger mb-3 py-2 small'>Խնդրում ենք լրացնել բոլոր դաշտերը։</div>";
        }

        $sql = "SELECT * FROM `users` WHERE `email` = '$email'";
        $query = mysqli_query($connect, $sql);

        if ($query && mysqli_num_rows($query) > 0) {
            $user = mysqli_fetch_assoc($query);

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