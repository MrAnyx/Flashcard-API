<?php

declare(strict_types=1);

namespace App\Model;

use App\Enum\JsonStandardStatus;

readonly class JsonStandard
{
    public \DateTimeImmutable $timestamp;

    public JsonStandardStatus $status;

    public ?array $pagination;

    public mixed $data;

    public function __construct(mixed $data, array $context = [], JsonStandardStatus $status = JsonStandardStatus::VALID)
    {
        $this->timestamp = new \DateTimeImmutable();
        $this->status = $status;
        $this->pagination = null;

        if ($data instanceof Paginator) {
            $this->data = $data->getData();
            $this->pagination = [
                'total' => $data->getTotal(),
                'count' => $data->getCount(),
                'offset' => $data->getOffset(),
                'items_per_page' => $data->getItemsPerPage(),
                'total_pages' => $data->getTotalPages(),
                'current_page' => $data->getCurrentPage(),
                'has_next_page' => $data->hasNextPage(),
                'has_previous_page' => $data->hasPreviousPage(),
            ];
        } else {
            $this->data = $data;
        }
    }
}
