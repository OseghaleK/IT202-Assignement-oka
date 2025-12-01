<?php
include 'connect.php';
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Student Lookup (Secure)</title>
</head>
<body>
    <h2>Student Lookup (Secure - SQL Injection Prevented)</h2>

    <form method="post" action="">
        <label>Student Name:
            <input type="text" name="student_name" required>
        </label>
        <button type="submit">Search</button>
    </form>

    <?php
    if (isset($_POST['student_name'])) {
        $name = trim($_POST['student_name']);

        $sql = "SELECT id, name, major, gpa FROM students WHERE name = ?";
        $stmt = mysqli_prepare($con, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $name);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($result && mysqli_num_rows($result) > 0) {
                echo "<table border='1' cellpadding='5'>";
                echo "<tr><th>ID</th><th>Name</th><th>Major</th><th>GPA</th></tr>";
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['major']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['gpa']) . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p>No matching student found.</p>";
            }

            mysqli_stmt_close($stmt);
        } else {
            echo "<p>Query preparation failed.</p>";
        }
    }
    ?>
</body>
</html>
