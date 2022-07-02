<?php
declare(strict_types=1);

namespace App\Projects\Domain\ValueObject;

use App\Projects\Domain\Collection\ProjectTaskCollection;
use App\Projects\Domain\Entity\Project;
use App\Projects\Domain\Entity\ProjectTask;
use App\Projects\Domain\Exception\UserHasProjectTaskException;
use App\Shared\Domain\ValueObject\UserId;
use Exception;

final class ProjectTasks
{
    private readonly ProjectTaskCollection $tasks;

    public function __construct(array $items = [])
    {
        $this->tasks = new ProjectTaskCollection($items);
    }

    /**
     * @param UserId $userId
     * @return bool
     * @throws Exception
     */
    public function ensureDoesUserHaveTask(UserId $userId): void
    {
        /** @var ProjectTask $task */
        foreach ($this->tasks as $task) {
            if ($task->getOwnerId()->isEqual($userId)) {
                throw new UserHasProjectTaskException();
            }
        }
    }

    public function limitDatesOfAllTasksByProjectFinishDate(Project $project): void
    {
        /** @var ProjectTask $task */
        foreach ($this->tasks as $task) {
            $task->limitDatesByProjectFinishDate($project);
        }
    }

    public function closeAllTasksIfActive(Project $project): void
    {
        /** @var ProjectTask $task */
        foreach ($this->tasks as $task) {
            $task->closeIfActive($project);
        }
    }
}