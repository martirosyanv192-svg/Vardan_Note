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
        // Ստուգում ենք՝ արդյոք կան Railway-ի environment variables, թե ոչ
        $this->host        = getenv('MYSQLHOST') ?: "localhost";
        $this->db_login    = getenv('MYSQLUSER') ?: "root";
        $this->db_password = getenv('MYSQLPASSWORD') !== false ? getenv('MYSQLPASSWORD') : "root"; // MAMP-ում լռելյայն root է
        $this->db_name     = getenv('MYSQLDATABASE') ?: "note_db";
        $this->db_port     = getenv('MYSQLPORT') ?: 3306;

        // Փորձում ենք միանալ բազային (հաշվի առնելով նաև պորտը)
        $this->connect = @mysqli_connect(
            $this->host,
            $this->db_login,
            $this->db_password,
            $this->db_name,
            (int)$this->db_port
        );

        if (!$this->connect) {
            // Եթե MAMP-ի root/root գաղտնաբառով չմիանա, փորձում ենք դատարկ գաղտնաբառով (տեղային թեստերի համար)
            $this->connect = @mysqli_connect(
                "localhost",
                "root",
                "",
                $this->db_name,
                3306
            );
        }

        if (!$this->connect) {
            die("<div class='alert alert-danger text-center m-3'>Տվյալների բազայի միացման սխալ: " . mysqli_connect_error() . "</div>");
        }

        // Սահմանում ենք UTF-8 կոդավորումը հայերեն տառերի ճիշտ աշխատանքի համար
        mysqli_set_charset($this->connect, "utf8mb4");
    }

    // Գրանցման ֆունկցիա
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

    // Մուտքի ֆունկցիա
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