<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Repository\UserRepository;

class AuthenticationSuccessListener
{
    private $repository;
    // Construct the repo to get it into the method, neither the method wouldn't take it
    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param AuthenticationSuccessEvent $event
     */
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $data = $event->getData();
        $user = $event->getUser();
        // email of user who connect
        $email = $user->getUserIdentifier();
        // all data of this user
        $actualUser = $this->repository->findOneBy(['email' => $email]);
        if (!$user instanceof UserInterface) {
            return;
        }
        $data['user'] = [
            'id' => $actualUser->getId(),
            'pseudo' => $actualUser->getPseudo(),
        ];
        $event->setData($data);
    }
}