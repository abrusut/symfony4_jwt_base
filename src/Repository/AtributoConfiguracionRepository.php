<?php


namespace App\Repository;


use ApiPlatform\Core\Bridge\Doctrine\Orm\Paginator;
use App\Entity\AtributoConfiguracion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Symfony\Bridge\Doctrine\RegistryInterface;

class AtributoConfiguracionRepository extends ServiceEntityRepository
{
    private $tokenStorage;
    /**
     * @var RegistryInterface
     */
    private $registry;
    
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, AtributoConfiguracion::class);
    }
    
    /**
     * @param $termino Palabra, id, Buscado
     * @param $page Pagina pedida
     * @param $size Cantidad de registros pedidos
     * @return AtributoConfiguracion[]
     */
    public function findByTermino($termino, $page = 1, $size = 20, $order)
    {
        $firstResult = ($page -1) * $size;
        
        $queryBuilder = $this->createQueryBuilder('a')
            ->andWhere('a.clave LIKE :termino')
            ->setParameter('termino', '%'.$termino.'%')
            ->orWhere('a.valor LIKE :termino')
            ->setParameter('termino', '%'.$termino.'%');
        
        if(!is_null($order) && count($order)>0){
            foreach ($order as  $clave => $valor)
            {
                $queryBuilder->orderBy('a.'.$clave , $valor);
            }
        }
        
        $criteria = Criteria::create()
            ->setFirstResult($firstResult)
            ->setMaxResults($size);
        $queryBuilder->addCriteria($criteria);
        
        $doctrinePaginator = new DoctrinePaginator($queryBuilder);
        $paginator = new Paginator($doctrinePaginator);
        
        return $paginator;
    }
    
    
    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    
}