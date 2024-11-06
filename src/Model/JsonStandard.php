<?php

declare(strict_types=1);

namespace App\Model;

use App\Enum\JsonStandardStatus;

readonly class JsonStandard
{
    public \DateTimeImmutable $timestamp;

    public JsonStandardStatus $status;

    public ?Pagination $pagination;

    public mixed $data;

    public function __construct(mixed $data, JsonStandardStatus $status = JsonStandardStatus::VALID)
    {
        $this->timestamp = new \DateTimeImmutable();
        $this->status = $status;

        if ($data instanceof Paginator) {
            $this->data = $data->data;
            $this->pagination = $data->pagination;
        } else {
            $this->data = $data;
            $this->pagination = null;
        }
    }
}
