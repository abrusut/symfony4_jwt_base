<?php


namespace App\Security;


use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\ExpiredTokenException;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authentication\Token\PreAuthenticationJWTUserToken;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Guard\JWTTokenAuthenticator;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Extendemos de JWTTokenAuthenticator para sobrecribir el getUser y poder validar el token,
 * luego de un cambio de password
 * Class TokenAuthenticator
 * @package App\Security
 */
class TokenAuthenticator extends JWTTokenAuthenticator
{
    /**
     * @param PreAuthenticationJWTUserToken $preAuthToken
     * @param UserProviderInterface $userProvider
     * @return \Symfony\Component\Security\Core\User\UserInterface|void|null
     */
    public function getUser($preAuthToken, UserProviderInterface $userProvider)
    {
        /** @var User $user */
        $user = parent::getUser($preAuthToken, $userProvider);
    
    
        /**
         * $preAuthToken->getPayload()['iat'], tiene el dato del timestamp del momento en que se genero el token (login).
         * Lo que hacemos es compararlo con el timestamp del $user->getPasswordChangeDate() si existe, para ver si hubo
         * un cambio de clave, marcar ese token como Expirado y obligar al login.
         * De lo contrario el usuario podria quedar operando con 2 token distintos, el entregado al momento de cambiar la clave,
         * y el token anterior cuando se logueo.
         */
        if($user->getPasswordChangeDate() &&
            $preAuthToken->getPayload()['iat'] < $user->getPasswordChangeDate()){
            throw new ExpiredTokenException();
        }
        
        return $user;
    }
    
}