<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "oka";
$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) { die("Connection failed: " . mysqli_connect_error()); }
mysqli_set_charset($conn, 'utf8mb4');

$idnum = $_SESSION['ID_NUMBER'] ?? null;
if (!$idnum) { die("No ID provided."); }

$sql = "SELECT s.ID_NUMBER, s.NAME, s.MAJOR, t.CLASS, t.Grade AS GRADE
        FROM Students s
        INNER JOIN Transcripts t ON s.ID_NUMBER = t.STUDENT_ID
        WHERE s.ID_NUMBER = ?
        ORDER BY t.CLASS, t.ID";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $idnum);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html>
<body>
<h1>Display Records</h1>
<?php
if ($result && mysqli_num_rows($result) > 0) {
  echo "<table border='1' cellpadding='5'>";
  echo "<tr><td>ID_NUMBER</td><td>NAME</td><td>MAJOR</td><td>CLASS</td><td>GRADE</td></tr>";
  while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row["ID_NUMBER"]) . "</td>";
    echo "<td>" . htmlspecialchars($row["NAME"]) . "</td>";
    echo "<td>" . htmlspecialchars($row["MAJOR"]) . "</td>";
    echo "<td>" . htmlspecialchars($row["CLASS"]) . "</td>";
    echo "<td>" . htmlspecialchars($row["GRADE"]) . "</td>";
    echo "</tr>";
  }
  echo "</table>";
} else {
  echo "0 results";
}
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
</body>
</html>


