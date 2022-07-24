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
use App\Shared\Infrastructure\Service\DoctrineVersionedProxyInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

final class RequestManagerProxy implements DoctrineVersionedProxyInterface
{
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
        $this->participants = $this->loadParticipants($entity);
        $this->requests = $this->loadRequests($entity);
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

    private function loadParticipants(RequestManager $entity): Collection
    {
        $participants = new ArrayCollection();

        /** @var UserId $child */
        foreach ($entity->getParticipants()->getCollection() as $child) {
            $participant = $this->participants->filter(function (RequestManagerParticipantProxy $item) use ($child) {
                return $child->value === $item->getUserId();
            })->first();
            if ($participant === false) {
                $participant = new RequestManagerParticipantProxy();
            }
            $participant->loadFromEntity($this, $child);
            $participants->add($participant);
        }

        return $participants;
    }

    private function loadRequests(RequestManager $entity): Collection
    {
        $requests = new ArrayCollection();

        /** @var Request $child */
        foreach ($entity->getRequests()->getCollection() as $child) {
            $request = $this->requests->filter(function (RequestProxy $item) use ($child) {
                return $child->getId()->value === $item->getId();
            })->first();
            if ($request === false) {
                $request = new RequestProxy();
            }
            $request->loadFromEntity($this, $child);
            $requests->add($request);
        }

       return $requests;
    }
}
