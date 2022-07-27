<?php
declare(strict_types=1);

namespace App\Projects\Infrastructure\Persistence\Doctrine\Proxy;

use App\Projects\Domain\Collection\ProjectTaskCollection;
use App\Projects\Domain\Entity\Project;
use App\Projects\Domain\ValueObject\ProjectDescription;
use App\Projects\Domain\ValueObject\ProjectInformation;
use App\Projects\Domain\ValueObject\ProjectName;
use App\Projects\Domain\ValueObject\ProjectTasks;
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
use DateTime as PhpDateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\PersistentCollection;

final class ProjectProxy implements DoctrineVersionedProxyInterface, DoctrineProxyInterface
{
    private string $id;
    private string $name;
    private string $description;
    private PhpDateTime $finishDate;
    private int $status;
    private string $ownerId;
    /**
     * @var Collection|PersistentCollection|ProjectParticipantProxy[]
     */
    private Collection $participants;
    /**
     * @var Collection|PersistentCollection|ProjectTaskProxy[]
     */
    private Collection $tasks;
    private int $version;
    private ?Project $entity = null;

    public function __construct(Project $entity)
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
        $this->name = $this->entity->getInformation()->name->value;
        $this->description = $this->entity->getInformation()->description->value;
        $this->finishDate = $this->entity->getInformation()->finishDate->getPhpDateTime();
        $this->status = $this->entity->getStatus()->getScalar();
        $this->ownerId = $this->entity->getOwner()->userId->value;
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

    public function createEntity(): Project
    {
        if ($this->entity === null) {
            $participants = new UserIdCollection(array_map(function (ProjectParticipantProxy $item){
                return $item->createEntity();
            }, $this->participants->toArray()));
            $tasks = new ProjectTaskCollection(array_map(function (ProjectTaskProxy $item){
                return $item->createEntity();
            }, $this->tasks->toArray()));

            $this->entity = new Project(
                new ProjectId($this->id),
                new ProjectInformation(
                    new ProjectName($this->name),
                    new ProjectDescription($this->description),
                    new DateTime($this->finishDate->format(DateTime::DEFAULT_FORMAT))
                ),
                ProjectStatus::createFromScalar($this->status),
                new Owner(
                    new UserId($this->ownerId)
                ),
                new Participants($participants),
                new ProjectTasks($tasks)
            );
        }

        return $this->entity;
    }
}
