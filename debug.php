<?php
session_start();
require_once 'user.php';

$userObj = new User();
$connect = $userObj->connect;

echo "<h3>1. Session-ի տվյալները.</h3>";
echo "<pre>"; print_r($_SESSION); echo "</pre>";

if (isset($_SESSION['user_id'])) {
    $user_id = (int)$_SESSION['user_id'];
    $result = mysqli_query($connect, "SELECT * FROM `users` WHERE `id` = '$user_id'");
    $userData = mysqli_fetch_assoc($result);

    echo "<h3>2. Տվյալների բազայում քո տվյալները.</h3>";
    echo "<pre>"; print_r($userData); echo "</pre>";
} else {
    echo "<h3>Օգտատերը մուտք չի գործել: Խնդրում ենք նախ login լինել:</h3>";
}
?>