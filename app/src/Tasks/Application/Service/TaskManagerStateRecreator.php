<?php
declare(strict_types=1);

namespace App\Tasks\Application\Service;

use App\Shared\Domain\Bus\Event\DomainEvent;
use App\Shared\Domain\Event\ProjectInformationWasChangedEvent;
use App\Shared\Domain\Event\ProjectOwnerWasChangedEvent;
use App\Shared\Domain\Event\ProjectParticipantWasAddedEvent;
use App\Shared\Domain\Event\ProjectParticipantWasRemovedEvent;
use App\Shared\Domain\Event\ProjectStatusWasChangedEvent;
use App\Shared\Domain\Exception\LogicException;
use App\Shared\Domain\Factory\ProjectStatusFactory;
use App\Shared\Domain\ValueObject\ClosedProjectStatus;
use App\Shared\Domain\ValueObject\DateTime;
use App\Shared\Domain\ValueObject\UserId;
use App\Tasks\Domain\DTO\TaskManagerMergeDTO;
use App\Tasks\Domain\Entity\Task;
use App\Tasks\Domain\Entity\TaskManager;
use App\Tasks\Domain\Factory\TaskManagerMerger;

final class TaskManagerStateRecreator
{
    public function __construct(
        private readonly TaskManagerMerger $managerMerger
    ) {
    }

    public function fromEvent(TaskManager $source, DomainEvent $event): TaskManager
    {
        if ($event instanceof ProjectStatusWasChangedEvent) {
            return $this->changeStatus($source, $event);
        }
        if ($event instanceof ProjectOwnerWasChangedEvent) {
            return $this->changeOwner($source, $event);
        }
        if ($event instanceof ProjectParticipantWasRemovedEvent) {
            return $this->removeParticipant($source, $event);
        }
        if ($event instanceof ProjectParticipantWasAddedEvent) {
            return $this->addParticipant($source, $event);
        }
        if ($event instanceof ProjectInformationWasChangedEvent) {
            return $this->changeFinishDate($source, $event);
        }

        throw new LogicException(sprintf('Invalid domain event "%s"', get_class($event)));
    }

    private function changeStatus(TaskManager $source, ProjectStatusWasChangedEvent $event): TaskManager
    {
        if (ProjectStatusFactory::objectFromScalar((int) $event->status) instanceof ClosedProjectStatus) {
            /** @var Task $task */
            foreach ($source->getTasks()->getInnerItems() as $task) {
                $task->closeIfCan();
            }
        }
        return $this->managerMerger->merge($source, new TaskManagerMergeDTO(
            status: (int) $event->status,
            tasks: $source->getTasks()->getInnerItems()
        ));
    }

    private function changeOwner(TaskManager $source, ProjectOwnerWasChangedEvent $event): TaskManager
    {
        return $this->managerMerger->merge($source, new TaskManagerMergeDTO(
            ownerId: $event->ownerId
        ));
    }

    private function removeParticipant(TaskManager $source, ProjectParticipantWasRemovedEvent $event): TaskManager
    {
        $participants = $source->getParticipants()->remove(new UserId($event->participantId));

        return $this->managerMerger->merge($source, new TaskManagerMergeDTO(
            participantIds: $participants->getInnerItems()
        ));
    }

    private function addParticipant(TaskManager $source, ProjectParticipantWasAddedEvent $event): TaskManager
    {
        $participants = $source->getParticipants()->add(new UserId($event->participantId));

        return $this->managerMerger->merge($source, new TaskManagerMergeDTO(
            participantIds: $participants->getInnerItems()
        ));
    }

    private function changeFinishDate(TaskManager $source, ProjectInformationWasChangedEvent $event): TaskManager
    {
        $date = new DateTime($event->finishDate);
        /** @var Task $task */
        foreach ($source->getTasks()->getInnerItems() as $task) {
            $task->limitDatesIfNeed($date);
        }
        return $this->managerMerger->merge($source, new TaskManagerMergeDTO(
            finishDate: $event->finishDate,
            tasks: $source->getTasks()->getInnerItems()
        ));
    }
}
