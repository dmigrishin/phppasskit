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

//predefine store locations
$locations = array(
    array("latitude"=>43.789708,"longitude"=>131.948114), 
    "altitude"=>30.0);

$coupon = new Pass("pass/source");

//fill in dynamic data
$coupon->content['serialNumber'] = (string)uniqid();
$coupon->content['coupon']['secondaryFields'][0]['value'] =
 (string)$_POST['name'];

 $coupon->content['locations'][0] =
 $locations[(int)$_POST['location']];
 $coupon->writePassJSONFile();

$coupon->writeRecursiveManifest();
$coupon->writeSignatureWithKeysPathAndPassword("pass", 'appleadopt');
$fileName = $coupon->writePassBundle();

//send over the pass file
require_once("lib.php");

$success = sendFileToEmailWithTitleAndMessage(
    $_POST['email'],
    "Your coupon has arrived",
    $_POST['name'].", Thank you for participating!",
    "noreply@yourdomain.com", $fileName);
 
 //show thank you message
 header("Location: index.php?message=Thank you for participating in our bonus program. <br/>Your coupon has been sent to ".$_POST['email']);

?>