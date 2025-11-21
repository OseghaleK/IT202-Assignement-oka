<?php
require 'config.php';

$first = trim($_POST['firstName'] ?? '');
$last = trim($_POST['lastName'] ?? '');
$pwd = trim($_POST['password'] ?? '');
$id = trim($_POST['idNumber'] ?? '');
$phone = trim($_POST['phoneNumber'] ?? '');
$email = trim($_POST['email'] ?? '');
$action = $_POST['action'] ?? '';

if (!empty($email)) {
    $stmt = $pdo->prepare("SELECT * FROM caterer WHERE first_name=? AND last_name=? AND password=? AND caterer_id=? AND email=?");
    $stmt->execute([$first, $last, $pwd, $id, $email]);
} else {
    $stmt = $pdo->prepare("SELECT * FROM caterer WHERE first_name=? AND last_name=? AND password=? AND caterer_id=?");
    $stmt->execute([$first, $last, $pwd, $id]);
}

$caterer = $stmt->fetch();

if ($caterer) {
    $_SESSION['caterer_id'] = $caterer['caterer_id'];
    $_SESSION['first_name'] = $caterer['first_name'];
    $_SESSION['last_name'] = $caterer['last_name'];
    $_SESSION['transaction'] = $action;
    
    switch($action) {
        case "search": 
            header("Location: search_caterer.php"); 
            break;
        case "book": 
            header("Location: booking_create.php"); 
            break;
        case "cancel": 
            header("Location: booking_cancel.php"); 
            break;
        case "request": 
            header("Location: additional_service_request.php"); 
            break;
        case "update": 
            header("Location: additional_service_update.php"); 
            break;
        case "create": 
            header("Location: client_create.php"); 
            break;
        default: 
            header("Location: dashboard.php"); 
    }
    exit;
} else {
    echo "<script>alert('Caterer not found. Please check your login details.');window.location='cc_form.html';</script>";
}
?>
