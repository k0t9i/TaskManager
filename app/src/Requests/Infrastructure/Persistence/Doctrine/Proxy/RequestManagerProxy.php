<?php
declare(strict_types=1);

namespace App\Requests\Infrastructure\Persistence\Doctrine\Proxy;

use App\Requests\Domain\Collection\RequestCollection;
use App\Requests\Domain\Entity\Request;
use App\Requests\Domain\Entity\RequestManager;
use App\Requests\Domain\ValueObject\RequestManagerId;
use App\Requests\Domain\ValueObject\Requests;
use App\Shared\Domain\Collection\UserIdCollection;
use App\Shared\Domain\ValueObject\Owner;
use App\Shared\Domain\ValueObject\Participants;
use App\Shared\Domain\ValueObject\Projects\ProjectId;
use App\Shared\Domain\ValueObject\Projects\ProjectStatus;
use App\Shared\Domain\ValueObject\Users\UserId;
use App\Shared\Infrastructure\Persistence\Doctrine\Proxy\DoctrineVersionedProxyInterface;
use App\Shared\Infrastructure\Persistence\Doctrine\Proxy\ProxyCollectionLoaderTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

final class RequestManagerProxy implements DoctrineVersionedProxyInterface
{
    use ProxyCollectionLoaderTrait;

    private string $id;
    private string $projectId;
    private int $status;
    private string $ownerId;
    /**
     * @var Collection|RequestManagerParticipantProxy[]
     */
    private Collection $participants;
    /**
     * @var Collection|RequestProxy[]
     */
    private Collection $requests;
    private int $version;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
        $this->requests = new ArrayCollection();
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function loadFromEntity(RequestManager $entity): void
    {
        $this->id = $entity->getId()->value;
        $this->projectId = $entity->getProjectId()->value;
        $this->status = $entity->getStatus()->getScalar();
        $this->ownerId = $entity->getOwner()->userId->value;
        $this->loadParticipants($entity);
        $this->loadRequests($entity);
    }

    public function createEntity(): RequestManager
    {
        $participants = new UserIdCollection(array_map(function (RequestManagerParticipantProxy $item){
            return $item->createEntity();
        }, $this->participants->toArray()));
        $requests = new RequestCollection(array_map(function (RequestProxy $item){
            return $item->createEntity();
        }, $this->requests->toArray()));

        return new RequestManager(
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

    private function loadParticipants(RequestManager $entity): void
    {
        $this->loadCollection(
            $entity->getParticipants()->getCollection(),
            $this->participants,
            new RequestManagerParticipantProxy(),
            function (RequestManagerParticipantProxy $proxy, RequestManagerProxy $parent, UserId $entity) {
                $proxy->loadFromEntity($parent, $entity);
            }
        );
    }

    private function loadRequests(RequestManager $entity): void
    {
        $this->loadCollection(
            $entity->getRequests()->getCollection(),
            $this->requests,
            new RequestProxy(),
            function (RequestProxy $proxy, RequestManagerProxy $parent, Request $entity) {
                $proxy->loadFromEntity($parent, $entity);
            }
        );
    }
}
