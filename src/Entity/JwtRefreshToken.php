<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken;
use App\Repository\JwtRefreshTokenRepository;

/**
 * @ORM\Table("ravemap__refresh_token")
 * @ORM\Entity(repositoryClass=JwtRefreshTokenRepository::class)
 */
class JwtRefreshToken extends RefreshToken
{
}
