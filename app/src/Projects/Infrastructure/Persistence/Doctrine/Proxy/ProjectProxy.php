<?php
declare(strict_types=1);

namespace App\Projects\Infrastructure\Persistence\Doctrine\Proxy;

use App\Projects\Domain\Collection\ProjectTaskCollection;
use App\Projects\Domain\Entity\Project;
use App\Projects\Domain\Entity\ProjectTask;
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
use App\Shared\Infrastructure\Persistence\Doctrine\Proxy\DoctrineVersionedProxyInterface;
use App\Shared\Infrastructure\Persistence\Doctrine\Proxy\ProxyCollectionLoaderTrait;
use DateTime as PhpDateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

final class ProjectProxy implements DoctrineVersionedProxyInterface
{
    use ProxyCollectionLoaderTrait;

    private string $id;
    private string $name;
    private string $description;
    private PhpDateTime $finishDate;
    private int $status;
    private string $ownerId;
    /**
     * @var Collection|ProjectParticipantProxy[]
     */
    private Collection $participants;
    /**
     * @var Collection|ProjectTaskProxy[]
     */
    private Collection $tasks;
    private int $version;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
        $this->tasks = new ArrayCollection();
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function loadFromEntity(Project $entity): void
    {
        $this->id = $entity->getId()->value;
        $this->name = $entity->getInformation()->name->value;
        $this->description = $entity->getInformation()->description->value;
        $this->finishDate = PhpDateTime::createFromFormat(
            DateTime::DEFAULT_FORMAT,
            $entity->getInformation()->finishDate->getValue()
        );
        $this->status = $entity->getStatus()->getScalar();
        $this->ownerId = $entity->getOwner()->userId->value;
        $this->loadParticipants($entity);
        $this->loadTasks($entity);
    }

    public function createEntity(): Project
    {
        $participants = new UserIdCollection(array_map(function (ProjectParticipantProxy $item){
            return $item->createEntity();
        }, $this->participants->toArray()));
        $tasks = new ProjectTaskCollection(array_map(function (ProjectTaskProxy $item){
            return $item->createEntity();
        }, $this->tasks->toArray()));

        return new Project(
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

    private function loadParticipants(Project $entity): void
    {
        $this->loadCollection(
            $entity->getParticipants()->getCollection(),
            $this->participants,
            new ProjectParticipantProxy(),
            function (ProjectParticipantProxy $proxy, ProjectProxy $parent, UserId $entity) {
                $proxy->loadFromEntity($parent, $entity);
            }
        );
    }

    private function loadTasks(Project $entity): void
    {
        $this->loadCollection(
            $entity->getTasks()->getCollection(),
            $this->tasks,
            new ProjectTaskProxy(),
            function (ProjectTaskProxy $proxy, ProjectProxy $parent, ProjectTask $entity) {
                $proxy->loadFromEntity($parent, $entity);
            }
        );
    }
}
