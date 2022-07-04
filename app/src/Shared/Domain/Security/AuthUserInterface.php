<?php
declare(strict_types=1);

namespace App\Shared\Domain\Security;

use App\Shared\Domain\ValueObject\UserId;

interface AuthUserInterface
{
    public function getId(): UserId;
}
