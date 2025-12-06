<?php
include("connect.php");

if (isset($_GET['username'])) {
    $username = $_GET['username'];
    $sql = "SELECT * FROM chat_messages WHERE send_to = '$username' ORDER BY created_at DESC";
} else {
    $sql = "SELECT * FROM chat_messages ORDER BY created_at DESC";
}

$result = mysqli_query($con, $sql);

while ($row = mysqli_fetch_assoc($result)) {
    echo "<p><strong>" . $row['username'] . "</strong>: " . $row['user_chat_message'] . "</p>";
}
?>
