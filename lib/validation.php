<?php

include_once "./utils/exception.php";

enum Types: string
{
    case email = "type_email";
    case array = "type_array";
    case file = "type_file";
    case float = "type_float";
    case integer = "type_integer";
    case string = "type_string";
}

enum Required: string
{
    case true = "required_true";
    case false = "required_false";
}

function validateSchema(array $schema, array $data): void
{
    foreach ($schema as $key => $val) {
        $type = $val['type'];
        $required = $val['required'] ?? false;
        $types = explode("|", $type);

        $isFile = (in_array("file", $types)) || (in_array("image", $types));

        // Required Checking
        if (
            $required && !(isset($data[$key]) || ($isFile && isset($_FILES[$key])))
        ) {
            throw new ClientException("data/$key/required");
        }

        if (!$required && !isset($data[$key])) {
            continue;
        }

        if ($isFile) {
            continue;
        }

        $val = $data[$key];

        $currentType = gettype($val);

        $isMatchedType = false;

        foreach ($types as $type) {
            $isMatchedType = [
                ($currentType == "array" && $type == "array" && is_array($val)) || // Array
                ($currentType == "string" && $type == "integer" && is_numeric($val)) || // Number
                ($currentType == "string" && $type == "double" && is_float($val)) || // Float
                ($currentType === $type) || // Another Types
                ($currentType === "string" && $type === "email" && filter_var($val, FILTER_VALIDATE_EMAIL)) // Email
            ];

            // Security Checking XSS|SQL Injection
            if (
                $currentType === "string" && (strpos($val, "'") || strpos($val, "<") || strpos($val, ">"))
            ) {
                str_replace($val, "<", "");
                str_replace($val, "'", "");
                str_replace($val, ">", "");
            }

            if ($isMatchedType) {
                break;
            }
        }

        if (!$isMatchedType) {
            throw new ClientException("data/$key/invalid");
        }
    }
}
