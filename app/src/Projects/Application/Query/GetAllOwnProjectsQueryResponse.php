<?php
declare(strict_types=1);

namespace App\Projects\Application\Query;

use App\Projects\Domain\DTO\ProjectListResponseDTO;
use App\Shared\Domain\Bus\Query\QueryResponseInterface;

final class GetAllOwnProjectsQueryResponse implements QueryResponseInterface
{
    /**
     * @var ProjectListResponseDTO[]
     */
    private readonly array $projects;

    public function __construct(ProjectListResponseDTO... $projects)
    {
        $this->projects = $projects;
    }

    /**
     * @return ProjectListResponseDTO[]
     */
    public function getProjects(): array
    {
        return $this->projects;
    }
}
