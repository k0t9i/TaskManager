<?php
declare(strict_types=1);

namespace App\Users\Domain\Entity;

use App\Shared\Domain\Aggregate\AggregateRoot;
use App\Shared\Domain\ValueObject\UserId;
use App\Users\Domain\ValueObject\UserEmail;
use App\Users\Domain\ValueObject\UserFirstname;
use App\Users\Domain\ValueObject\UserLastname;
use App\Users\Domain\ValueObject\UserPassword;

final class User extends AggregateRoot
{
    public function __construct(
        private UserId $id,
        private UserEmail $email,
        private UserFirstname $firstname,
        private UserLastname $lastname,
        private UserPassword $password
    ) {
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

    public function getPassword(): UserPassword
    {
        return $this->password;
    }
}