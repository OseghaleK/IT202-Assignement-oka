<?php
require 'config.php';

$first = $_POST['firstName'] ?? '';
$last = $_POST['lastName'] ?? '';
$password = $_POST['password'] ?? '';
$id = $_POST['repId'] ?? '';
$email = $_POST['email'] ?? '';
$emailConfirm = isset($_POST['emailConfirm']);

$stmt = $pdo->prepare("SELECT * FROM caterer WHERE first_name=? AND last_name=? AND password=? AND caterer_id=?".($emailConfirm ? " AND email=?" : ""));
$params = [$first,$last,$password,$id];
if($emailConfirm) $params[] = $email;
$stmt->execute($params);
$caterer = $stmt->fetch();

if($caterer){
    session_start();
    $_SESSION['caterer_id'] = $caterer['caterer_id'];
    header("Location:index.php");
    exit;
}else{
    echo "<script>alert('Caterer not found. Please check your login details.');window.location.href='cc_form.html';</script>";
}
?>
