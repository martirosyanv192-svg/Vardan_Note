<?php
class User {
    private $host;
    private $username;
    private $password;
    private $database;
    private $port;
    public $conn;

    public function __construct() {
        // Ստուգում ենք՝ արդյոք աշխատում ենք Railway-ում, թե տեղային (localhost) միջավայրում
        if (getenv('MYSQLHOST')) {
            $this->host = getenv('MYSQLHOST');
            $this->username = getenv('MYSQLUSER');
            $this->password = getenv('MYSQLPASSWORD');
            $this->database = getenv('MYSQLDATABASE');
            $this->port = getenv('MYSQLPORT') ?: 3306;
        } else {
            // Տեղային կարգավորումներ (MAMP / XAMPP)
            $this->host = 'localhost';
            $this->username = 'root';
            $this->password = 'root'; // MAMP-ի դեպքում root, XAMPP-ի դեպքում թողեք դատարկ ''
            $this->database = 'lesson4'; // Ձեր տեղային բազայի անունը
            $this->port = 3306;
        }

        // Միանում ենք տվյալների բազային MySQLi-ի միջոցով
        $this->conn = mysqli_connect($this->host, $this->username, $this->password, $this->database, $this->port);

        // Ստուգում ենք կապի հաջողված լինելը
        if (!$this->conn) {
            die("Database connection failed: " . mysqli_connect_error());
        }

        // Կարգավորում ենք UTF-8 ռուսերեն/հայերեն տառերի ճիշտ աշխատանքի համար
        mysqli_set_charset($this->conn, "utf8mb4");
    }
}
?>