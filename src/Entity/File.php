<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\UploadFileAction;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;


/**
 * @ORM\Entity()
 * @Vich\Uploadable()
 * @ApiResource(
 *     attributes={"order"={"id" : "DESC" }},
 *     collectionOperations={
 *            "get",
 *          "post"={
 *              "access_control"="is_granted('IS_AUTHENTICATED_FULLY')",
 *              "method"="POST",
 *              "path"="/files",
 *              "controller"=UploadFileAction::class,
 *              "defaults"={"_api_receive"=false}
 *          }
 *     }
 * )
 */
class File
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;
    
    /**
     * "files" => es el nombre de la propiedad configurada en vich_uploader.yaml
     * "url" debe ser una propiedad de la misma clase
     * @Vich\UploadableField(mapping="files", fileNameProperty="url")
     * @Assert\NotNull()
     */
    private $file;
    
    /**
     * @ORM\Column(nullable=true)
     * @Groups({"get"})
     */
    private $url;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="files")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"get"})
     */
    private $author;
  
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
        return '/files/' . $this->url;
    }
    
    /**
     * @param mixed $url
     */
    public function setUrl($url): void
    {
        $this->url = $url;
    }
    
    
    public function setAuthor(UserInterface $user): File
    {
        $this->author =$user ;
        return $this;
    }
    
    /**
     * @return mixed
     */
    public function getAuthor()
    {
        return $this->author;
    }
    
    
}