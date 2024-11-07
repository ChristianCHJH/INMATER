<?php

namespace App\Services;

class MailFactory
{
    public static function create()
    {
        $mailService = env('MAIL_SERVICE');

        switch ($mailService) {
            case 'swiftmailer':
                return new MailSwiftService();
            case 'phpmailer':
            default:
                return new MailPHPMailerService();
        }
    }
}
