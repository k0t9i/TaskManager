<?php
declare(strict_types=1);

namespace App\ProjectRequests\Domain\Repository;

use App\ProjectRequests\Domain\Entity\Request;
use App\ProjectRequests\Domain\ValueObject\RequestId;

interface RequestRepositoryInterface
{
    public function findById(RequestId $id): ?Request;
    public function getById(RequestId $id): Request;
    public function create(Request $project): void;
    public function update(Request $project): void;
    public function delete(Request $project): void;
}