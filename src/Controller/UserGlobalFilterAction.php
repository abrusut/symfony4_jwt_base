<?php


namespace App\Controller;


use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserGlobalFilterAction
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var JWTTokenManagerInterface
     */
    private $JWTTokenManager;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var UserRepository
     */
    private $userRepository;
    
    public function __construct(UserRepository $userRepository,
                                LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->userRepository = $userRepository;
    }
    
    public function __invoke(Request $request)
    {
        $termino = $request->get('termino');
        
        $size =  $request->get('size');
        $size = isset($size) && $size ? $size : 20;
        
        $page = $request->get('page');
        $page = isset($page) && $page ? $page : 1;
    
        $order = $request->get('_order');
        $order = isset($order) && $order ? $order : array('id'=>'desc');
        
        $this->logger->info("Busqueda global de usuarios ".$termino);
        
        $users = $this->userRepository->findByUserNameOrNameOrEmail($termino,$page, $size, $order);
        $this->logger->info("Cantidad de Usuarios encontrados ".count($users));
        return $users;
    }
}