<?php
session_start();
session_destroy();
header("Location: cc_form.html");
exit;
?>
