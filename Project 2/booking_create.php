<?php
require 'config.php';
session_start();

if(!isset($_SESSION['caterer_id'])){
    header("Location: cc_form.html");
    exit;
}

$errors = [];
$success = '';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $client_id = intval($_POST['client_id'] ?? 0);
    $event_name = trim($_POST['event_name'] ?? '');
    $event_date = $_POST['event_date'] ?? '';
    $event_time = $_POST['event_time'] ?? '';
    $location = trim($_POST['location'] ?? '');
    $expected_guests = intval($_POST['expected_guests'] ?? 0);
    $total_cost = floatval($_POST['total_cost'] ?? 0);
    
    if($client_id <= 0) $errors[] = 'Please select a client';
    if($event_name === '') $errors[] = 'Event name is required';
    if($event_date === '') $errors[] = 'Event date is required';
    if($event_time === '') $errors[] = 'Event time is required';
    if($location === '') $errors[] = 'Location is required';
    if($expected_guests <= 0) $errors[] = 'Guest count must be greater than 0';
    if($total_cost <= 0) $errors[] = 'Total cost must be greater than 0';
    
    if(empty($errors)){
        $stmt = $pdo->prepare("
            INSERT INTO catering_event (client_id, caterer_id, event_name, event_date, event_time, location, expected_guests, total_cost, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Scheduled')
        ");
        $stmt->execute([$client_id, $_SESSION['caterer_id'], $event_name, $event_date, $event_time, $location, $expected_guests, $total_cost]);
        $success = 'Event booked successfully!';
    }
}

$clients = $pdo->query("SELECT client_id, first_name, last_name FROM client ORDER BY last_name, first_name")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Book Catering Event - Culinary Connoisseurs</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="form-box">
        <h1>Book New Catering Event</h1>
        
        <?php if($success): ?>
            <div style="color: green; margin-bottom: 15px; font-weight: bold;"><?=htmlspecialchars($success)?></div>
        <?php endif; ?>
        
        <?php if(!empty($errors)): ?>
            <div style="color: red; margin-bottom: 15px;">
                <?php foreach($errors as $error): ?>
                    <div><?=htmlspecialchars($error)?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <form method="post">
            <div class="form-group">
                <label for="client_id">Select Client:</label>
                <select id="client_id" name="client_id" required>
                    <option value="">-- Choose a Client --</option>
                    <?php foreach($clients as $client): ?>
                        <option value="<?=htmlspecialchars($client['client_id'])?>">
                            <?=htmlspecialchars($client['first_name'] . ' ' . $client['last_name'])?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="event_name">Event Name:</label>
                <input type="text" id="event_name" name="event_name" required placeholder="e.g., Birthday Party, Wedding, Corporate Meeting">
            </div>
            
            <div class="form-group">
                <label for="event_date">Event Date:</label>
                <input type="date" id="event_date" name="event_date" required>
            </div>
            
            <div class="form-group">
                <label for="event_time">Event Time:</label>
                <input type="time" id="event_time" name="event_time" required>
            </div>
            
            <div class="form-group">
                <label for="location">Event Location:</label>
                <input type="text" id="location" name="location" required placeholder="Full address of event venue">
            </div>
            
            <div class="form-group">
                <label for="expected_guests">Expected Number of Guests:</label>
                <input type="number" id="expected_guests" name="expected_guests" min="1" required placeholder="e.g., 50">
            </div>
            
            <div class="form-group">
                <label for="total_cost">Total Cost ($):</label>
                <input type="number" id="total_cost" name="total_cost" step="0.01" min="0.01" required placeholder="e.g., 500.00">
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn">Book Event</button>
                <a href="dashboard.php" class="btn" style="background-color: #6c757d; text-decoration: none; display: inline-block; text-align: center; margin-left: 10px;">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>
