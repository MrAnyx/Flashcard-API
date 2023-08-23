<?php

namespace App\Controller;

use App\Exception\ApiException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AbstractRestController extends AbstractController
{
    public function denyAccessIfDisconnected()
    {
        $user = $this->getUser();

        if ($user === null) {
            throw new ApiException('You must be authenticated to access this resource', Response::HTTP_UNAUTHORIZED);
        }
    }
}
