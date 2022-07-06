<?php
declare(strict_types=1);

namespace App\Users\Domain\Entity;

use App\Shared\Domain\Aggregate\AggregateRoot;
use App\Shared\Domain\Event\UserWasCreatedEvent;
use App\Shared\Domain\ValueObject\UserEmail;
use App\Shared\Domain\ValueObject\UserFirstname;
use App\Shared\Domain\ValueObject\UserId;
use App\Shared\Domain\ValueObject\UserLastname;
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

    public static function create(
        UserId $id,
        UserEmail $email,
        UserFirstname $firstname,
        UserLastname $lastname,
        UserPassword $password
    ): self {
        $user = new self($id, $email, $firstname, $lastname, $password);

        $user->registerEvent(new UserWasCreatedEvent(
            $user->id->value,
            $user->email->value,
            $user->firstname->value,
            $user->lastname->value,
            $user->password->value,
        ));

        return $user;
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