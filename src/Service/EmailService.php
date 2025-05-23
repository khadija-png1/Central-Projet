<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Psr\Log\LoggerInterface;

class EmailService
{
    private MailerInterface $mailer;
    private LoggerInterface $logger;

    public function __construct(MailerInterface $mailer, LoggerInterface $logger)
    {
        $this->mailer = $mailer;
        $this->logger = $logger;
    }

    /**
     * Envoie un e-mail.
     *
     * @param string $to Destinataire de l'e-mail
     * @param string $content Contenu HTML de l'e-mail
     * @param string $subject Sujet de l'e-mail
     */
    public function sendEmail(string $to = 'acharidhak@gmail.com', string $content = '<p>Test</p>', string $subject = 'Test'): void
    {
        try {
            $email = (new Email())
                ->from('kbouallaoui@gmail.com')
                ->to($to)
                ->subject($subject)
                ->html($content);

            $this->mailer->send($email);

            $this->logger->info("E-mail envoyé avec succès à {$to}.");
        } catch (\Exception $e) {
            $this->logger->error("Erreur lors de l'envoi de l'e-mail : " . $e->getMessage());
        }
    }
}
