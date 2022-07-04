<?php
declare(strict_types=1);

namespace App\Projects\Application\Query;

use App\Shared\Domain\Bus\Query\QueryResponseInterface;

final class GetAllOwnProjectsQueryResponse implements QueryResponseInterface
{
    /**
     * @var ProjectResponse[]
     */
    private readonly array $projects;

    public function __construct(ProjectResponse... $projects)
    {
        $this->projects = $projects;
    }

    /**
     * @return ProjectResponse[]
     */
    public function getProjects(): array
    {
        return $this->projects;
    }
}
