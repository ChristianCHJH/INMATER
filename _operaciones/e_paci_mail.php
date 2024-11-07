<?php
session_start();
require($_SERVER["DOCUMENT_ROOT"] . "/_database/database.php");
require($_SERVER["DOCUMENT_ROOT"] . "/config/environment.php");
include($_SERVER["DOCUMENT_ROOT"] . '/_libraries/php_mailer/PHPMailer.php');
include($_SERVER["DOCUMENT_ROOT"] . '/_libraries/php_mailer/Exception.php');
include($_SERVER["DOCUMENT_ROOT"] . '/_libraries/php_mailer/SMTP.php');

$login = "";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!!$_SESSION) {
    $login = $_SESSION['login'];
} else {
    http_response_code(400);
    echo json_encode(["message" => "no se ha iniciado sesión"]);
    exit();
}

if (isset($_POST["tipo"]) && !empty($_POST["tipo"])) {
    switch ($_POST["tipo"]) {
        case 'enviar_acceso':
            http_response_code(200);
            echo json_encode(["message" => enviar_acceso($_POST["email"], ucwords(mb_strtolower($_POST["nombres"])), $_POST["dni"], $login)]);
            break;
        
        default:
            http_response_code(400);
            echo json_encode(["message" => "la operacion no existe"]);
            break;
    }
} else {
    // descargar_base();
    http_response_code(400);
    echo json_encode(["message" => "no se enviaron los parametros correctamente"]);
    exit();
}

function enviar_acceso($email, $nombres, $usuario, $login) {
    $url_login = '';
    if ($_ENV["DEV"] == true) {
        $url_login = 'http://localhost/login.php';
        $email = 'asesorias.virtuales.sede23@gmail.com';
        $nombres = 'Demo Prueba';
    } else {
        $url_login = 'https://app.inmater.pe/login.php';
    }
    // generacion de la clave de acceso
    $length = 10;
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $acceso = '';
    for ($i = 0; $i < $length; $i++) {
        $acceso .= $characters[rand(0, $charactersLength - 1)];
    }

    // enviar email
    try {
        $mail = new PHPMailer(true);
        $mail->SMTPOptions = array(
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        );

        $mail->SMTPDebug = 2; // enable verbose debug output
        $mail->isSMTP(); // set mailer to use SMTP
        $mail->Host = 'smtp.gmail.com'; // specify main and backup SMTP servers
        $mail->SMTPAuth = true; // enable SMTP authentication
        $mail->Username = $_ENV["mail_user"]; // SMTP username
        $mail->Password = $_ENV["mail_pass"]; // SMTP password
        $mail->SMTPSecure = 'tls'; // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 587; // TCP port to connect to
        $mail->setFrom($_ENV["mail_user"], $_ENV["mail_name"]);
        $mail->addAddress($email, $nombres); // Add a recipient

        /* $mail->addAddress('ellen@example.com'); // Name is optional
        $mail->addReplyTo('info@example.com', 'Information');
        $mail->addCC('cc@example.com');
        $mail->addBCC('bcc@example.com'); */
        /* $mail->addAttachment('/var/tmp/file.tar.gz'); // Add attachments */

        // Content
        $mail->isHTML(true); // Set email format to HTML
        $mail->Subject = utf8_decode("Bienvenido al portal de la Clínica Inmater");
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
                    <td align="center" valign="top" width="100%" bgcolor="#72a2aa" style="background: #72a2aa repeat scroll 00; padding: 0px 15px 0 15px;" class="mobile-padding">
                        <table align="center" border="0" cellpadding="0" cellspacing="0" width="600" class="full-width">
                            <tr>
                                <td align="center" valign="top" style="padding: 0; font-family: Open Sans, Helvetica, Arial, sans-serif;">
                                    <h1 style="font-size: 18px; color: #ffffff;">
                                        Bienvenido al portal de la<br>Clínica INMATER
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
                                    <img src="https://app.inmater.pe/_images/logo_login_sinfondo.png" width="180" heigth="50" style="display: block; margin: 10px auto;"/>
                                    <p style="color: #444444; font-size: 15px; line-height: 32px; margin: 0; padding: 0 30px;">
                                        <span><b>Estimado: ' . $nombres . '</b></span><br>
                                        Con el siguiente acceso, usted podrá visualizar sus documentos de Ecografías:<br>
                                        <span style="font-size:20px;"><b>usuario:</b> ' . $usuario . '<br><b>contraseña:</b> ' . $acceso . '</span>
                                    </p>
                                </td>
                            </tr>

                            <tr>
                                <td align="center" style="border-bottom: 2px solid rgb(238, 238, 238); padding: 40px 0px;">
                                    <table border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td align="center" style="border-radius: 28px;" bgcolor="#006B9C">
                                                <a href="' . $url_login . '" target="_blank" style="font-size: 16px; font-family: Open Sans, Helvetica, Arial, sans-serif; color: #fff; text-decoration: none; border-radius: 28px; background-color: #72a2aa; padding: 10px 30px; border: 1px solid #72a2aa; display: block;">Ir al portal</a>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>                
                        </table>
                    </td>
                </tr>

                <tr>
                    <td align="center" height="100%" valign="top" width="100%" bgcolor="#f6f6f6" style="padding: 20px 15px; font-size: 12px;" class="mobile-padding">
                        <table align="center" border="0" cellpadding="0" cellspacing="0" width="600" class="mobile-wrapper">
                            <tr>
                                <td align="center" valign="top" style="padding: 0 0 5px 0; font-family: Open Sans, Helvetica, Arial, sans-serif;">
                                    ESPECIALISTAS EN MEDICINA REPRODUCTIVA S.A.C. - Todos los derechos reservados © 2019<br>
                                    AV. GUARDIA CIVIL NRO. 655 URB. CORPAC<br>
                                    https://www.inmater.pe
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
        $error = '';
    } catch (Exception $e) {
        /* echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}"; */
        $error = $mail->ErrorInfo;
    }

    // registrar acceso
    global $db;
    $stmt = $db->prepare("SELECT * FROM hc_paciente_accesos where dni = ? and estado = 1;");
    $stmt->execute([$usuario]);

    if ($stmt->rowCount() == 0) {
        $stmt = $db->prepare("INSERT INTO hc_paciente_accesos
        (acceso, dni, email, nombres, idusercreate) values (?, ?, ?, ?, ?)");
        $stmt->execute([$acceso, $usuario, $email, $nombres, $login]);
    } else {
        $stmt = $db->prepare("UPDATE hc_paciente_accesos
        set acceso=?, email=?, nombres=?, iduserupdate=?
        where dni=?");
        $stmt->execute([$acceso, $email, $nombres, $login, $usuario]);
    }

    return $error;
} ?>