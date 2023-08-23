<?php

require_once 'crudController.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require 'vendor/autoload.php';

class mailController extends crudController
{

  public function __construct()
  {
    parent::__construct();
    $this->response = new stdClass();
  }

  public function createTicketMail($ticketId, $userId, $titulo, $detalle, $anydesk)
  {
    $admins = $this->modularModel->getRegistrosTabla('user','nivel >= 9','',"O");
    $user = $this->modularModel->getRegistroID('user', $userId, "O");
    try {
      $url = "{$this->uri}ticketDetail/" . $ticketId;
      $clienteName = $user->username;
      foreach ($admins as $key => $admin) {
        $mail = $admin->mail;
        $adminName = $admin->username;
        $html = "
        <html>
        <head>
            <title>Nuevo ticket soporte</title>
        </head>
        <body style='font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;'>
            <table align='center' border='0' cellpadding='0' cellspacing='0' style='max-width: 600px; margin: 0 auto; background-color: #ffffff; border-collapse: collapse; border: 1px solid #ddd;'>
                <tr>
                    <td style='padding: 20px; text-align: center; background-color: #f38001;'>
                        <h1 style='color: #ffffff; margin: 0;'>Nuevo ticket soporte</h1>
                    </td>
                </tr>
                <tr>
                    <td style='padding: 20px;'>
                        <p>Hola, $adminName</p>
                        <p>Queremos informarte que se ha generado un nuevo ticket de soporte por parte de un cliente.</p>
                        <p>Detalles del Ticket:</p>
                        <ul>
                            <li><strong>Usuario:</strong> $clienteName</li>
                            <li><strong>Título:</strong> $titulo</li>
                            <li><strong>Descripción:</strong> $detalle</li>
                            <li><strong>AnyDesk:</strong> $anydesk</li>
                        </ul>
                        <p>Te pedimos que tomes las medidas necesarias para atender este ticket lo antes posible.</p>
                        <p>enlace a ticket: $url</p>
                    </td>
                </tr>
                <tr>
                    <td style='padding: 20px; text-align: center; background-color: #f38001;'>
                        <p style='color: #ffffff; margin: 0;'>Gracias por tu atención</p>
                    </td>
                </tr>
            </table>
        </body>
        </html>";
        $subject = "Nuevo ticket #$clienteName";
        $this->enviarMail($html, $mail, $subject, 'Nuevo ticket');
        $this->registroMail('Nuevo ticket', $mail, $titulo . $ticketId);
      }
      $this->response->data = 'Message has been sent ';
      $this->response->status = 200;
    } catch (Exception $e) {
      $this->response->data = "Message could not be sent. Mailer Error: {$mail->ErrorInfo} ";
      $this->response->status = 400;
    }
    return $this->response;
  }

  private function enviarMail($html, $to, $subject, $tipo)
  {
    $url = 'https://api.sendgrid.com/';
    $user = "apiKey";
    $pass = '';
    $headr = array();
    $headr[] = 'Authorization: Bearer ' . $pass;
    $params = array(
      'to' => $to,
      'subject' => $subject,
      'html' => $html,
      'text' => 'Soporte Worksi',
      'from' => 'soporte@wks.ar',
    );
    $request = $url . 'api/mail.send.json';

    $session = curl_init($request);
    if ($session) {
      curl_setopt($session, CURLOPT_FOLLOWLOCATION, true);
      curl_setopt($session, CURLOPT_POSTFIELDS, $params);

      // Tell curl not to return headers, but do return the response
      curl_setopt($session, CURLOPT_HEADER, false);

      curl_setopt($session, CURLOPT_RETURNTRANSFER, true);

      // add authorization header
      curl_setopt($session, CURLOPT_HTTPHEADER, $headr);

      // obtain response
      $response = curl_exec($session);
      curl_close($session);
      //
      return true;
    }
    return false;
  }

  private function registroMail($descripcion, $destino, $detalle)
  {
    $sql = "INSERT INTO mails(descripcion, destino, detalle) VALUES ('$descripcion', '$destino', '$detalle')";
    $this->modularModel->sqlVarios($sql);
  }
}