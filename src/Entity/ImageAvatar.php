<?php


namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\UploadImageAvatarAction;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity()
 * @ORM\Table(name="image_avatar")
 * @Vich\Uploadable()
 * @ApiResource(
 *     attributes={"order"={"id" : "DESC" }},
 *     collectionOperations={
 *           "get",
 *          "post"={
 *              "method"="POST",
 *              "path"="/images/{tipoEntidad}/{id}",
 *              "controller"=UploadImageAvatarAction::class,
 *              "defaults"={"_api_receive"=false}
 *          }
 *
 *     }
 * )
 */
class ImageAvatar
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;
    
    /**
     * "imagesAvatar" => es el nombre de la propiedad configurada en vich_uploader.yaml
     * "url" debe ser una propiedad de la misma clase
     * @Vich\UploadableField(mapping="imagesAvatar", fileNameProperty="url")
     * @Assert\NotNull()
     */
    private $file;
    
    /**
     * @ORM\Column(nullable=true)
     *  @Groups({"get-blog-post-with-author", "get-user-with-image"})
     */
    private $url;
    
    /**
     * @ORM\Column(nullable=true)
     *  @Groups({"get-blog-post-with-author", "get-user-with-image"})
     */
    private $tipoEntidad;
    
    public function __toString(): string
    {
        return $this->getId() . ':' . $this->getUrl();
    }
    
    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }
    
    /**
     * @return mixed
     */
    public function getFile()
    {
        return $this->file;
    }
    
    /**
     * @param mixed $file
     */
    public function setFile($file): void
    {
        $this->file = $file;
    }
    
    /**
     * @return mixed
     */
    public function getUrl()
    {
        return '/images/'.$this->tipoEntidad.'/'.$this->url;
    }
    
    /**
     * @param mixed $url
     */
    public function setUrl($url): void
    {
        $this->url = $url;
    }
    
    /**
     * @return mixed
     */
    public function getTipoEntidad()
    {
        return $this->tipoEntidad;
    }
    
    /**
     * @param mixed $tipoEntidad
     */
    public function setTipoEntidad($tipoEntidad): void
    {
        $this->tipoEntidad = $tipoEntidad;
    }
    
    
}