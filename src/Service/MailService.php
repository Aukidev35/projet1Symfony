<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class MailService
{
/**
 * 
 *
 * @var MailerInterface
 */
    private MailerInterface $mailer;


    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEmail(
        string $from,
        string $subject,
        string $htmlTemplate,
        array $context,
        string $to = 'admin@monprojet.fr'
    ): void
    {
        $email = (new TemplatedEmail())
        ->from($from)
        ->to($to)
        // ->subject($subject)
        ->htmlTemplate($htmlTemplate)
        
        //pass variables (names=>value) to the template

        ->context($context)
        ;
        $this->mailer->send($email);
    }
}