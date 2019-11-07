<?php


namespace App\EventSubscriber;


use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\UserConfirmation;
use App\Security\UserConfirmService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Esta clase UserConfirmationSubscriber valida un confirmationToken contra la DB y habilita el usuario enable=true
 * si es correcto.
 * Este subscriber se ejecuta invocando la ruta(definida en la clase Entity\UserConfirmation.php)
 * {{url}}/api/users/confirm
 * con un body:
 * {
 * "confirmationToken":"rmaaPQBD9pdEKHWDYduYkCdbgryjeB	"
 * }
 *
 * Class UserConfirmationSubscriber
 * @package App\EventSubscriber
 */
class UserConfirmationSubscriber implements EventSubscriberInterface
{
    /**
     * @var UserConfirmService
     */
    private $userConfirmService;
    
    public function __construct(UserConfirmService $userConfirmService)
    {
        $this->userConfirmService = $userConfirmService;
    }
    
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['confirmUser', EventPriorities::POST_VALIDATE]
        ];
    }
    
    public function confirmUser(GetResponseForControllerResultEvent $event)
    {
        $request = $event->getRequest();
        
        // el valor de esta ruta se saca haciendo "php bin/console debug:router" en la consola
        if( 'api_user_confirmations_post_collection' !== $request->get('_route') ){
            return;
        }
        
        /** @var UserConfirmation $confirmationToken */
        $confirmationToken = $event->getControllerResult();
        
        // Confirmo el usuario y lo habilito si existe
        $this->userConfirmService->confirmUser($confirmationToken->confirmationToken );
        
        $event->setResponse(new JsonResponse(null,Response::HTTP_OK));
    }
    
}