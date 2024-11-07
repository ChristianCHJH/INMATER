<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Jenssegers\Blade\Blade;

class MailPHPMailerService
{
    protected $mail;
    protected $blade;

    public function __construct()
    {
        $this->mail = new PHPMailer(true);
        $this->blade = new Blade('resources/views', 'resources/cache');
        $this->configure();
    }

    protected function configure()
    {
        $this->mail->isSMTP();
        //$this->mail->SMTPDebug = 2; 
        $this->mail->Host = env('MAIL_HOST');
        $this->mail->SMTPAuth = true;
        $this->mail->Username = env('MAIL_USERNAME');
        $this->mail->Password = env('MAIL_PASSWORD');
        $this->mail->SMTPSecure = env('MAIL_ENCRYPTION');
        $this->mail->Port = env('MAIL_PORT');
        $this->mail->CharSet = 'UTF-8';
        $this->mail->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME')); 
    }

    public function send($to, $subject, $template, $data, $cc = [], $bcc = [],$attachments = [])
    {
        try {
            $body = $this->blade->render($template, $data);

            if (is_array($to)) {
                foreach ($to as $recipient) {
                    $this->mail->addAddress($recipient);
                }
            } else {
                $this->mail->addAddress($to);
            }

            if (is_array($cc)) {
                foreach ($cc as $ccRecipient) {
                    $this->mail->addCC($ccRecipient);
                }
            }

            if (is_array($bcc)) {
                foreach ($bcc as $bccRecipient) {
                    $this->mail->addBCC($bccRecipient);
                }
            }

            foreach ($attachments as $filePath) {
                if (file_exists($filePath)) {
                    $this->mail->addAttachment($filePath);
                } else {
                    throw new Exception("Attachment not found: $filePath");
                }
            }

            $this->mail->Subject = $subject;
            $this->mail->Body = $body;
            $this->mail->isHTML(true);

            $this->mail->send();
            return true;
        } catch (Exception $e) {
            //error_log($e->getMessage());
            //print_r($e->getMessage());
            return false;
        }
    }
}
