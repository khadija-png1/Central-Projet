<?php

namespace App\Service;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
 use Psr\Log\LoggerInterface;

class EmailService
{
    public function __construct(private MailerInterface $mailer
    , private LoggerInterface $logger)
    {
        // Ce constructeur peut être utilisé pour injecter d'autres services
    }




    public function sendEmail(
            
        string $to='acharidhak@gmail.com',
        string $content='<p>Test</p>',
        string $subject='  Test',
        
    ):void
    {
    // Ajoute un log ici
   
    try {
        $email = (new Email())
            ->from('kbouallaoui@gmail.com')
            ->to($to)
            ->subject($subject)
            ->html($content);

        $this->mailer->send($email);
        $this->logger->info("E-mail envoyé avec succès à $to.");
    } catch (\Exception $e) {
        $this->logger->error("Erreur lors de l'envoi de l'e-mail : " . $e->getMessage());
    }
}
}