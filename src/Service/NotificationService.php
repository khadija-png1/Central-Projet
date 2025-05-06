<?php
namespace App\Service;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class NotificationService
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendNotification(string $subject, string $content, string $recipientEmail): void
    {
        $email = (new Email())
            ->from('kbouallaoui@gmail.com') // L'email de l'expéditeur
            ->to($recipientEmail)           // L'email du destinataire
            ->subject($subject)             // Le sujet de l'email
            ->text($content);               // Le contenu du message

        $this->mailer->send($email);
    }
}
