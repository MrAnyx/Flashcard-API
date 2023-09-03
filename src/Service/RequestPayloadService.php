<?php

namespace App\Service;

use App\Exception\JsonException;
use Symfony\Component\HttpFoundation\Request;

class RequestPayloadService
{
    /**
     * @throws JsonException
     */
    public function getRequestPayload(Request $request): mixed
    {
        $body = json_decode($request->getContent(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new JsonException('The request contains an invalid body that can not be parsed. Please verify the json body of the request.');
        }

        return $body;
    }

    public function getQueryPayload(Request $request): array
    {
        return $request->query->all();
    }
}
