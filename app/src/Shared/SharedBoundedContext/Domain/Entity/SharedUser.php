<?php
declare(strict_types=1);

namespace App\Shared\SharedBoundedContext\Domain\Entity;

use App\Shared\Domain\Aggregate\AggregateRoot;
use App\Shared\Domain\ValueObject\Users\UserEmail;
use App\Shared\Domain\ValueObject\Users\UserFirstname;
use App\Shared\Domain\ValueObject\Users\UserId;
use App\Shared\Domain\ValueObject\Users\UserLastname;

final class SharedUser extends AggregateRoot
{
    public function __construct(
        private UserId $id,
        private UserEmail $email,
        private UserFirstname $firstname,
        private UserLastname $lastname
    ) {
    }

    public static function create(
        UserId $id,
        UserEmail $email,
        UserFirstname $firstname,
        UserLastname $lastname
    ): self {
        return new SharedUser($id, $email, $firstname, $lastname);
    }

    public function changeProfile(
        UserFirstname $firstname,
        UserLastname $lastname
    ): void {
        $this->firstname = $firstname;
        $this->lastname = $lastname;
    }

    public function getId(): UserId
    {
        return $this->id;
    }

    public function getEmail(): UserEmail
    {
        return $this->email;
    }

    public function getFirstname(): UserFirstname
    {
        return $this->firstname;
    }

    public function getLastname(): UserLastname
    {
        return $this->lastname;
    }
}