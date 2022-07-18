<?php
declare(strict_types=1);

namespace App\Users\Infrastructure\Repository;

use App\Shared\Domain\ValueObject\Projects\ProjectId;
use App\Shared\Domain\ValueObject\Users\UserId;
use App\Users\Domain\DTO\ProfileResponseDTO;
use App\Users\Domain\DTO\UserListResponseDTO;
use App\Users\Domain\Repository\UserQueryRepositoryInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class SqlUserQueryRepository implements UserQueryRepositoryInterface
{
    private const CONNECTION = 'read';

    public function __construct(
        private readonly ManagerRegistry $managerRegistry
    ) {
    }

    /**
     * @throws Exception
     */
    public function findByProjectIdAndUserId(ProjectId $projectId, UserId $userId): ?UserListResponseDTO
    {
        $rawItem = $this->queryBuilder()
            ->select('*')
            ->from('user_projections')
            ->where('project_id = ?')
            ->andWhere('user_id = ?')
            ->setParameters([
                $projectId->value,
                $userId->value
            ])
            ->fetchAssociative();

        if ($rawItem === false) {
            return null;
        }

        return UserListResponseDTO::create($rawItem);
    }

    /**
     * @param ProjectId $projectId
     * @return UserListResponseDTO[]
     * @throws Exception
     */
    public function findAllByProjectId(ProjectId $projectId): array
    {
        $rawItems = $this->queryBuilder()
            ->select('*')
            ->from('user_projections')
            ->where('project_id = ?')
            ->setParameters([
                $projectId->value,
            ])
            ->fetchAllAssociative();

        $result = [];
        foreach ($rawItems as $rawItem) {
            $result[] = UserListResponseDTO::create($rawItem);
        }

        return $result;
    }

    /**
     * @param UserId $userId
     * @return ProfileResponseDTO|null
     * @throws Exception
     */
    public function findProfile(UserId $userId): ?ProfileResponseDTO
    {
        $rawItem = $this->queryBuilder()
            ->select('*')
            ->from('user_projections')
            ->where('user_id = ?')
            ->setParameters([
                $userId->value
            ])
            ->fetchAssociative();

        if ($rawItem === false) {
            return null;
        }

        return ProfileResponseDTO::create($rawItem);
    }

    private function queryBuilder(): QueryBuilder
    {
        /** @var Connection $connection */
        $connection = $this->managerRegistry->getConnection(self::CONNECTION);
        return $connection->createQueryBuilder();
    }
}