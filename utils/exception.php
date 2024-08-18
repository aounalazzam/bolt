<?php

class ClientException extends Exception
{
    public function __construct($message = "Bad Request")
    {
        parent::__construct($message, 400);
        header($_SERVER['SERVER_PROTOCOL'] . " 400 Bad Request");
    }
}

class UnauthorizedException extends Exception
{
    public function __construct($message = "Unauthorized")
    {
        parent::__construct($message, 401);
        header($_SERVER['SERVER_PROTOCOL'] . " 401 Unauthorized");
    }
}

class ForbiddenException extends Exception
{
    public function __construct($message = "Forbidden")
    {
        parent::__construct($message, 403);
        header($_SERVER['SERVER_PROTOCOL'] . " 403 Forbidden");
    }
}

class NotFoundException extends Exception
{
    public function __construct($message = "Not Found")
    {
        parent::__construct($message, 404);
        header($_SERVER['SERVER_PROTOCOL'] . " 404 Not Found");
    }
}

class ConflictException extends Exception
{
    public function __construct($message = "Conflict")
    {
        parent::__construct($message, 409);
        header($_SERVER['SERVER_PROTOCOL'] . " 409 Conflict");
    }
}

class InternalServerErrorException extends Exception
{
    public function __construct($message = "Internal Server Error")
    {
        parent::__construct($message, 500);
        header($_SERVER['SERVER_PROTOCOL'] . " 500 Internal Server Error");
    }
}
