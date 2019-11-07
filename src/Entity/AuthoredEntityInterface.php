<?php


namespace App\Entity;


use App\EventSubscriber\AuthoredEntitySubscriber;
use Symfony\Component\Security\Core\User\UserInterface;

interface AuthoredEntityInterface
{
    public function setAuthor(UserInterface $user):AuthoredEntityInterface;
}