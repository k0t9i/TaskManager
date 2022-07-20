<?php
declare(strict_types=1);

namespace App\Projects\Application\Query;

use App\Projects\Domain\DTO\ProjectResponseDTO;
use App\Shared\Application\Bus\Query\QueryResponseInterface;

final class GetProjectQueryResponse implements QueryResponseInterface
{
    private readonly ProjectResponseDTO $project;

    public function __construct(ProjectResponseDTO $project)
    {
        $this->project = $project;
    }

    public function getProject(): ProjectResponseDTO
    {
        return $this->project;
    }
}
