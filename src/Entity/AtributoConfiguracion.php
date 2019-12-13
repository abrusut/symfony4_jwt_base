<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use App\Controller\AtributoConfiguracionGlobalFilterAction;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * @ApiFilter(
 *     OrderFilter::class,
 *     properties={
 *              "id",
 *              "clave",
 *              "valor"
 *     },
 *     arguments={"orderParameterName"="_order"}
 * )
 * @ApiFilter(
 *     RangeFilter::class,
 *     properties={
 *              "id"
 *     }
 * )
 * @ApiFilter(BooleanFilter::class, properties={"enabled"})
 * @ApiFilter(
 *     SearchFilter::class,
 *     properties={
 *           "id": "exact",
 *          "clave":"partial",
 *          "valor":"partial"
 *     }
 * )
 * @ApiResource(
 *     attributes={"order"={"id" : "DESC" },
 *                 "pagination_enabled"=true,
 *                 "pagination_client_enabled"=true,
 *                  "pagination_client_items_per_page"=true,
 *                  "maximum_items_per_page"=30
 *      },
 *      itemOperations={
 *              "get"={
 *                      "access_control"="is_granted('IS_AUTHENTICATED_FULLY')",
 *                       "normalization_context"={
 *                            "groups" = { "get" }
 *                      }
 *               },
 *              "put"={
 *                       "access_control"="is_granted('IS_AUTHENTICATED_FULLY')",
 *                      "denormalization_context"={
 *                            "groups" = { "put" }
 *                      },
 *                   "normalization_context"={
 *                            "groups" = { "get" }
 *                      }
 *              }
 *     },
 *      collectionOperations={
 *
 *              "get-global-search"={
 *                       "access_control"="is_granted('IS_AUTHENTICATED_FULLY')",
 *                      "method"="GET",
 *                      "path"="/atributo-configuracions/globalFilter",
 *                      "controller"=AtributoConfiguracionGlobalFilterAction::class,
 *                      "defaults"={"_api_receive"=false}
 *               },
 *              "get"={
 *                      "access_control"="is_granted('ROLE_SUPER_ADMIN')",
 *                       "normalization_context"={
 *                            "groups" = { "get" }
 *                      }
 *               },
 *              "post" = {
 *                       "access_control"="is_granted('IS_AUTHENTICATED_ANONYMOUSLY')",
 *                       "denormalization_context"={
 *                            "groups" = { "post" }
 *                      },
 *                      "normalization_context"={
 *                            "groups" = { "get" }
 *                      }
 *
 *
 *                  }
 *      }
 *
 * )
 *
 * @ORM\Entity(repositoryClass="App\Repository\AtributoConfiguracionRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(fields={"clave"})
 */
class AtributoConfiguracion
{
    /**
     * @Groups({"get"})
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

       
    /**
     * @var string
     * @Groups({"get", "post","put"})
     * @ORM\Column(name="clave", type="string", nullable=false, length=50)
     * @Assert\NotNull()
     * @Assert\NotBlank(groups={"post"})
     * @Assert\Length(min=6, max=50, groups={"post"})
     */
    private $clave;
    
    /**
     * @var string
     * @Groups({"get", "post","put"})
     * @ORM\Column(name="valor", type="string", nullable=false, length=255)
     * @Assert\NotNull()
     * @Assert\NotBlank()
     * @Assert\Length(min=1, max=255, groups={"post"})
     */
    private $valor;

     /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     * @Groups({"get"})
     */
    private $createdAt;


     /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     * @Groups({"get"})
     */
    private $updatedAt;

     /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="fecha_baja", type="datetime", nullable=true)
     * @Groups({"get", "post", "put"})
     */
    private $fechaBaja;


    /** 
     * @ORM\PreUpdate 
    */
    public function setUpdatedAtValue()
    {
        $this->updatedAt = new \DateTime();
    }

    /**
    * @ORM\PrePersist
    */
    public function setCreatedAtValue()
    {
        $this->createdAt = new \DateTime();
    }


    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return AtributoConfiguracion
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Numerador
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set fechaBaja
     *
     * @param \DateTime $fechaBaja
     * @return Numerador
     */
    public function setFechaBaja($fechaBaja)
    {
        $this->fechaBaja = $fechaBaja;

        return $this;
    }

    /**
     * Get fechaBaja
     *
     * @return \DateTime 
     */
    public function getFechaBaja()
    {
        return $this->fechaBaja;
    }

    /**
     * Set clave
     *
     * @param string $clave
     * @return AtributoConfiguracion
     */
    public function setClave($clave)
    {
        $this->clave = $clave;

        return $this;
    }

    /**
     * Get clave
     *
     * @return string 
     */
    public function getClave()
    {
        return $this->clave;
    }

    /**
     * Set valor
     *
     * @param string $valor
     * @return AtributoConfiguracion
     */
    public function setValor($valor)
    {
        $this->valor = $valor;

        return $this;
    }

    /**
     * Get valor
     *
     * @return string 
     */
    public function getValor()
    {
        return $this->valor;
    }
}
