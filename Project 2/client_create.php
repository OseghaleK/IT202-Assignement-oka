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
    $first = trim($_POST['first_name'] ?? '');
    $last = trim($_POST['last_name'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    
    if($first === '') $errors[] = 'First name is required';
    if($last === '') $errors[] = 'Last name is required';
    if($phone === '') $errors[] = 'Phone number is required';
    if($email === '') $errors[] = 'Email is required';
    
    if(empty($errors)){
        $stmt = $pdo->prepare("
            INSERT INTO client (first_name, last_name, address, phone, email) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$first, $last, $address, $phone, $email]);
        $success = 'Client created successfully!';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Create Client Account - Culinary Connoisseurs</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="form-box">
        <h1>Create New Client Account</h1>
        
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
                <label for="first_name">First Name:</label>
                <input type="text" id="first_name" name="first_name" required placeholder="Enter client's first name">
            </div>
            
            <div class="form-group">
                <label for="last_name">Last Name:</label>
                <input type="text" id="last_name" name="last_name" required placeholder="Enter client's last name">
            </div>
            
            <div class="form-group">
                <label for="address">Address:</label>
                <input type="text" id="address" name="address" placeholder="Full address (street, city, state, zip)">
            </div>
            
            <div class="form-group">
                <label for="phone">Phone Number:</label>
                <input type="tel" id="phone" name="phone" required placeholder="123-456-7890">
            </div>
            
            <div class="form-group">
                <label for="email">Email Address:</label>
                <input type="email" id="email" name="email" required placeholder="client@example.com">
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn">Create Client Account</button>
                <a href="dashboard.php" class="btn" style="background-color: #6c757d; text-decoration: none; display: inline-block; text-align: center; margin-left: 10px;">Cancel</a>
            </div>
        </form>
    </div>
    
    <script>
        document.getElementById('phone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 6) {
                value = value.slice(0, 3) + '-' + value.slice(3, 6) + '-' + value.slice(6, 10);
            } else if (value.length >= 3) {
                value = value.slice(0, 3) + '-' + value.slice(3);
            }
            e.target.value = value;
        });
    </script>
</body>
</html>
