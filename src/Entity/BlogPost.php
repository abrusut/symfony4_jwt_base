<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiSubresource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;


/**
 * @ORM\Entity(repositoryClass="App\Repository\BlogPostRepository")
 * @ApiFilter(
 *     PropertyFilter::class,
 *     arguments={
 *       "parameterName": "properties",
 *       "overrideDefaultProperties": false,
 *       "whitelist": {"id","author","slug","title","content"}
 *     }
 * )
 * @ApiFilter(
 *     OrderFilter::class,
 *     properties={
 *              "id",
 *              "published",
 *              "title"
 *     },
 *     arguments={"orderParameterName"="_order"}
 * )
 * @ApiFilter(
 *     RangeFilter::class,
 *     properties={
 *              "id"
 *     }
 * )
 * @ApiFilter(
 *     DateFilter::class,
 *     properties={
 *              "published"
 *     }
 * )
 * @ApiFilter(
 *     SearchFilter::class,
 *     properties={
            "id": "exact",
 *          "title":"partial",
 *          "content":"partial",
 *          "author":"exact",
 *          "author.name":"partial"
 *
 *     }
 * )
 * @ApiResource(
 *     attributes={"order"={"published" : "DESC" },
 *                 "pagination_enabled"=true,
 *                 "pagination_client_enabled"=true,
 *                  "pagination_client_items_per_page"=true,
 *                  "maximum_items_per_page"=30,
 *                  "pagination_partial"= true
 *      },
 *     itemOperations={
 *              "get"={
*                   "access_control"="is_granted('IS_AUTHENTICATED_FULLY')",
 *                  "normalization_context"={
 *                          "groups"={"get-blog-post-with-author"}
 *                  }
 *               },
 *              "put"={
 *                   "access_control"="is_granted('ROLE_EDITOR') or (is_granted('ROLE_WRITER') and object.getAuthor() == user)"
 *              }
 *     },
 *      collectionOperations={
 *              "get"={
 *                  "access_control"="is_granted('IS_AUTHENTICATED_FULLY')",
 *               },
 *              "post" = {
 *                   "access_control"="is_granted('ROLE_WRITER')"
 *              }
 *      },
 *     denormalizationContext={
            "groups"={"post"}
 *     },
 *     subresourceOperations={
 *         "api_blog_posts_comments_get_subresource"={
 *             "normalization_context"={
 *                 "groups"={"get-comment-with-author"}
 *             }
 *         }
 *     }
 *   )
 */
class BlogPost implements AuthoredEntityInterface, PublishedDateEntityInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"get-blog-post-with-author"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(min="5")
     * @Groups({"post","get-blog-post-with-author"})
     */
    private $title;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"get-blog-post-with-author"})
     */
    private $published;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="posts")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"get-blog-post-with-author"})
     */
    private $author;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(min="20")
     * @Groups({"post","get-blog-post-with-author"})
     */
    private $content;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     * @Groups({"post","get-blog-post-with-author"})
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="blogPost")
     * @ApiSubresource()
     * @Groups({"get-blog-post-with-author"})
     */
    private $comments;
    
    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Image")
     * @ORM\JoinTable()
     * @Groups({"post","get-blog-post-with-author"})
     */
    private $images;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->images = new ArrayCollection();
    }
    
    public function __toString(): string
    {
        return $this->getTitle();
    }
    
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @param mixed $slug
     */
    public function setSlug($slug): void
    {
        $this->slug = $slug;
    }

    /**
     * @return mixed
     */
    public function getPublished():?\DateTimeInterface
    {
        return $this->published;
    }


    public function setPublished(\DateTimeInterface $published): PublishedDateEntityInterface
    {
        $this->published = $published;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content): void
    {
        $this->content = $content;
    }

    /**
     * @return User
     */
    public function getAuthor(): ?User
    {
        return $this->author;
    }

    /**
     * @param UserInterface $author
     * @return $this
     */
    public function setAuthor(UserInterface $author): AuthoredEntityInterface
    {
        $this->author = $author;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    /**
     * @param mixed $comments
     */
    public function setComments($comments): void
    {
        $this->comments = $comments;
    }
    
    public function getImages():Collection
    {
        return $this->images;
    }
    
    /**
     * @param Image $images
     */
    public function addImage(Image $images): void
    {
        $this->images->add($images);
    }
    
    /**
     * @param Image $images
     */
    public function removeImage(Image $images): void
    {
        $this->images->removeElement($images);
    }


}
