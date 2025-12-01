<?php
include 'connect.php';
mysqli_set_charset($con, 'utf8mb4');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Student Lookup (Secure)</title>
</head>
<body>
    <form method="post" action="">
        <label><strong>Student Name</strong>
            <input type="text" name="student_name" required>
        </label>
        <button type="submit">Submit</button>
    </form>

    <?php
    if (!empty($_POST['student_name'])) {
        $name = trim($_POST['student_name']);

        $sql = "SELECT id, name, major, gpa FROM students WHERE name = ?";
        $stmt = mysqli_prepare($con, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $name);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($result && mysqli_num_rows($result) > 0) {
                echo "<br>";
                echo "<table border='1' cellpadding='5' cellspacing='0'>";
                echo "<tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Major</th>
                        <th>GPA</th>
                      </tr>";
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
