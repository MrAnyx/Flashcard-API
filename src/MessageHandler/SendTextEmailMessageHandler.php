<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\SendTextEmailMessage;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Email;

#[AsMessageHandler]
final class SendTextEmailMessageHandler
{
    public function __construct(private MailerInterface $mailerInterface)
    {
    }

    public function __invoke(SendTextEmailMessage $message): void
    {
        $email = (new Email())
            ->from('noreply@example.com')
            ->to($message->email)
            ->priority($message->priority)
            ->subject($message->subject)
            ->text($message->message);

        $this->mailerInterface->send($email);
    }
}
