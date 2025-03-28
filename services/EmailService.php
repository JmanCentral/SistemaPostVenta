
<?php

require_once 'PHPMailer/PHPMailer.php';
require_once 'PHPMailer/SMTP.php';
require_once 'PHPMailer/Exception.php';
require_once 'config/configproperties.php';


class EmailService {

    private $mailer;
    private $config;
    
    public function __construct(array $smtpConfig) {
        $this->config =  $smtpConfig;
        $this->mailer = new PHPMailer\PHPMailer\PHPMailer(true);
        $this->configureMailer();
    }

    private function configureMailer() {
        $this->mailer->isSMTP();
        $this->mailer->Host = $this->config['host'];
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = $this->config['username'];
        $this->mailer->Password = $this->config['password'];
        $this->mailer->SMTPSecure = $this->config['secure'];
        $this->mailer->Port = $this->config['port'];
        $this->mailer->setFrom(
            $this->config['from_email'], 
            $this->config['from_name']
        );
    }
    
    public function sendEmailWithAttachment($to, $subject, $body, $attachmentPath) {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($to);
            $this->mailer->isHTML(true);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $body;
            $this->mailer->AltBody = strip_tags($body);
            $this->mailer->addAttachment($attachmentPath);
            
            return $this->mailer->send();
        } catch (Exception $e) {
            throw new Exception("Error al enviar correo: {$this->mailer->ErrorInfo}");
        }
    }
}