<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken;

#[ORM\Entity]
#[ORM\Table('jwt_refresh_token')]
class JWTRefreshToken extends RefreshToken
{
}
