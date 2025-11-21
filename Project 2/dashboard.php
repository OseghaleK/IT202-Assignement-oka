<?php
session_start();
if(!isset($_SESSION['caterer_id'])){
    header("Location: cc_form.html");
    exit;
}

$first = $_SESSION['first_name'];
$last = $_SESSION['last_name'];
$transaction = $_SESSION['transaction'];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Culinary Connoisseurs Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .dashboard-content {
            background: rgba(255, 255, 255, 0.9);
            padding: 40px;
            border-radius: 12px;
            width: 800px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
            text-align: center;
        }
        .nav-buttons {
            margin-top: 30px;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
        .nav-button {
            background-color: #007bff;
            color: white;
            padding: 15px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        .nav-button:hover {
            background-color: #0056b3;
        }
        .logout-btn {
            background-color: #dc3545;
            margin-top: 20px;
        }
        .logout-btn:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <div class="dashboard-content">
        <h1>Welcome, <?=htmlspecialchars($first)?> <?=htmlspecialchars($last)?></h1>
        <p><strong>Selected Action:</strong> <?=htmlspecialchars($transaction)?></p>
        
        <div class="nav-buttons">
            <a href="search_caterer.php" class="nav-button">Search Caterer Account</a>
            <a href="booking_create.php" class="nav-button">Book Catering Event</a>
            <a href="booking_cancel.php" class="nav-button">Cancel Catering Event</a>
            <a href="additional_service_request.php" class="nav-button">Request Additional Services</a>
            <a href="additional_service_update.php" class="nav-button">Update Additional Services</a>
            <a href="client_create.php" class="nav-button">Create Client Account</a>
        </div>
        
        <a href="logout.php" class="nav-button logout-btn">Logout</a>
    </div>
</body>
</html>
