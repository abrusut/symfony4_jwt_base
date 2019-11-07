<?php


namespace App\EventSubscriber;


use ApiPlatform\Core\EventListener\EventPriorities;
use App\Email\Mailer;
use App\Entity\User;
use App\Security\TokenGenerator;
use Swift_Message;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Esta clase se encarga de hashear la password y de asignarle un string ConfirmationToken al usuario que se registra
 * El usuario luego debera recibir por email ese ConfirmationToken que lo redirige a {{url}}/api/users/confirm
 * con un Body como este
 * {
 *   "confirmationToken":"rmaaPQBD9pdEKHWDYduYkCdbgryjeB	"
 * }
 * Y entonces cuando esa ruta (definida en la clase Entity\UserConfirmation.php) se cargue, entra en accion el
 *  UserConfirmationSubscriber validando este confirmationToken contra la DB y habilitando el usuario si es correcto
 * Class UserRegisterSubscriber
 * @package App\EventSubscriber
 */
class UserRegisterSubscriber implements EventSubscriberInterface
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;
    /**
     * @var TokenGenerator
     */
    private $tokenGenerator;
    /**
     * @var Mailer
     */
    private $mailer;
    
    /**
     * PasswordHashSubscriber constructor.
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder,
            TokenGenerator $tokenGenerator,
            Mailer $mailer)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->tokenGenerator = $tokenGenerator;
        $this->mailer = $mailer;
    }

    public static function getSubscribedEvents()
    {
       return [
           KernelEvents::VIEW => ['userRegistered', EventPriorities::PRE_WRITE]
       ];
    }

    public function userRegistered(GetResponseForControllerResultEvent $event){
        $user = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if(!$user instanceof User || !in_array($method,[Request::METHOD_POST])){
            return;
        }

        // Es un usuario y vino por POST, se hace hash de la password
        $user->setPassword(
            $this->passwordEncoder->encodePassword($user, $user->getPassword())
        );

        // Se asigna un token de confirmacion para el alta de la cuenta
        $user->setConfirmationToken($this->tokenGenerator->getRandomSecureToken());

        // Envio de email para confirmacion de cuenta
        $this->mailer->sendConfirmationEmail($user);
       
    }


}
