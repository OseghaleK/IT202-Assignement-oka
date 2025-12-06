<?php
include("connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $message = $_POST['message'];

    $sql = "SELECT * FROM chat_users WHERE username='$username' AND password='$password'";
    $result = mysqli_query($con, $sql);

    if (mysqli_num_rows($result) > 0) {
        $sql = "INSERT INTO chat_messages (username, password, user_chat_message, send_to) 
                VALUES ('$username', '$password', '$message', 'admin')";
        if (mysqli_query($con, $sql)) {
            echo "OK";
        } else {
            echo "Error: " . mysqli_error($con);
        }
    } else {
        echo "Invalid username or password!";
    }
}
?>
