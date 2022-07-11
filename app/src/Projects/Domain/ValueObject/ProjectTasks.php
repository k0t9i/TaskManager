<?php
declare(strict_types=1);

namespace App\Projects\Domain\ValueObject;

use App\Projects\Domain\Collection\ProjectTaskCollection;
use App\Projects\Domain\Entity\ProjectTask;
use App\Projects\Domain\Exception\UserHasProjectTaskException;
use App\Shared\Domain\Collection\Hashable;
use App\Shared\Domain\ValueObject\Tasks\TaskId;
use App\Shared\Domain\ValueObject\Users\UserId;
use Exception;

final class ProjectTasks
{
    private ProjectTaskCollection $tasks;

    public function __construct(?ProjectTaskCollection $items = null)
    {
        if ($items === null) {
            $this->tasks = new ProjectTaskCollection();
        } else {
            $this->tasks = $items;
        }
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
                throw new UserHasProjectTaskException($userId->value);
            }
        }
    }

    public function add(ProjectTask $task): self
    {
        $result = new self();
        $result->tasks = $this->tasks->add($task);
        return $result;
    }

    /**
     * @param TaskId $taskId
     * @return ProjectTask|null|Hashable
     */
    public function get(TaskId $taskId): ?ProjectTask
    {
        return $this->tasks->get($taskId->getHash());
    }

    public function getInnerItems(): ProjectTaskCollection
    {
        return $this->tasks;
    }
}
