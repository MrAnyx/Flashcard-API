<?php

namespace App\Exception;

enum ExceptionCode: int
{
    case GENERIC_ERROR = 0;
    case TODO = 1;

    // Bad request - 400
    case INVALID_REQUEST_BODY = 400_0;
    case INVALID_PAGINATION = 400_1;
    case UNUSABLE_RESOURCE = 400_2;
    case VALIDATION_FAILURE = 400_3;

    // Unauthorized - 401
    case INVALID_CREDENTIALS = 401_0;
    case AUTHENTICATION_FAILURE = 401_1;
    case UNAUTHENTICATED_USER = 401_2;

    // Forbidden - 403
    case UNAUTHORIZED = 403_0;

    // Not found - 404
    case RESOURCE_NOT_FOUND = 404_0;

}
