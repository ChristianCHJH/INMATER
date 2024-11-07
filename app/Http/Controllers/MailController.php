<?php
namespace App\Http\Controllers;

use App\Services\MailFactory;
use App\Http\Requests\EmailRequest;
use Jenssegers\Blade\Blade;
use App\Helpers\ResponseHelper;
use App\Helpers\Storage;

class MailController extends Controller
{
    protected $mailService;

    public function __construct(Blade $blade)
    {
        $this->blade = $blade;
        $this->mailService = MailFactory::create();
    }

    public function sendTestEmail()
    {
        $to = ['fidelleandro@msn.com', 'fileceru@gmail.com'];
        $subject = 'Bienvenido';
        $template = 'emails.welcome';
        $data = [
            'title' => 'Bienvenido a Nuestro Sitio',
            'body' => 'Estamos encantados de tenerte con nosotros.'
        ];
        $cc = ['friedelruiz4@gmail.com'];
        $bcc = ['friedelruiz8@gmail.com'];
        // Archivos adjuntos (pueden ser rutas absolutas de archivos en el servidor)
        $attachments = [
            '/path/to/file1.pdf',
            '/path/to/file2.jpg'
        ];

        if ($this->mailService->send($to, $subject, $template, $data, $cc, $bcc, $attachments)) {
            echo 'Correo enviado correctamente';
        } else {
            echo 'Error al enviar el correo';
        }
        exit;
    }
    public function viewEmail()
    {
        echo $this->blade->make('emails.send')->render();
        exit;
    }
    public function sendEmail()
    {
        $validatemsj = [];
        $attachments = [];

        $response['status'] = false;
        try {
            $request2 = new EmailRequest();
            $validatedData = $request2->validate($validatemsj); 

            if (json_decode($validatedData['to'], true) == '') {
                throw new \Exception("El campo para es requerido!!!");
            }

            if (trim($validatedData['body']) == '') {
                throw new \Exception("El cuerpo del mensaje es requerido!!!");
            }
            
            $to = array_map(function ($item) { return $item['value']; }, json_decode($validatedData['to'], true));
            $subject = $validatedData['subject'];
            $body = $validatedData['body'];
            $cc = $validatedData['cc'] ? array_map(function ($item) { return $item['value']; }, json_decode($validatedData['cc'], true)) : [];
            $bcc = $validatedData['bcc'] ? array_map(function ($item) { return $item['value']; }, json_decode($validatedData['bcc'], true)) : [];

            $data = [
                'title' => $subject,
                'body' => $body
            ];
            
            $template = 'emails.welcome'; 
             
            // Manejo de archivos adjuntos
            if ($request2->hasFile('attachments')) {  
                $attachments = Storage::handleAttachments($_FILES['attachments']);
            }  

            if (!$this->mailService->send($to, $subject, $template, $data, $cc, $bcc, $attachments)) {
                throw new \Exception("Error al enviar el correo.");
            }

            $response['status'] = true;
            $response['message'] = 'Correo enviado correctamente';
        } catch (\Exception $e) {
            $response['message'] = $e->getMessage();
        }

        return ResponseHelper::json([
            'status' => $response['status'],
            'message' => $response['message'],
            'html' => $validatemsj
        ]);
    }

}
