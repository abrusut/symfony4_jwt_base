<?php


namespace App\Controller;


use ApiPlatform\Core\Validator\Exception\ValidationException;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\Image;
use App\Form\ImageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;


class UploadImageAction
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
    
    public function __construct(FormFactoryInterface $formFactory, EntityManagerInterface $entityManager,
                ValidatorInterface $validator)
    {
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }
    
    public function __invoke(Request $request)
    {
        // Create Image Instance
        $image = new Image();
        
        // Create and Validate Form
        $form = $this->formFactory->create(ImageType::class, $image);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
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