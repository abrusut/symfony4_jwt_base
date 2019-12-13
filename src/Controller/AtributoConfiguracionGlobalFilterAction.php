<?php


namespace App\Controller;


use App\Repository\AtributoConfiguracionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;

class AtributoConfiguracionGlobalFilterAction
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
     * @var AtributoConfiguracionRepository
     */
    private $atributoConfiguracionRepository;
    
    
    public function __construct(AtributoConfiguracionRepository $atributoConfiguracionRepository,
                                LoggerInterface $logger)
    {
        $this->logger = $logger;
    
        $this->atributoConfiguracionRepository = $atributoConfiguracionRepository;
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
        
        $this->logger->info("Busqueda global de Atributo Configuracion ".$termino);
        
        $configuraciones = $this->atributoConfiguracionRepository->findByTermino($termino,$page, $size, $order);
        $this->logger->info("Cantidad de Configuraciones encontradas ".count($configuraciones));
        return $configuraciones;
    }
}