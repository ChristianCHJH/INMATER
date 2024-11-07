<?php
require($_SERVER["DOCUMENT_ROOT"] . "/_database/database.php");
require($_SERVER["DOCUMENT_ROOT"] . "/config/environment.php");

include('../_libraries/php_mailer/PHPMailer.php');
include('../_libraries/php_mailer/Exception.php');
include('../_libraries/php_mailer/SMTP.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (isset($_POST["tipo"]) && !empty($_POST["tipo"]) &&
        isset($_POST["dni"]) && !empty($_POST["dni"]) &&
        isset($_POST["login"]) && !empty($_POST["login"])) {

    $dni = $_POST["dni"];
    $login = $_POST["login"];
    $email = $_POST["email"];
    $acceso = generateRandomString(8);

    enviar_correo_acceso_paciente($email, $dni, $acceso);

    agregar_paciente_acceso([
        'dni' => $dni,
        'acceso' => $acceso,
        'login' => $login,
        'email' => $email
    ]);
}

function agregar_paciente_acceso($data)
{
    
    global $db;

    $stmt = $db->prepare("update hc_paciente_accesos set estado = 0 where estado = 1 and dni = ?");
    $stmt->execute(array($data['dni']));

    $stmt = $db->prepare("insert into hc_paciente_accesos (dni, email, acceso, idusercreate) values (?, ?, ?, ?)");
    $stmt->execute(array($data['dni'], $data['email'], $data['acceso'], $data['login']));
}

function generateRandomString($length = 8) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';

    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

    return $randomString;
}

function enviar_correo_acceso_paciente(
    $address = 'jose.villasantem@gmail.com',
    $user = 'demo',
    $password = 'demo',
    $subject = 'Bienvenido al portal del Centro Médico Inmater',
    $address_name = '') {
    $mail = new PHPMailer(true);

    try {
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        //Server settings
        $mail->SMTPDebug = 2; // Enable verbose debug output
        $mail->isSMTP(); // Set mailer to use SMTP
        $mail->Host = 'smtp.gmail.com'; // Specify main and backup SMTP servers
        $mail->SMTPAuth = true; // Enable SMTP authentication
        $mail->Username = $_ENV["mail_user"]; // SMTP username
        $mail->Password = $_ENV["mail_pass"]; // SMTP password
        $mail->SMTPSecure = 'tls'; // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 587; // TCP port to connect to

        $mail->setFrom($_ENV["mail_user"], $_ENV["mail_name"]);
        $mail->addAddress($address, $address_name); // Add a recipient
        /*
        $mail->addAddress('ellen@example.com'); // Name is optional
        $mail->addReplyTo('info@example.com', 'Information');
        $mail->addCC('cc@example.com');
        $mail->addBCC('bcc@example.com');
        */

        /*
        // Attachments
        $mail->addAttachment('/var/tmp/file.tar.gz'); // Add attachments
        $mail->addAttachment('/tmp/image.jpg', 'new.jpg'); // Optional name
        */

        // Content
        $mail->isHTML(true); // Set email format to HTML
        $mail->Subject = utf8_decode($subject);
        $mail->Body = '
            <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
            <!doctype html>
            <html>
                <head>
                <title>Bienvenida</title>
                <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
                <style type="text/css">
                    body,table,td,a{
                        -webkit-text-size-adjust:100%;
                        -ms-text-size-adjust:100%;
                    }
                    table,td{
                        mso-table-lspace:0pt;
                        mso-table-rspace:0pt;
                    }
                    img{
                        -ms-interpolation-mode:bicubic;
                    }
                    img{
                        border:0;
                        height:auto;
                        line-height:100%;
                        outline:none;
                        text-decoration:none;
                    }
                    table{
                        border-collapse:collapse !important;
                    }
                    body{
                        height:100% !important;
                        margin:0 !important;
                        padding:0 !important;
                        width:100% !important;
                    }
                    a[x-apple-data-detectors]{
                        color:inherit !important;
                        text-decoration:none !important;
                        font-size:inherit !important;
                        font-family:inherit !important;
                        font-weight:inherit !important;
                        line-height:inherit !important;
                    }
                    div[style*=margin: 16px 0;]{
                        margin:0 !important;
                    }
                </style>
            </head>
            <body style="margin: 0 !important; padding: 0; !important background-color: #ffffff;" bgcolor="#ffffff">
            <!-- HIDDEN PREHEADER TEXT -->
            <div style="display: none; font-size: 1px; color: #fefefe; line-height: 1px; font-family: Open Sans, Helvetica, Arial, sans-serif; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden;"></div>

            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td align="center" valign="top" width="100%" bgcolor="#72a2aa" style="background: #72a2aa repeat scroll 00; padding: 10px 15px 0 15px;" class="mobile-padding">
                        <table align="center" border="0" cellpadding="0" cellspacing="0" width="600" class="full-width">
                            <tr>
                                <td align="center" valign="top" style="padding: 0; font-family: Open Sans, Helvetica, Arial, sans-serif;">
                                    <h1 style="font-size: 32px; color: #ffffff;">
                                        Bienvenido al portal del<br>CENTRO MÉDICO INMATER
                                    </h1>
                                    <p style="font-style: italic; color: #313639; font-size: 20px; line-height: 25px; margin: 0;">
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align="center" height="100%" valign="top" width="100%" style="padding: 0 15px;" class="mobile-padding">
                        <table align="center" border="0" cellpadding="0" cellspacing="0" width="600" class="full-width">
                            <tr>
                                <td align="center" valign="top" style="font-family: Open Sans, Helvetica, Arial, sans-serif;">
                                    <img src="https://app.inmater.pe/_images/logo_login_sinfondo.png" width="236" heigth="76" style="display: block; margin: 10px auto;"/>
                                    <p style="color: #444444; font-size: 15px; line-height: 32px; margin: 0;">
                                        Con los siguientes accesos, usted podrá subir y gestionar los documentos de atención (Legal, Análisis clínico, Andrología, Riesgo Quirúrgico, Examen psicológico, Cariotipo):<br><span style="font-size:20px;"><b>usuario:</b> ' . $user . '<br><b>contraseña:</b> ' . $password . '</span>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td align="center" style="border-bottom: 2px solid rgb(238, 238, 238); padding: 40px 0px;">
                                    <table border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td align="center" style="border-radius: 28px;" bgcolor="#006B9C">
                                                <a href="https://app.inmater.pe/login.php" target="_blank" style="font-size: 20px; font-family: Open Sans, Helvetica, Arial, sans-serif; color: #fff; text-decoration: none; border-radius: 28px; background-color: #72a2aa; padding: 10px 30px; border: 1px solid #72a2aa; display: block;">
                                                    Ir al portal
                                                </a>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>                
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align="center" height="100%" valign="top" width="100%" bgcolor="#f6f6f6" style="padding: 40px 15px;" class="mobile-padding">
                        <table align="center" border="0" cellpadding="0" cellspacing="0" width="600" class="mobile-wrapper">
                            <tr>
                                <td align="center" valign="top" style="padding: 0 0 5px 0; font-family: Open Sans, Helvetica, Arial, sans-serif;">
                                    ESPECIALISTAS EN MEDICINA REPRODUCTIVA S.A.C. - Todos los derechos reservados © 2019<br>
                                    AV. GUARDIA CIVIL NRO. 655 URB. CORPAC<br>
                                    inmater.pe
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            </body>
            </html>';
        /* $mail->AltBody = 'This is the body in plain text for non-HTML mail clients'; */

        $mail->send();
        echo 'Message has been sent';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>