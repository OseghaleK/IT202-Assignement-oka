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
    $service_id = intval($_POST['service_id'] ?? 0);
    $service_type = trim($_POST['service_type'] ?? '');
    $service_description = trim($_POST['service_description'] ?? '');
    $service_cost = floatval($_POST['service_cost'] ?? 0);
    $status = $_POST['status'] ?? 'Requested';
    
    if($service_id <= 0) $errors[] = 'Please select a service to update';
    if($service_type === '') $errors[] = 'Service type is required';
    if($service_cost <= 0) $errors[] = 'Service cost must be greater than 0';
    
    if(empty($errors)){
        $stmt = $pdo->prepare("
            UPDATE additional_service 
            SET service_type = ?, service_description = ?, service_cost = ?, status = ?
            WHERE service_id = ? AND event_id IN (
                SELECT event_id FROM catering_event WHERE caterer_id = ?
            )
        ");
        $stmt->execute([$service_type, $service_description, $service_cost, $status, $service_id, $caterer_id]);
        $success = 'Additional service updated successfully!';
    }
}

$stmt = $pdo->prepare("
    SELECT asrv.service_id, asrv.service_type, asrv.service_description, 
           asrv.service_cost, asrv.status, asrv.request_date,
           ce.event_id, ce.event_name, ce.event_date,
           c.first_name, c.last_name
    FROM additional_service asrv
    JOIN catering_event ce ON asrv.event_id = ce.event_id
    JOIN client c ON ce.client_id = c.client_id
    WHERE ce.caterer_id = ?
    ORDER BY asrv.request_date DESC
");
$stmt->execute([$caterer_id]);
$services = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Update Additional Services - Culinary Connoisseurs</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .services-container {
            background: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 12px;
            width: 90%;
            max-width: 1200px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
        }
        .services-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
        }
        .services-table th, .services-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .services-table th {
            background-color: #17a2b8;
            color: white;
            font-weight: bold;
        }
        .services-table tr:hover {
            background-color: #f8f9fa;
        }
        .update-form {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-top: 30px;
        }
        .status-approved { color: #28a745; font-weight: bold; }
        .status-pending { color: #ffc107; font-weight: bold; }
        .status-requested { color: #17a2b8; font-weight: bold; }
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
    <div class="services-container">
        <h1>Update Additional Services</h1>
        <a href="dashboard.php" class="back-btn">← Back to Dashboard</a>
        
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
        
        <?php if (count($services) > 0): ?>
            <h3>Your Additional Services</h3>
            <table class="services-table">
                <tr>
                    <th>Service ID</th>
                    <th>Event Name</th>
                    <th>Client</th>
                    <th>Service Type</th>
                    <th>Description</th>
                    <th>Cost</th>
                    <th>Status</th>
                    <th>Request Date</th>
                </tr>
                <?php foreach($services as $service): ?>
                <tr>
                    <td><?=htmlspecialchars($service['service_id'])?></td>
                    <td><?=htmlspecialchars($service['event_name'])?></td>
                    <td><?=htmlspecialchars($service['first_name'] . ' ' . $service['last_name'])?></td>
                    <td><?=htmlspecialchars($service['service_type'])?></td>
                    <td><?=htmlspecialchars(substr($service['service_description'], 0, 50))?>...</td>
                    <td>$<?=number_format($service['service_cost'], 2)?></td>
                    <td class="status-<?=strtolower($service['status'])?>"><?=htmlspecialchars($service['status'])?></td>
                    <td><?=htmlspecialchars(date('m/d/Y', strtotime($service['request_date'])))?></td>
                </tr>
                <?php endforeach; ?>
            </table>
            
            <div class="update-form">
                <h3>Update Service</h3>
                <form method="post">
                    <div class="form-group">
                        <label for="service_id">Select Service to Update:</label>
                        <select id="service_id" name="service_id" required onchange="loadServiceDetails()">
                            <option value="">-- Choose a Service --</option>
                            <?php foreach($services as $service): ?>
                                <option value="<?=htmlspecialchars($service['service_id'])?>">
                                    Service ID <?=htmlspecialchars($service['service_id'])?> - <?=htmlspecialchars($service['service_type'])?> (<?=htmlspecialchars($service['event_name'])?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="service_type">Service Type:</label>
                        <input type="text" id="service_type" name="service_type" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="service_description">Service Description:</label>
                        <textarea id="service_description" name="service_description" rows="4"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="service_cost">Service Cost ($):</label>
                        <input type="number" id="service_cost" name="service_cost" step="0.01" min="0.01" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="status">Status:</label>
                        <select id="status" name="status" required>
                            <option value="Requested">Requested</option>
                            <option value="Pending">Pending</option>
                            <option value="Approved">Approved</option>
                            <option value="Completed">Completed</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn">Update Service</button>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <p>No additional services found for your events.</p>
        <?php endif; ?>
    </div>
    
    <script>
        const services = <?=json_encode($services)?>;
        
        function loadServiceDetails() {
            const serviceId = document.getElementById('service_id').value;
            const service = services.find(s => s.service_id == serviceId);
            
            if (service) {
                document.getElementById('service_type').value = service.service_type;
                document.getElementById('service_description').value = service.service_description;
                document.getElementById('service_cost').value = service.service_cost;
                document.getElementById('status').value = service.status;
            }
        }
    </script>
</body>
</html>
