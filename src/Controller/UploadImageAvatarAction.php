<?php


namespace App\Controller;


use ApiPlatform\Core\Validator\Exception\ValidationException;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\Image;
use App\Entity\ImageAvatar;
use App\Entity\User;
use App\Exception\CustomValidationException;
use App\Form\ImageAvatarType;
use App\Form\ImageType;
use App\Repository\UserRepository;
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
     * @var UserRepository
     */
    private $userRepository;
    
    
    public function __construct(FormFactoryInterface $formFactory, EntityManagerInterface $entityManager,
                ValidatorInterface $validator, UserRepository $userRepository)
    {
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->userRepository = $userRepository;
    }
    
    public function __invoke(Request $request, $tipoEntidad, $id)
    {
        // Create Image Instance
        $image = new ImageAvatar();
        
        // Create and Validate Form
        $form = $this->formFactory->create(ImageAvatarType::class, $image);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            switch ($tipoEntidad) {
                case 'usuarios':
                    /** @var UserInterface $user */
                    $user = $this->userRepository->find($id);
                    if(is_null($user) || !is_object($user) || !$user instanceof User) {
                        throw new CustomValidationException("No se pudo obtener el usuario");
                    }
                    $image->setTipoEntidad($tipoEntidad);
                    $user->setAvatar($image);
                    break;
                default:
                    throw new CustomValidationException("El tipo de entidad no es valida");
                    break;
            }
    
            // Persist Image Entity
            $this->entityManager->persist($image);
            $this->entityManager->flush();
    
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