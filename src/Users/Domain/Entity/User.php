<?php
declare(strict_types=1);

namespace App\Users\Domain\Entity;

use App\Users\Domain\ValueObject\UserEmail;
use App\Users\Domain\ValueObject\UserFirstname;
use App\Users\Domain\ValueObject\UserId;
use App\Users\Domain\ValueObject\UserLastname;

class User
{
    public function __construct(
        private UserId $id,
        private UserEmail $email,
        private UserFirstname $firstname,
        private UserLastname $lastname
    ) {
    }

    /**
     * @return UserId
     */
    public function getId(): UserId
    {
        return $this->id;
    }

    /**
     * @return UserEmail
     */
    public function getEmail(): UserEmail
    {
        return $this->email;
    }

    /**
     * @param UserEmail $email
     */
    public function setEmail(UserEmail $email): void
    {
        $this->email = $email;
    }

    /**
     * @return UserFirstname
     */
    public function getFirstname(): UserFirstname
    {
        return $this->firstname;
    }

    /**
     * @param UserFirstname $firstname
     */
    public function setFirstname(UserFirstname $firstname): void
    {
        $this->firstname = $firstname;
    }

    /**
     * @return UserLastname
     */
    public function getLastname(): UserLastname
    {
        return $this->lastname;
    }

    /**
     * @param UserLastname $lastname
     */
    public function setLastname(UserLastname $lastname): void
    {
        $this->lastname = $lastname;
    }
}