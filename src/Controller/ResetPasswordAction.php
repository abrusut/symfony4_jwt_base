<?php

namespace App\Controller;

use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\User;
use App\Exception\CustomValidationException;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


/**
 * Este controller tiene su ruta definida en la clase App\Entity\User.php
 * Ya que ahi define API Platform sus endpoints y para el caso del Reset Password,
 * definimos un endpoint propio.
 * Ver en User.php -> itemOperations=> "put-reset-password"
 *
 */
class ResetPasswordAction
{
    
    /**
     * @var ValidatorInterface
     */
    private $validator;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var JWTTokenManagerInterface
     */
    private $JWTTokenManager;
    
    public function __construct(ValidatorInterface $validator,
                        UserPasswordEncoderInterface $userPasswordEncoder,
                        EntityManagerInterface $entityManager,
                        JWTTokenManagerInterface $JWTTokenManager)
    {
        $this->validator = $validator;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->entityManager = $entityManager;
        $this->JWTTokenManager = $JWTTokenManager;
    }
    
    public function __invoke(User $user)
    {
        //var_dump($user);
        //die;
        
        /**
         * El validador ejecuta todos los constraint (Assert) definidos en la entidad, en este caso App\Entity\User.php
         * Si no cumple alguna propiedad tira una excepcion
        **/
        $this->validator->validate($user,  ['groups'=>'put-reset-password']);
    
        /**
         * Parche el validate de arriba al hacer reset password no compara que User->newPassword() === User->getNewRetypedPassword()
         */
        if( $user->getNewPassword() !== $user->getNewRetypedPassword() )
        {
            
            throw new CustomValidationException("Las passwords deben ser iguales", 400);
        }
        
        
        $user->setPassword(
            $this->userPasswordEncoder->encodePassword(
                $user, $user->getNewPassword()
            )
        );
        
        $user->setPasswordChangeDate(time());
    
        /** Force flush, para que ejecute el update contra la base, sino devuelve error de que el oldPassword esta mal, por que
            en realidad ya lo cambio.
         * No se ejecuta un persist(), por que al hacer el setPassword ya se encarga Doctrine de hacer el update
         **/
        $this->entityManager->flush();
        
        /**
         * Generamos un nuevo token ya que cambio la informacion del usuario logueado
         */
        $token = $this->JWTTokenManager->create($user);
        
        return new JsonResponse(["token" => $token]);
    }
}
