<?php
declare(strict_types=1);

namespace App\Requests\Infrastructure\Persistence\Doctrine\Proxy;

use App\Requests\Domain\Collection\RequestCollection;
use App\Requests\Domain\Entity\RequestManager;
use App\Requests\Domain\ValueObject\RequestManagerId;
use App\Requests\Domain\ValueObject\Requests;
use App\Shared\Domain\Collection\UserIdCollection;
use App\Shared\Domain\ValueObject\Owner;
use App\Shared\Domain\ValueObject\Participants;
use App\Shared\Domain\ValueObject\Projects\ProjectId;
use App\Shared\Domain\ValueObject\Projects\ProjectStatus;
use App\Shared\Domain\ValueObject\Users\UserId;
use App\Shared\Infrastructure\Persistence\Doctrine\PersistentCollectionLoaderInterface;
use App\Shared\Infrastructure\Persistence\Doctrine\Proxy\DoctrineProxyInterface;
use App\Shared\Infrastructure\Persistence\Doctrine\Proxy\DoctrineVersionedProxyInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\PersistentCollection;

final class RequestManagerProxy implements DoctrineVersionedProxyInterface, DoctrineProxyInterface
{
    private string $id;
    private string $projectId;
    private int $status;
    private string $ownerId;
    /**
     * @var Collection|PersistentCollection|RequestManagerParticipantProxy[]
     */
    public Collection $participants;
    /**
     * @var Collection|PersistentCollection|RequestProxy[]
     */
    private Collection $requests;
    private int $version;
    public ?RequestManager $entity = null;

    public function __construct(RequestManager $entity)
    {
        $this->participants = new ArrayCollection();
        $this->requests = new ArrayCollection();
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
        $loader->loadInto(
            $this->participants,
            $this->entity->getParticipants()->getCollection(),
            $this
        );
        $loader->loadInto(
            $this->requests,
            $this->entity->getRequests()->getCollection(),
            $this
        );
    }

    public function createEntity(): RequestManager
    {
        if ($this->entity === null) {
            $participants = new UserIdCollection(array_map(function (RequestManagerParticipantProxy $item){
                return $item->createEntity();
            }, $this->participants->toArray()));
            $requests = new RequestCollection(array_map(function (RequestProxy $item){
                return $item->createEntity();
            }, $this->requests->toArray()));
            $this->entity = new RequestManager(
                new RequestManagerId($this->id),
                new ProjectId($this->projectId),
                ProjectStatus::createFromScalar($this->status),
                new Owner(
                    new UserId($this->ownerId)
                ),
                new Participants($participants),
                new Requests($requests)
            );
        }

        return $this->entity;
    }
}
