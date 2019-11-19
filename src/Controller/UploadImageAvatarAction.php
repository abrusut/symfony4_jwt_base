<?php


namespace App\Controller;


use ApiPlatform\Core\Validator\Exception\ValidationException;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\Image;
use App\Entity\ImageAvatar;
use App\Entity\User;
use App\Form\ImageAvatarType;
use App\Form\ImageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;


class UploadImageAvatarAction
{
    
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var ValidatorInterface
     */
    private $validator;
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;
    
    public function __construct(FormFactoryInterface $formFactory, EntityManagerInterface $entityManager,
                ValidatorInterface $validator, TokenStorageInterface $tokenStorage)
    {
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->tokenStorage = $tokenStorage;
    }
    
    public function __invoke(Request $request)
    {
        // Create Image Instance
        $image = new ImageAvatar();
    
        /** @var UserInterface $user */
        $user = $this->tokenStorage->getToken()->getUser();
        
        // Create and Validate Form
        $form = $this->formFactory->create(ImageAvatarType::class, $image);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            // Persist Image Entity
            $this->entityManager->persist($image);
            $this->entityManager->flush();
            if($user){
                $user->setAvatar($image);
            }
            $image->setFile(null);
            return $image;
        }
        
        
        // Uploading Done for us in background by VichUploader
        
        
        // Lanzamos excepcion si es que alguna validacion no se cumple
        throw new ValidationException(
            $this->validator->validate($image)
        );
    }
}