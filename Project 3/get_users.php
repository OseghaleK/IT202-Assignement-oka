<?php
include("connect.php");

$sql = "SELECT username FROM chat_users";
$result = mysqli_query($con, $sql);

while ($row = mysqli_fetch_assoc($result)) {
    echo "<p>" . $row['username'] . "</p>";
}
?>
