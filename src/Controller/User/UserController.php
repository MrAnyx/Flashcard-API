<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Controller\AbstractRestController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', 'api_', format: 'json')]
class UserController extends AbstractRestController
{
    #[Route('/users/me', name: 'get_me', methods: ['GET'])]
    public function getMe()
    {
        $user = $this->getUser();

        return $this->jsonStd($user, groups: ['read:user:user']);
    }

    #[Route('/users/me', name: 'delete_me', methods: ['DELETE'])]
    public function deleteMe(EntityManagerInterface $em)
    {
        $user = $this->getUser();

        // All flashcards, units, topics and reviews are deleted using the on cascade trigger
        $em->remove($user);
        $em->flush();

        return $this->jsonStd(null, status: Response::HTTP_NO_CONTENT);
    }
}
