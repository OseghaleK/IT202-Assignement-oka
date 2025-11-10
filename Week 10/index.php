<?php
session_start();
$HOST='localhost';
$PORT=22;
$USER='oka';
$PASS='Helenakh2025';
$DB='oka';

$mysqli=mysqli_init();
$mysqli->options(MYSQLI_OPT_CONNECT_TIMEOUT,5);
if(!$mysqli->real_connect($HOST,$USER,$PASS,$DB,$PORT,null)){http_response_code(500);exit('Connection failed (TCP): '.mysqli_connect_error());}
mysqli_set_charset($mysqli,'utf8mb4');

$notFound=false;
if($_SERVER['REQUEST_METHOD']==='POST'){
  $idnum=trim($_POST['idnum']??'12345');
  if($idnum!==''&&ctype_digit($idnum)){
    $_SESSION['ID_NUMBER']=(int)$idnum;
    $stmt=$mysqli->prepare("SELECT 1 FROM Students WHERE ID_NUMBER=?");
    $stmt->bind_param("i",$_SESSION['ID_NUMBER']);
    $stmt->execute();
    $stmt->store_result();
    if($stmt->num_rows>0){$stmt->close();$mysqli->close();header("Location: display.php");exit;}else{$notFound=true;}
    $stmt->close();
  }else{$notFound=true;}
}
$prefill=htmlspecialchars($_POST['idnum']??'12345');
?>
<!DOCTYPE html>
<html>
<body>
<h1>Form</h1>
<form method="post">
<label for="idnum">Enter ID number of student : </label>
<input type="text" name="idnum" value="<?php echo $prefill; ?>">
<input type="submit" value="Submit">
</form>
<?php if($notFound){echo "0 results";}?>
</body>
</html>
