<?php

namespace App\Controller;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Este controller sobrescribe el controller automatico del EasyAdmin,
 * para poder hacer encoder de la password al persistir un usuario
 * Class UserAdminController
 * @package App\Controller
 */
class UserAdminController extends EasyAdminController
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;
    
    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
    }
    
    /**
     * @param User $entity
     */
    protected function persistEntity($entity)
    {
    
        $this->encodePassword($entity);
    
        parent::persistEntity($entity);
    }
    
    /**
     * @param User $entity
     */
    protected function updateEntity($entity)
    {
        $this->encodePassword($entity);
    
        parent::updateEntity($entity);
    }
    
    /**
     * @param User $entity
     */
    protected function encodePassword($entity): void
    {
        $entity->setPassword($this->userPasswordEncoder->encodePassword($entity, $entity->getPassword()));
    }
    
    
}