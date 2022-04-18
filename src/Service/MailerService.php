<?php

namespace App\Service;

use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class MailerService {

    /**
     * @var MailerInterface
     */
    private MailerInterface $mailer;

    /**
     * @var Environment
     */
    private Environment $twig;

    /**
     * @param MailerInterface $mailer
     * @param $twig
     */
    public function __construct(MailerInterface $mailer,Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }


    /**
     * @param string $subject
     * @param string $from
     * @param string $to
     * @param string $template
     * @param array $parameters
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws TransportExceptionInterface
     */
    public function send(string $subject, string $from, string $to, string $template, array $parameters):void
    {
        $email = (new Email())
            ->from($from)
            ->to($to)
            ->subject($subject)
            ->text('coucou')
            ->html(
                $this->twig->render($template, $parameters),
                charset: 'utf-8'
            )
        ;

        $this->mailer->send($email);
    }

}
