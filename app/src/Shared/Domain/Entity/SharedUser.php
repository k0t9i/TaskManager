<?php
declare(strict_types=1);

namespace App\Shared\Domain\Entity;

use App\Shared\Domain\Aggregate\AggregateRoot;
use App\Shared\Domain\Event\SharedUserWasCreatedEvent;
use App\Shared\Domain\ValueObject\UserEmail;
use App\Shared\Domain\ValueObject\UserFirstname;
use App\Shared\Domain\ValueObject\UserId;
use App\Shared\Domain\ValueObject\UserLastname;

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
        $user = new SharedUser($id, $email, $firstname, $lastname);

        $user->registerEvent(new SharedUserWasCreatedEvent(
            $user->id->value,
            $user->email->value,
            $user->firstname->value,
            $user->lastname->value,
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
}