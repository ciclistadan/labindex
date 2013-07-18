
<?php
$to      = 'dan.rozelle@gmail.com';
$subject = 'Comments from LabIndex.com';
$message = $_POST['message'];


if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    $headers = 'From: '.$_POST['email']."\r\n";
}
else{
	$headers = 'From: dan.rozelle@gmail.com' . "\r\n";
}	

	$headers .= 'X-Mailer: PHP/' . phpversion();

if(mail($to, $subject, $message, $headers)){echo "success";}
	else{ echo "failure";}

?>