<?php
declare(strict_types=1);

namespace App\Users\Application\Query;

use App\Shared\Application\Bus\Query\QueryResponseInterface;
use App\Users\Domain\DTO\ProfileResponseDTO;

final class GetProfileQueryResponse implements QueryResponseInterface
{
    private readonly ProfileResponseDTO $profile;

    public function __construct(ProfileResponseDTO $profile)
    {
        $this->profile = $profile;
    }

    public function getProfile(): ProfileResponseDTO
    {
        return $this->profile;
    }
}
