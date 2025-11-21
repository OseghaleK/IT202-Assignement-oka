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
    $service_type = trim($_POST['service_type'] ?? '');
    $service_description = trim($_POST['service_description'] ?? '');
    $service_cost = floatval($_POST['service_cost'] ?? 0);
    
    if($event_id <= 0) $errors[] = 'Please select an event';
    if($service_type === '') $errors[] = 'Service type is required';
    if($service_cost <= 0) $errors[] = 'Service cost must be greater than 0';
    
    if(empty($errors)){
        $stmt = $pdo->prepare("
            INSERT INTO additional_service (event_id, service_type, service_description, service_cost, request_date, status) 
            VALUES (?, ?, ?, ?, CURDATE(), 'Requested')
        ");
        $stmt->execute([$event_id, $service_type, $service_description, $service_cost]);
        $success = 'Additional service requested successfully!';
    }
}

$stmt = $pdo->prepare("
    SELECT ce.event_id, ce.event_name, ce.event_date, 
           c.first_name, c.last_name
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
    <title>Request Additional Services - Culinary Connoisseurs</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="form-box">
        <h1>Request Additional Services</h1>
        
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
                <label for="event_id">Select Event:</label>
                <select id="event_id" name="event_id" required>
                    <option value="">-- Choose an Event --</option>
                    <?php foreach($events as $event): ?>
                        <option value="<?=htmlspecialchars($event['event_id'])?>">
                            <?=htmlspecialchars($event['event_name'])?> - <?=htmlspecialchars($event['first_name'] . ' ' . $event['last_name'])?> (<?=htmlspecialchars(date('m/d/Y', strtotime($event['event_date'])))?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="service_type">Service Type:</label>
                <select id="service_type" name="service_type" required onchange="handleServiceTypeChange()">
                    <option value="">-- Select Service Type --</option>
                    <option value="Dessert Bar">Dessert Bar</option>
                    <option value="Beverage Service">Beverage Service</option>
                    <option value="Floral Decoration">Floral Decoration</option>
                    <option value="Table Rental">Table Rental</option>
                    <option value="Chair Rental">Chair Rental</option>
                    <option value="Tent Rental">Tent Rental</option>
                    <option value="Sound System">Sound System</option>
                    <option value="Lighting">Lighting</option>
                    <option value="Photography">Photography</option>
                    <option value="Other">Other (please specify)</option>
                </select>
            </div>
            
            <div class="form-group" id="other_service_group" style="display: none;">
                <label for="other_service">Please specify service:</label>
                <input type="text" id="other_service" name="other_service" placeholder="Enter the specific service type">
            </div>
            
            <div class="form-group">
                <label for="service_description">Service Description:</label>
                <textarea id="service_description" name="service_description" rows="4" placeholder="Describe the additional service needed"></textarea>
            </div>
            
            <div class="form-group">
                <label for="service_cost">Service Cost ($):</label>
                <input type="number" id="service_cost" name="service_cost" step="0.01" min="0.01" required placeholder="e.g., 150.00">
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn">Request Service</button>
                <a href="dashboard.php" class="btn" style="background-color: #6c757d; text-decoration: none; display: inline-block; text-align: center; margin-left: 10px;">Cancel</a>
            </div>
        </form>
    </div>
    
    <script>
        function handleServiceTypeChange() {
            const serviceType = document.getElementById('service_type').value;
            const otherServiceGroup = document.getElementById('other_service_group');
            
            if (serviceType === 'Other') {
                otherServiceGroup.style.display = 'block';
                document.getElementById('other_service').setAttribute('required', 'required');
            } else {
                otherServiceGroup.style.display = 'none';
                document.getElementById('other_service').removeAttribute('required');
            }
        }
    </script>
</body>
</html>
