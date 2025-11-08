<?php
   use PHPMailer\PHPMailer\PHPMailer;
   use PHPMailer\PHPMailer\SMTP;
   use PHPMailer\PHPMailer\Exception;

   require_once __DIR__ . '/vendor/phpmailer/src/Exception.php';
   require_once __DIR__ . '/vendor/phpmailer/src/PHPMailer.php';
   require_once __DIR__ . '/vendor/phpmailer/src/SMTP.php';  
   require 'vendor/autoload.php';

   $mail = new PHPMailer;
   if(isset($_POST['send'])){
   
      // getting post values
    	
      $toemail=$_POST['email'];	
     
      $message=$_POST['message'];
      $mail->isSMTP();					      // Set mailer to use SMTP
      $mail->Host = 'smtp.gmail.com';             
      $mail->SMTPAuth = true;                     
      $mail->Username = 'fundraiser0069@gmail.com';	// SMTP username
      $mail->Password = 'mkpidizlegyqjdmn'; 		// SMTP password

      // Enable TLS encryption, 'ssl' also accepted
      $mail->SMTPSecure = 'tls';
      $mail->Port = 587;                          
      $mail->setFrom('fundraiser0069@gmail.com', 'fundraiser');
      $mail->addReplyTo('fundraiser0069@gmail.com', 'fundraiser');
      $mail->addAddress($email);   	  // Add a recipient
      $mail->isHTML(true);                // Set email format to HTML
      $bodyContent=$message;
   
      $body .='<p>'.$message.'</p>';
      $mail->Body = $body;

      if(!$mail->send()) {
         echo 'Message could not be sent.';
         echo 'Mailer Error: ' . $mail->ErrorInfo;
      } else {
         echo 'Message has been sent';
      }
   }
?>