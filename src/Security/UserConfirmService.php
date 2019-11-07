<?php


namespace App\Security;


use App\Exception\InvalidConfirmationTokenException;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserConfirmService
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var LoggerInterface
     */
    private $logger;
    
    public function __construct(UserRepository $userRepository, EntityManagerInterface $entityManager,
            LoggerInterface $logger)
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }
    
    public function confirmUser(string $confirmationToken){
        $this->logger->debug('Buscando usuario por confirmationToken');
        $user = $this->userRepository->findOneBy(
            [
                'confirmationToken' => $confirmationToken
            ]);
    
    
        if( !$user ){
            $this->logger->debug('No existe usuario por confirmationToken '.$confirmationToken);
            throw new InvalidConfirmationTokenException();
        }
    
        $user->setEnabled(true);
        $user->setConfirmationToken(null);
        $this->entityManager->flush();
    
        $this->logger->debug('Usuario habilitado por confirmationToken '.$confirmationToken);
    }
    
}