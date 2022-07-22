<?php
declare(strict_types=1);

namespace App\Users\Domain\Entity;

use App\Shared\Domain\Aggregate\AggregateRoot;
use App\Shared\Domain\Event\Users\UserProfileWasChangedEvent;
use App\Shared\Domain\Event\Users\UserWasCreatedEvent;
use App\Shared\Domain\ValueObject\Users\UserEmail;
use App\Shared\Domain\ValueObject\Users\UserId;
use App\Users\Domain\ValueObject\UserProfile;

final class User extends AggregateRoot
{
    public function __construct(
        private UserId $id,
        private UserEmail $email,
        private UserProfile $profile
    ) {
    }

    public static function create(
        UserId $id,
        UserEmail $email,
        UserProfile $profile
    ): self {
        $user = new self($id, $email, $profile);

        $user->registerEvent(new UserWasCreatedEvent(
            $user->id->value,
            $user->email->value,
            $user->profile->firstname->value,
            $user->profile->lastname->value,
            $user->profile->password->value,
        ));

        return $user;
    }

    public function changeProfile(UserProfile $profile): void
    {
        $this->profile = $profile;

        $this->registerEvent(new UserProfileWasChangedEvent(
            $this->id->value,
            $this->profile->firstname->value,
            $this->profile->lastname->value,
            $this->profile->password->value,
        ));
    }

    public function getId(): UserId
    {
        return $this->id;
    }

    public function getProfile(): UserProfile
    {
        return $this->profile;
    }
}