<?php

declare(strict_types=1);

namespace App\Users\Application\Query;

use App\Shared\Application\Bus\Query\QueryResponseInterface;
use App\Users\Domain\Entity\ProfileProjection;

final class GetProfileQueryResponse implements QueryResponseInterface
{
    private readonly ProfileProjection $profile;

    public function __construct(ProfileProjection $profile)
    {
        $this->profile = $profile;
    }

    public function getProfile(): ProfileProjection
    {
        return $this->profile;
    }
}
