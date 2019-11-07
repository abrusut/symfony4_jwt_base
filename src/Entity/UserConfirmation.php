<?php


namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     collectionOperations={
            "post" = {
 *              "path"="/users/confirm"
 *          }
 *     },
 *     itemOperations={}
 * )
 * Class UserConfirmation
 * @package App\Entity
 */
class UserConfirmation
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=30, max=30)
     */
    public $confirmationToken;
    
}