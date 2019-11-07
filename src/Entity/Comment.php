<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;


// get-with-author se usa para el eviar el loop infinito, si colocamos get la relacion en ambas direcciones
// de un comentario con su usuario y el usuario con sus comentarios genera loop
/**
 * @ApiResource(
 *     attributes={"order"={"published" : "DESC" },
 *                 "pagination_enabled"=true,
 *                 "pagination_client_enabled"=true,
 *                  "pagination_client_items_per_page"=true,
 *                  "maximum_items_per_page"=30,
 *                  "pagination_partial"= false
 *      },
 *     itemOperations={
 *              "get"={
 *
 *                },
 *              "put"={
 *                   "access_control"="is_granted('ROLE_EDITOR') or (is_granted('ROLE_COMMENTATOR') and object.getAuthor() == user)"
 *              }
 *     },
 *      collectionOperations={
 *              "get",
 *              "post" = {
 *                    "access_control"="is_granted('ROLE_COMMENTATOR')",
 *                    "normalization_context"={
 *                        "groups"={"get-comment-with-author"}
 *                      }
 *              },
 *      },
 *     subresourceOperations={
 *         "api_blog_posts_comments_get_subresource"={
 *             "normalization_context"={
 *                 "groups"={"get-comment-with-author"}
 *             }
 *         }
 *     },
 *     denormalizationContext={
 *        "groups"={"post"}
 *     }
 *   )
 * @ORM\Entity(repositoryClass="App\Repository\CommentRepository")
 */
class Comment implements AuthoredEntityInterface, PublishedDateEntityInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"get-comment-with-author"})
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     * @Groups({"post","get-comment-with-author"})
     * @Assert\NotBlank()
     * @Assert\Length(min="5", max="3000")
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"get-comment-with-author"})
     */
    private $published;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"get-comment-with-author"})
     */
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\BlogPost", inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"post"})
     */
    private $blogPost;
    
    public function __toString(): string
    {
        return substr($this->getContent(),0,20).'...';
    }

    public function getBlogPost(): ?BlogPost
    {
        return $this->blogPost;
    }

    public function setBlogPost($blogPost): self
    {
        $this->blogPost = $blogPost;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getPublished(): ?\DateTimeInterface
    {
        return $this->published;
    }

    public function setPublished(\DateTimeInterface $published): PublishedDateEntityInterface
    {
        $this->published = $published;

        return $this;
    }

    /**
     * @return User
     */
    public function getAuthor(): User
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
}
