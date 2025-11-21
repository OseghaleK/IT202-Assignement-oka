<?php
require 'config.php';
session_start();

if(!isset($_SESSION['caterer_id'])){
    header("Location: cc_form.html");
    exit;
}

$caterer_id = $_SESSION['caterer_id'];
$errors = [];
$success = '';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $event_id = intval($_POST['event_id'] ?? 0);
    
    if($event_id <= 0) {
        $errors[] = 'Please select an event to cancel';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM catering_event WHERE event_id = ? AND caterer_id = ?");
        $stmt->execute([$event_id, $caterer_id]);
        $event = $stmt->fetch();
        
        if(!$event) {
            $errors[] = 'Event not found or you do not have permission to cancel this event';
        } elseif($event['status'] === 'Cancelled') {
            $errors[] = 'This event has already been cancelled';
        } else {
            $update_stmt = $pdo->prepare("UPDATE catering_event SET status = 'Cancelled' WHERE event_id = ?");
            if($update_stmt->execute([$event_id])) {
                $success = 'Event cancelled successfully!';
            } else {
                $errors[] = 'Error cancelling event. Please try again.';
            }
        }
    }
}

$stmt = $pdo->prepare("
    SELECT ce.event_id, ce.event_name, ce.event_date, ce.event_time, 
           c.first_name, c.last_name, ce.status
    FROM catering_event ce
    JOIN client c ON ce.client_id = c.client_id
    WHERE ce.caterer_id = ? AND ce.status = 'Scheduled'
    ORDER BY ce.event_date ASC
");
$stmt->execute([$caterer_id]);
$events = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Cancel Catering Event - Culinary Connoisseurs</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .cancel-container {
            background: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 12px;
            width: 90%;
            max-width: 1000px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
        }
        .events-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
        }
        .events-table th, .events-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .events-table th {
            background-color: #dc3545;
            color: white;
            font-weight: bold;
        }
        .events-table tr:hover {
            background-color: #f8f9fa;
        }
        .cancel-btn {
            background-color: #dc3545;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        .cancel-btn:hover {
            background-color: #c82333;
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
        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .alert-error {
            color: #721c24;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="cancel-container">
        <h1>Cancel Catering Event</h1>
        <a href="dashboard.php" class="back-btn">← Back to Dashboard</a>
        
        <?php if($success): ?>
            <div class="alert-success"><?=htmlspecialchars($success)?></div>
        <?php endif; ?>
        
        <?php if(!empty($errors)): ?>
            <div class="alert-error">
                <?php foreach($errors as $error): ?>
                    <div><?=htmlspecialchars($error)?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <?php if (count($events) > 0): ?>
            <h3>Your Scheduled Events</h3>
            <p>Select an event to cancel:</p>
            
            <table class="events-table">
                <tr>
                    <th>Event ID</th>
                    <th>Event Name</th>
                    <th>Client</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                <?php foreach($events as $event): ?>
                <tr>
                    <td><?=htmlspecialchars($event['event_id'])?></td>
                    <td><?=htmlspecialchars($event['event_name'])?></td>
                    <td><?=htmlspecialchars($event['first_name'] . ' ' . $event['last_name'])?></td>
                    <td><?=htmlspecialchars(date('m/d/Y', strtotime($event['event_date'])))?></td>
                    <td><?=htmlspecialchars(date('h:i A', strtotime($event['event_time'])))?></td>
                    <td><?=htmlspecialchars($event['status'])?></td>
                    <td>
                        <form method="post" onsubmit="return confirm('Are you sure you want to cancel this event?')">
                            <input type="hidden" name="event_id" value="<?=htmlspecialchars($event['event_id'])?>">
                            <button type="submit" class="cancel-btn">Cancel Event</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No scheduled events found for cancellation.</p>
        <?php endif; ?>
    </div>
</body>
</html>
