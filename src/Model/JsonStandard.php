<?php

declare(strict_types=1);

namespace App\Model;

use App\Enum\JsonStandardStatus;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\SerializedName;

readonly class JsonStandard
{
    public const DEFAULT_GROUP = '@pagination';

    #[Groups([self::DEFAULT_GROUP])]
    #[SerializedName('@timestamp')]
    public \DateTimeImmutable $timestamp;

    #[Groups([self::DEFAULT_GROUP])]
    #[SerializedName('@status')]
    public JsonStandardStatus $status;

    #[Groups([self::DEFAULT_GROUP])]
    #[SerializedName('@pagination')]
    public ?array $pagination;

    #[Groups([self::DEFAULT_GROUP])]
    public mixed $data;

    public function __construct(mixed $data, JsonStandardStatus $status = JsonStandardStatus::VALID)
    {
        $this->timestamp = new \DateTimeImmutable();
        $this->status = $status;
        $this->pagination = null;

        if ($data instanceof Paginator) {
            $this->data = $data->getData();

            // TODO Refacto
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
