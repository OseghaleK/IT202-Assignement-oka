<?php
require 'config.php';
session_start();

if(!isset($_SESSION['caterer_id'])){
    header("Location: cc_form.html");
    exit;
}

$caterer_id = $_SESSION['caterer_id'];

$stmt = $pdo->prepare("
    SELECT c.first_name AS client_first, c.last_name AS client_last, 
           ce.event_id, ce.event_name, ce.event_date, ce.event_time, 
           ce.location, ce.expected_guests, ce.total_cost, ce.status
    FROM catering_event ce
    JOIN client c ON ce.client_id = c.client_id
    WHERE ce.caterer_id = ?
    ORDER BY ce.event_date DESC
");
$stmt->execute([$caterer_id]);
$bookings = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Search Caterer - Culinary Connoisseurs</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .table-container {
            background: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 12px;
            width: 90%;
            max-width: 1200px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .back-btn {
            background-color: #6c757d;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            margin-bottom: 20px;
        }
        .back-btn:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <div class="table-container">
        <h1>Your Catering Bookings</h1>
        <a href="dashboard.php" class="back-btn">← Back to Dashboard</a>
        
        <?php if (count($bookings) > 0): ?>
        <table>
            <tr>
                <th>Client Name</th>
                <th>Event Name</th>
                <th>Date</th>
                <th>Time</th>
                <th>Location</th>
                <th>Guests</th>
                <th>Total Cost</th>
                <th>Status</th>
            </tr>
            <?php foreach($bookings as $booking): ?>
            <tr>
                <td><?=htmlspecialchars($booking['client_first'] . ' ' . $booking['client_last'])?></td>
                <td><?=htmlspecialchars($booking['event_name'])?></td>
                <td><?=htmlspecialchars(date('m/d/Y', strtotime($booking['event_date'])))?></td>
                <td><?=htmlspecialchars(date('h:i A', strtotime($booking['event_time'])))?></td>
                <td><?=htmlspecialchars($booking['location'])?></td>
                <td><?=htmlspecialchars($booking['expected_guests'])?></td>
                <td>$<?=number_format($booking['total_cost'], 2)?></td>
                <td><?=htmlspecialchars($booking['status'])?></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php else: ?>
        <p>No bookings found for your account.</p>
        <?php endif; ?>
    </div>
</body>
</html>
