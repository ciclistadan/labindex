
<?php
$to      = 'ciclistadan@live.com';
$subject = 'the subject';
$message = 'hello';
$headers = 'From: dan.rozelle@gmail.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

mail($to, $subject, $message, $headers)

?>