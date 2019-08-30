<?php
//1 VERY simple input validation
if (strlen($_POST['year'])<4 || strlen($_POST['email'])<6 ||
strlen($_POST['location'])<1) {
 header("Location: index.php?message=Enter valid data");
 exit();
}
//2 check for correct answer
if ($_POST['year']!="2003") {
    header("Location: index.php?message=That was not the correct answer, try again");
    exit();
}

//create new pass instance
require_once("Pass.php");
$coupon = new Pass("pass/source");
?>