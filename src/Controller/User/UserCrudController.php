<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Controller\AbstractRestController;
use App\Entity\Setting;
use App\Entity\User;
use App\Modifier\Modifier;
use App\Modifier\Mutator\HashPasswordMutator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api', 'api_', format: 'json')]
class UserCrudController extends AbstractRestController
{
    #[Route('/users/me', name: 'get_me', methods: ['GET'])]
    public function getMe(
        #[CurrentUser] User $user,
    ) {
        return $this->json($user, context: ['groups' => ['read:user:user']]);
    }

    #[Route('/users/me', name: 'delete_me', methods: ['DELETE'])]
    public function deleteMe(EntityManagerInterface $em)
    {
        $user = $this->getUser();

        /*
        Delete all children using orphanRemove :
        - Flashcard
        - Unit
        - Topic
        - Review
        - Setting
        */
        $em->remove($user);
        $em->flush();

        return $this->json(null, status: Response::HTTP_NO_CONTENT);
    }

    #[Route('/users/me', name: 'update_me', methods: ['PATCH', 'PUT'])]
    public function updateMe(
        EntityManagerInterface $em,
        #[CurrentUser] User $user,
    ): JsonResponse {
        $updatedUser = $this->decodeBody(
            classname: User::class,
            fromObject: $user,
            deserializationGroups: ['write:user:user'],
            mutators: [
                new Modifier('rawPassword', HashPasswordMutator::class),
            ]
        );

        if ($updatedUser->getRawPassword() !== null) {
            $this->validateEntity($updatedUser, ['edit:user:password']);
        }

        $em->flush();

        return $this->json($updatedUser, context: ['groups' => ['read:user:user']]);
    }
}
