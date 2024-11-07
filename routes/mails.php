<?php 
use App\Http\Controllers\MailController;
use App\Http\Requests\EmailRequest;

$router->get('/send-email-test', [MailController::class, 'sendTestEmail'])->name('sendtest.email');
$router->get('/send-email', [MailController::class, 'viewEmail'])->name('view.email');
$router->post('/send-email-data', [MailController::class, 'sendEmail'])->name('send.email');