<?php
declare(strict_types=1);

namespace App\Shared\Domain\Service;

use App\Shared\Domain\ValueObject\AuthUser;

interface AuthenticatorServiceInterface
{
    public function getAuthUser(): AuthUser;
}
