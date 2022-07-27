<?php
declare(strict_types=1);

namespace App\Tasks\Infrastructure\Persistence\Doctrine\Proxy;

use App\Shared\Domain\ValueObject\DateTime;
use App\Shared\Domain\ValueObject\Tasks\TaskId;
use App\Shared\Domain\ValueObject\Users\UserId;
use App\Shared\Infrastructure\Persistence\Doctrine\PersistentCollectionLoaderInterface;
use App\Shared\Infrastructure\Persistence\Doctrine\Proxy\DoctrineProxyCollectionItemInterface;
use App\Shared\Infrastructure\Persistence\Doctrine\Proxy\DoctrineProxyInterface;
use App\Tasks\Domain\Collection\TaskLinkCollection;
use App\Tasks\Domain\Entity\Task;
use App\Tasks\Domain\ValueObject\TaskBrief;
use App\Tasks\Domain\ValueObject\TaskDescription;
use App\Tasks\Domain\ValueObject\TaskInformation;
use App\Tasks\Domain\ValueObject\TaskLink;
use App\Tasks\Domain\ValueObject\TaskName;
use App\Tasks\Domain\ValueObject\TaskStatus;
use DateTime as PhpDateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\PersistentCollection;

final class TaskProxy implements DoctrineProxyCollectionItemInterface, DoctrineProxyInterface
{
    private string $id;
    private string $name;
    private string $brief;
    private string $description;
    private PhpDateTime $startDate;
    private PhpDateTime $finishDate;
    private string $ownerId;
    private int $status;
    /**
     * @var Collection|PersistentCollection|TaskLink[]
     */
    private Collection $links;
    private TaskManagerProxy $manager;
    private ?Task $entity = null;

    public function __construct(TaskManagerProxy $owner, Task $entity)
    {
        $this->links = new ArrayCollection();
        $this->manager = $owner;
        $this->entity = $entity;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function refresh(PersistentCollectionLoaderInterface $loader): void
    {
        $this->id = $this->entity->getId()->value;
        $this->name = $this->entity->getInformation()->name->value;
        $this->brief = $this->entity->getInformation()->brief->value;
        $this->description = $this->entity->getInformation()->description->value;
        $this->startDate = PhpDateTime::createFromFormat(
            DateTime::DEFAULT_FORMAT,
            $this->entity->getInformation()->startDate->getValue()
        );
        $this->finishDate = PhpDateTime::createFromFormat(
            DateTime::DEFAULT_FORMAT,
            $this->entity->getInformation()->finishDate->getValue()
        );
        $this->ownerId = $this->entity->getOwnerId()->value;
        $this->status = $this->entity->getStatus()->getScalar();
        $loader->loadInto(
            $this->links,
            $this->entity->getLinks(),
            $this
        );
    }

    public function createEntity(): Task
    {
        if ($this->entity === null) {
            $links = new TaskLinkCollection(array_map(function (TaskLinkProxy $item){
                return $item->createEntity();
            }, $this->links->toArray()));

            $this->entity = new Task(
                new TaskId($this->id),
                new TaskInformation(
                    new TaskName($this->name),
                    new TaskBrief($this->brief),
                    new TaskDescription($this->description),
                    new DateTime($this->startDate->format(DateTime::DEFAULT_FORMAT)),
                    new DateTime($this->finishDate->format(DateTime::DEFAULT_FORMAT)),
                ),
                new UserId($this->ownerId),
                TaskStatus::createFromScalar($this->status),
                $links
            );
        }
        return $this->entity;
    }

    public function getKey(): string
    {
        return $this->id;
    }
}
