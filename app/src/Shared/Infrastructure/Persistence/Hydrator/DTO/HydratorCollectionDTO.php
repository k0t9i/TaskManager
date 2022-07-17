<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Hydrator\DTO;

final class HydratorCollectionDTO
{
    /**
     * @var HydratorEntityDTO[]
     */
    private readonly array $items;

    /**
     * @param HydratorEntityDTO[] $items
     */
    public function __construct(
        array $items
    ) {
        $indexed = [];
        foreach ($items as $item) {
            $indexed[$item->table][] = $item;
        }
        $this->items = $indexed;
    }

    /**
     * @param string $table
     * @return HydratorEntityDTO[]
     */
    public function getByTable(string $table): array
    {
        return $this->items[$table] ?? [];
    }
}
