<?php

namespace App\Security\Http\Authentication;

use App\Entity\ImageAvatar;
use App\Entity\User;
use App\Exception\CustomValidationException;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationSuccessResponse;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface as ContractsEventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

/**
 * AuthenticationSuccessHandler.
 *
 * @author Andres Brusutti
 */
class AuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    protected $jwtManager;
    protected $dispatcher;
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;
    
    public function __construct(JWTTokenManagerInterface $jwtManager, EventDispatcherInterface $dispatcher,
                                TokenStorageInterface $tokenStorage)
    {
        $this->jwtManager = $jwtManager;
        $this->dispatcher = $dispatcher;
        $this->tokenStorage = $tokenStorage;
    }
    
    /**
     * {@inheritdoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        return $this->handleAuthenticationSuccess($token->getUser());
    }
    
    public function handleAuthenticationSuccess(UserInterface $user, $jwt = null)
    {
        
        if (null === $jwt) {
            $jwt = $this->jwtManager->create($user);
        }
    
        /** @var User $userLogged */
        $userLogged = $this->tokenStorage->getToken()->getUser();
        if(is_null($userLogged) || !is_object($userLogged) || !$userLogged instanceof User) {
            throw new CustomValidationException("No se pudo obtener el usuario");
        }
    
        $avatarUser = null;
        if(!is_null($userLogged->getAvatar()) && is_object($userLogged->getAvatar()))
            $avatarUser = $userLogged->getAvatar()->getUrl();
        
        $response = new JWTAuthenticationSuccessResponse($jwt);
        
        
        $payload = array(
            'token' => $jwt,
            'user' =>  array(
                'id' => $userLogged->getId(),
                'username'=>$userLogged->getUsername(),
                'fullName'=>$userLogged->getFullName(),
                'email' => $userLogged->getEmail(),
                'roles' => $userLogged->getRoles(),
                'avatar' => $avatarUser
            )
        );
        
        $event    = new AuthenticationSuccessEvent($payload, $user, $response);
        
        if ($this->dispatcher instanceof ContractsEventDispatcherInterface) {
            $this->dispatcher->dispatch($event, Events::AUTHENTICATION_SUCCESS);
        } else {
            $this->dispatcher->dispatch(Events::AUTHENTICATION_SUCCESS, $event);
        }
    
        
        $response->setData($event->getData());
        
        return $response;
    }
}
