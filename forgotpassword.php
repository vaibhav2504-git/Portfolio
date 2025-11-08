<?php

$email = mysqli_real_escape_string($con, $_POST["email"] ?? '');
$message = mysqli_real_escape_string($con, $_POST["message"] ?? '');
$sb = mysqli_real_escape_string($con, $_POST["sb"] ?? '');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
function sendMail($email) 
{
   require('PHPMailer/PHPMailer.php');
   require('PHPMailer/SMTP.php');
   require('PHPMailer/Exception.php');

   $mail = new PHPMailer(true);

   try {
    //Server settings
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = 'fundraiser0069@gmail.com';                     //SMTP username
    $mail->Password   = 'mkpidizlegyqjdmn';                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
    $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom('fundraiser0069@gmail.com', 'Fundraiser');

    $mail->addAddress($email);     //Add a recipient


    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = 'confirmation comment';
    $mail->Body    = "We got a request to reset the password for your account. <br>
                    <a > we got your response, we will contact soon </a>";

    $mail->send();
    return true;
    }

     catch (Exception $e) {
      return false;
    }
  }


if(isset($_POST['sb'])) 
{
  if($result) {
     
          if(mysqli_query($con,$query) && sendMail($_POST['email']))
          {
            echo"
            <script> alert('Password Reset Link Sent! Check Your Email');
            </script>
            "; 
          }
          else{
            echo"
            <script> alert('Server Down! Try Again Later');
            window.location.href='login.html';
            </script>
            ";
          }
      

      else{
        echo"
    <script> alert('Email Not Found');
    window.location.href='login.html';
    </script>
    ";
      }
    }

}
?>