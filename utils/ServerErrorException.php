<?php

namespace Bolt\Utils;

class ServerErrorException extends \Exception
{
    public function __construct(int $errorCode, string $errorName, string $message)
    {
        if ($message === $errorName) {
            parent::__construct($errorName, $errorCode);
        } else {
            parent::__construct("$errorName: $message", $errorCode);
        }

        header($_SERVER['SERVER_PROTOCOL'] . " $errorCode $errorName");
    }

    // 4xx Client Error Responses
    public static function BadRequest(string $message = "Bad Request"): ServerErrorException
    {
        return new ServerErrorException(400, "Bad Request", $message);
    }

    public static function Unauthorized(string $message = "Unauthorized"): ServerErrorException
    {
        return new ServerErrorException(401, "Unauthorized", $message);
    }

    public static function PaymentRequired(string $message = "Payment Required"): ServerErrorException
    {
        return new ServerErrorException(402, "Payment Required", $message);
    }

    public static function Forbidden(string $message = "Forbidden"): ServerErrorException
    {
        return new ServerErrorException(403, "Forbidden", $message);
    }

    public static function NotFound(string $message = "Not Found"): ServerErrorException
    {
        return new ServerErrorException(404, "Not Found", $message);
    }

    public static function MethodNotAllowed(string $message = "Method Not Allowed"): ServerErrorException
    {
        return new ServerErrorException(405, "Method Not Allowed", $message);
    }

    public static function NotAcceptable(string $message = "Not Acceptable"): ServerErrorException
    {
        return new ServerErrorException(406, "Not Acceptable", $message);
    }

    public static function ProxyAuthenticationRequired(string $message = "Proxy Authentication Required"): ServerErrorException
    {
        return new ServerErrorException(407, "Proxy Authentication Required", $message);
    }

    public static function RequestTimeout(string $message = "Request Timeout"): ServerErrorException
    {
        return new ServerErrorException(408, "Request Timeout", $message);
    }

    public static function Conflict(string $message = "Conflict"): ServerErrorException
    {
        return new ServerErrorException(409, "Conflict", $message);
    }

    public static function Gone(string $message = "Gone"): ServerErrorException
    {
        return new ServerErrorException(410, "Gone", $message);
    }

    public static function LengthRequired(string $message = "Length Required"): ServerErrorException
    {
        return new ServerErrorException(411, "Length Required", $message);
    }

    public static function PreconditionFailed(string $message = "Precondition Failed"): ServerErrorException
    {
        return new ServerErrorException(412, "Precondition Failed", $message);
    }

    public static function PayloadTooLarge(string $message = "Payload Too Large"): ServerErrorException
    {
        return new ServerErrorException(413, "Payload Too Large", $message);
    }

    public static function URITooLong(string $message = "URI Too Long"): ServerErrorException
    {
        return new ServerErrorException(414, "URI Too Long", $message);
    }

    public static function UnsupportedMediaType(string $message = "Unsupported Media Type"): ServerErrorException
    {
        return new ServerErrorException(415, "Unsupported Media Type", $message);
    }

    public static function RangeNotSatisfiable(string $message = "Range Not Satisfiable"): ServerErrorException
    {
        return new ServerErrorException(416, "Range Not Satisfiable", $message);
    }

    public static function ExpectationFailed(string $message = "Expectation Failed"): ServerErrorException
    {
        return new ServerErrorException(417, "Expectation Failed", $message);
    }

    public static function ImATeapot(string $message = "I'm a Teapot"): ServerErrorException
    {
        return new ServerErrorException(418, "I'm a Teapot", $message);
    }

    public static function MisdirectedRequest(string $message = "Misdirected Request"): ServerErrorException
    {
        return new ServerErrorException(421, "Misdirected Request", $message);
    }

    public static function UnprocessableEntity(string $message = "Unprocessable Entity"): ServerErrorException
    {
        return new ServerErrorException(422, "Unprocessable Entity", $message);
    }

    public static function Locked(string $message = "Locked"): ServerErrorException
    {
        return new ServerErrorException(423, "Locked", $message);
    }

    public static function FailedDependency(string $message = "Failed Dependency"): ServerErrorException
    {
        return new ServerErrorException(424, "Failed Dependency", $message);
    }

    public static function TooEarly(string $message = "Too Early"): ServerErrorException
    {
        return new ServerErrorException(425, "Too Early", $message);
    }

    public static function UpgradeRequired(string $message = "Upgrade Required"): ServerErrorException
    {
        return new ServerErrorException(426, "Upgrade Required", $message);
    }

    public static function PreconditionRequired(string $message = "Precondition Required"): ServerErrorException
    {
        return new ServerErrorException(428, "Precondition Required", $message);
    }

    public static function TooManyRequests(string $message = "Too Many Requests"): ServerErrorException
    {
        return new ServerErrorException(429, "Too Many Requests", $message);
    }

    public static function RequestHeaderFieldsTooLarge(string $message = "Request Header Fields Too Large"): ServerErrorException
    {
        return new ServerErrorException(431, "Request Header Fields Too Large", $message);
    }

    public static function UnavailableForLegalReasons(string $message = "Unavailable For Legal Reasons"): ServerErrorException
    {
        return new ServerErrorException(451, "Unavailable For Legal Reasons", $message);
    }

    // 5xx Server Error Responses
    public static function InternalServerError(string $message = "Internal Server Error"): ServerErrorException
    {
        return new ServerErrorException(500, "Internal Server Error", $message);
    }

    public static function NotImplemented(string $message = "Not Implemented"): ServerErrorException
    {
        return new ServerErrorException(501, "Not Implemented", $message);
    }

    public static function BadGateway(string $message = "Bad Gateway"): ServerErrorException
    {
        return new ServerErrorException(502, "Bad Gateway", $message);
    }

    public static function ServiceUnavailable(string $message = "Service Unavailable"): ServerErrorException
    {
        return new ServerErrorException(503, "Service Unavailable", $message);
    }

    public static function GatewayTimeout(string $message = "Gateway Timeout"): ServerErrorException
    {
        return new ServerErrorException(504, "Gateway Timeout", $message);
    }

    public static function HTTPVersionNotSupported(string $message = "HTTP Version Not Supported"): ServerErrorException
    {
        return new ServerErrorException(505, "HTTP Version Not Supported", $message);
    }

    public static function VariantAlsoNegotiates(string $message = "Variant Also Negotiates"): ServerErrorException
    {
        return new ServerErrorException(506, "Variant Also Negotiates", $message);
    }

    public static function InsufficientStorage(string $message = "Insufficient Storage"): ServerErrorException
    {
        return new ServerErrorException(507, "Insufficient Storage", $message);
    }

    public static function LoopDetected(string $message = "Loop Detected"): ServerErrorException
    {
        return new ServerErrorException(508, "Loop Detected", $message);
    }

    public static function NotExtended(string $message = "Not Extended"): ServerErrorException
    {
        return new ServerErrorException(510, "Not Extended", $message);
    }

    public static function NetworkAuthenticationRequired(string $message = "Network Authentication Required"): ServerErrorException
    {
        return new ServerErrorException(511, "Network Authentication Required", $message);
    }
}
