<?php
namespace App\Services;

use Swift_SmtpTransport;
use Swift_Mailer;
use Swift_Message;
use Swift_Attachment;
use Jenssegers\Blade\Blade;

class MailSwiftService
{
    protected $mailer;
    protected $blade;

    public function __construct()
    {
        $this->blade = new Blade('resources/views', 'resources/cache');
        $this->configure();
    }

    protected function configure()
    {
        $transport = (new Swift_SmtpTransport(env('MAIL_HOST'), env('MAIL_PORT')))
            ->setUsername(env('MAIL_USERNAME'))
            ->setPassword(env('MAIL_PASSWORD'))
            ->setEncryption(env('MAIL_ENCRYPTION'));

        $this->mailer = new Swift_Mailer($transport);
    }

    public function send($to, $subject, $template, $data, $cc = [], $bcc = [], $attachments = [])
    {
        $body = $this->blade->render($template, $data);

        $message = (new Swift_Message($subject))
            ->setFrom([env('MAIL_FROM_ADDRESS') => env('MAIL_FROM_NAME')])
            ->setBody($body, 'text/html');

        if (is_array($to)) {
            $message->setTo($to);
        } else {
            $message->setTo([$to]);
        }

        if (is_array($cc) && !empty($cc)) {
            $message->setCc($cc);
        }

        if (is_array($bcc) && !empty($bcc)) {
            $message->setBcc($bcc);
        }

        // Añadir archivos adjuntos
        foreach ($attachments as $filePath) {
            if (file_exists($filePath)) {
                $message->attach(Swift_Attachment::fromPath($filePath));
            } else {
                throw new \Exception("Attachment not found: $filePath");
            }
        }

        try {
            $this->mailer->send($message);
            return true;
        } catch (\Exception $e) {
            // Manejo del error (puedes registrar el error si lo deseas)
            // error_log($e->getMessage());
            return false;
        }
    }
}
