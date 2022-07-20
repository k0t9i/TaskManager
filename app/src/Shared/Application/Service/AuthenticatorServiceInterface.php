<?php
declare(strict_types=1);

namespace App\Shared\Application\Service;

interface AuthenticatorServiceInterface
{
    public function getAuthUser(): AuthUserInterface;
    public function getToken(string $id): string;
}
