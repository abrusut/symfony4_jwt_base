<?php


namespace App\Email;


use App\Entity\User;
use Swift_Message;

class Mailer
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    /**
     * @var \Twig_Environment
     */
    private $twigEnvironment;
    
    public function __construct(\Swift_Mailer $mailer,
                                \Twig_Environment $twigEnvironment)
    {
        $this->mailer = $mailer;
        $this->twigEnvironment = $twigEnvironment;
    }
    
    public function sendConfirmationEmail(User $user){
        $body = $this->twigEnvironment->render(
            'email/confirmation.html.twig',
                    ['user' => $user]
        );
    
        $message = (new Swift_Message('Please confirm your account'))
            ->setFrom('api-platform@api.com')
            ->setTo($user->getEmail())
            ->setBody($body, 'text/html');
    
        $this->mailer->send($message);
    }
}