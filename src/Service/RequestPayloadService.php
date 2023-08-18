<?php

namespace App\Service;

// use App\Exception\ExceptionCode;
use App\Exception\ExceptionStatus;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class RequestPayloadService
{
    public function __construct(
        private SerializerInterface $serializer,
        private ValidationService $validation
    ) {
    }

    /**
     * @template T
     *
     * @param class-string<T> $type
     * @return T
     */
    public function getRequestPayload(
        Request $request,
        string $type,
        // ExceptionCode $code,
        ExceptionStatus $status = ExceptionStatus::BAD_REQUEST,
        string $format = 'json'
    ) {
        $content = $request->getContent();
        $userDTO = $this->serializer->deserialize($content, $type, $format);
        $this->validation->validateOrThrow($userDTO, $status);

        return $userDTO;
    }
}
