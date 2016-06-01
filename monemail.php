<?php
require_once("includes/startsession.php");
$page_title = "Monitor";
require_once("includes/header.php");
require_once ("database/MonitorSection.php");
require_once("includes/PHPMailer/PHPMailerAutoload.php")
//define the receiver of the email
?>
    <div id="content">
<?php
$result = MonitorSection::getID($_GET['id']);
if (mysqli_num_rows($result) > 0) {
    // looping through all results
    // products node
    //$response["pdfs"] = array();
    ?>

    <?php
    while ($row = mysqli_fetch_array($result)) {
        //echo json_encode($row);
        $email = $row['Email'];
    }
}
error_reporting(-1);
ini_set('display_errors', 1);
set_error_handler("var_dump");

$to = array ($email);
$subject = "Import Extranet Primary";
$body = "<h4>Extranet Primary</h4>Import to database: extranet primary 2015 on ";
echo $body;
send_email ($body, $to, $subject);

function send_email ($body, $to, $subject) {
    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Host = "10.1.0.49";
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = "ssl";
    $mail->Username = "xavier@eeb2.be";
    $mail->Password = "Varken90";
    $mail->Port = 465;
    $mail->IsHTML(true);
    $mail->SetFrom('extranet@eeb2.eu', 'Extranet');
    $mail->Subject = $subject;
    $mail->MsgHTML($body);
    foreach ($to as $contact) {
        $mail->AddAddress($contact, "Xavier");
        if(!$mail->Send())
            echo "Mailer Error: " . $mail->ErrorInfo;
        else
            echo "</br>Import completed! Mail has been sent to ".$contact."!";
    }
}
?>
</div>
        <?php
require_once("includes/footer.php");
?>