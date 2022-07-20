<?php
declare(strict_types=1);

namespace App\Projects\Application\Query;

use App\Projects\Domain\Entity\ProjectProjection;
use App\Shared\Application\Bus\Query\QueryResponseInterface;

final class GetProjectQueryResponse implements QueryResponseInterface
{
    private readonly ProjectProjection $project;

    public function __construct(ProjectProjection $project)
    {
        $this->project = $project;
    }

    public function getProject(): ProjectProjection
    {
        return $this->project;
    }
}
