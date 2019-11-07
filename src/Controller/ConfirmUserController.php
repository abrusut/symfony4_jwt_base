<?php


namespace App\Controller;


use App\Security\UserConfirmService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * La confirmacion que se puede hacer a traves de API Platform definiendo la ruta en la Entity UserConfirmation.php,
 * tambien la implementamos en un controller comun y corriente, utilizando el mismo servicio UserConfirmService
 * @Route("/")
 * Class ConfirmUserController
 * @package App\Controller
 */
class ConfirmUserController extends AbstractController
{
    
    /**
     * @Route("/", name="confirm_user_index")
     * @return JsonResponse
     */
    public function index(){
        return $this->render('base.html.twig');
    }
    /**
     * @Route("/confirm-user/{token}", name="confirm_user_token")
     * @param string $token
     */
    public function confirmUser(string $token, UserConfirmService $userConfirmService){
        $userConfirmService->confirmUser($token);
        return $this->redirectToRoute('confirm_user_index');
    }
}