<?php
declare(strict_types=1);

namespace App\Tasks\Infrastructure\Persistence\Doctrine\Proxy;

use App\Shared\Domain\Collection\UserIdCollection;
use App\Shared\Domain\ValueObject\DateTime;
use App\Shared\Domain\ValueObject\Owner;
use App\Shared\Domain\ValueObject\Participants;
use App\Shared\Domain\ValueObject\Projects\ProjectId;
use App\Shared\Domain\ValueObject\Projects\ProjectStatus;
use App\Shared\Domain\ValueObject\Users\UserId;
use App\Shared\Infrastructure\Persistence\Doctrine\PersistentCollectionLoaderInterface;
use App\Shared\Infrastructure\Persistence\Doctrine\Proxy\DoctrineProxyInterface;
use App\Shared\Infrastructure\Persistence\Doctrine\Proxy\DoctrineVersionedProxyInterface;
use App\Tasks\Domain\Collection\TaskCollection;
use App\Tasks\Domain\Entity\TaskManager;
use App\Tasks\Domain\ValueObject\TaskManagerId;
use App\Tasks\Domain\ValueObject\Tasks;
use DateTime as PhpDateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\PersistentCollection;

final class TaskManagerProxy implements DoctrineVersionedProxyInterface, DoctrineProxyInterface
{
    private string $id;
    private string $projectId;
    private int $status;
    private string $ownerId;
    private PhpDateTime $finishDate;
    /**
     * @var Collection|PersistentCollection|TaskManagerParticipantProxy[]
     */
    private Collection $participants;
    /**
     * @var Collection|PersistentCollection|TaskProxy[]
     */
    private Collection $tasks;
    private int $version;
    private ?TaskManager $entity = null;

    public function __construct(TaskManager $entity)
    {
        $this->participants = new ArrayCollection();
        $this->tasks = new ArrayCollection();
        $this->entity = $entity;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function refresh(PersistentCollectionLoaderInterface $loader): void
    {
        $this->id = $this->entity->getId()->value;
        $this->projectId = $this->entity->getProjectId()->value;
        $this->status = $this->entity->getStatus()->getScalar();
        $this->ownerId = $this->entity->getOwner()->userId->value;
        $this->finishDate = PhpDateTime::createFromFormat(
            DateTime::DEFAULT_FORMAT,
            $this->entity->getFinishDate()->getValue()
        );
        $loader->loadInto(
            $this->participants,
            $this->entity->getParticipants()->getCollection(),
            $this
        );
        $loader->loadInto(
            $this->tasks,
            $this->entity->getTasks()->getCollection(),
            $this
        );
    }

    public function createEntity(): TaskManager
    {
        if ($this->entity === null) {
            $participants = new UserIdCollection(array_map(function (TaskManagerParticipantProxy $item){
                return $item->createEntity();
            }, $this->participants->toArray()));
            $tasks = new TaskCollection(array_map(function (TaskProxy $item){
                return $item->createEntity();
            }, $this->tasks->toArray()));

            $this->entity = new TaskManager(
                new TaskManagerId($this->id),
                new ProjectId($this->projectId),
                ProjectStatus::createFromScalar($this->status),
                new Owner(
                    new UserId($this->ownerId)
                ),
                new DateTime($this->finishDate->format(DateTime::DEFAULT_FORMAT)),
                new Participants($participants),
                new Tasks($tasks)
            );
        }

        return $this->entity;
    }
}
