<?php
require_once('email_config.php');
require('phpmailer/PHPMailer/PHPMailerAutoload.php');

$message = [];
$output = [
        "success" => null,
        "messages" => []
];

$message["name"] = filter_var($_POST["contactName"], FILTER_SANITIZE_STRING);
if (empty($message["name"])){
    $output["success"] = false;
    $output["messages"][] = "missing name key";
}

$message["email"] = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
if (empty($message["email"])){
    $output["success"] = false;
    $output["messages"][] = "Invalid email key";
}

$message["message"] = filter_var($_POST["comments"], FILTER_SANITIZE_STRING);
if (empty($message["message"])){
    $output["success"] = false;
    $output["messages"][] = "missing message key";
}


if ($output["success"] !== null) {
    http_response_code(400);
    echo json_encode($output);
    exit();
}

//$message ["phone"] = preg_filter ?
$mail = new PHPMailer;


$mail->SMTPDebug = 3;           // Enable verbose debug output. Change to 0 to disable debugging output.

$mail->isSMTP();                // Set mailer to use SMTP.
$mail->Host = 'smtp.gmail.com'; // Specify main and backup SMTP servers.
$mail->SMTPAuth = true;         // Enable SMTP authentication


$mail->Username = EMAIL_USER;   // SMTP username
$mail->Password = EMAIL_PASS;   // SMTP password
$mail->SMTPSecure = 'tls';      // Enable TLS encryption, `ssl` also accepted, but TLS is a newer more-secure encryption
$mail->Port = 587;              // TCP port to connect to
$options = array(
    'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    )
);
$mail->smtpConnect($options);
$mail->From = 'fuze.php.mailer@gmail.com';  // sender's email address (shows in "From" field)
$mail->FromName = 'Brett\'s Fuze Mailer';   // sender's name (shows in "From" field)
$mail->addAddress('brettalbright@outlook.com', 'Brett Albright');  // Add a recipient
//$mail->addAddress('ellen@example.com');                        // Name is optional
$mail->addReplyTo($_POST["email"]);                          // Add a reply-to address
//$mail->addCC('cc@example.com');
//$mail->addBCC('bcc@example.com');

//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
$mail->isHTML(true);

$message["subject"] = $message["name"] . " has sent you a message on your portfolio";


// Set email format to HTML

$mail->Subject = 'New Subject';
$currentDate = date('y-m-d H:i:s');
$mail->Body    = "<div>Name: {$_POST["name"]}</div>
                  <div>Email: {$_POST["email"]}</div> 
                  <div>Subject: {$_POST["subject"]}</div>
                  <div>Message: {$_POST["body"]}</div>
                  <div>Meta data: {$_SERVER["REMOTE_ADDR"]} at {$currentDate}</div> ";
$mail->AltBody = "Name: {$_POST["name"]}
                  Email: {$_POST["email"]} 
                  Subject: {$_POST["subject"]}
                  Message: {$_POST["body"]}
                  Meta data: {$_SERVER["REMOTE_ADDR"]} at {$currentDate}";



if(!$mail->send()) {
$output["success"] = false;
$output['messages'][] = 'Mailer Error: ' . $mail->ErrorInfo;
} else {
$output['messages'] =  'Message has been sent';
$output['success']= true;
}

print( json_encode($output));

?>