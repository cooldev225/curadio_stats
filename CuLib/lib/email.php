<?PHP
/*
error_reporting(E_ALL);

require 'mail/class.phpmailer.php';

$mail = new PHPMailer;

$mail->IsSMTP();                                      // Set mailer to use SMTP
//$mail->Host = 'smtp1.example.com;smtp2.example.com'; // Specify main and backup server
$mail->Host = 'smtp.dd24.net';  
$mail->SMTPAuth = true;                               // Enable SMTP authentication
$mail->Username = 'khvicha@cugate.com';                            // SMTP username
$mail->Password = 'Dioskuria';                           // SMTP password
$mail->SMTPSecure = 'ssl';							// Enable encryption, 'ssl' also accepted
$mail->Port = 465;                        

$mail->From = 'khvicha@cugate.com';
$mail->FromName = 'CUGATE Ltd..';
$mail->AddAddress('khvicha@gmail.com', 'Khvicha Chikhladze');  // Add a recipient
//$mail->AddAddress('ellen@example.com');               // Name is optional
$mail->AddReplyTo('khvicha@cugate.com', 'CUGATE Ltd.');
//$mail->AddCC('cugate.khvicha@gmail.com', 'Cugate - Khvicha Chikhladze');
//$mail->AddCC('amz.khvicha@gmail.com', 'AMAZON - Khvicha Chikhladze');
//$mail->AddBCC('dropbox.khvicha@gmaail.com', 'DROPBOX - Kkhvicha Chikhlazde');
$mail->AddBCC('khvicha73@gmaail.com', '73 - Kkhvicha Chikhlazde');

$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
$mail->AddAttachment('C:\\Temp_Upload\\cd.jpg');         // Add attachments
$mail->AddAttachment('C:\\Temp_Upload\\Sony.jpg');         // Add attachments
//$mail->AddAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
$mail->IsHTML(true);                                  // Set email format to HTML

$mail->Subject = 'Subject from Cugate';
$mail->Body    = 'This is the HTML message body <b>in bold!</b>';
$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

if(!$mail->Send()) {
   echo 'Message could not be sent.';
   echo 'Mailer Error: ' . $mail->ErrorInfo;
   exit;
}

echo 'Message has been sent';
*/



function cug_check_email_syntax($email) {
	
	if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
		return true; 
	}	
	else {
		return false;
	}
}
?>