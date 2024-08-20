<?php

namespace Bolt\Lib\Validator;

use Bolt\Utils\{ClientException};

class Validator
{
    static  function validateSchema(array $schema, array $data): void
    {
        $parsedValidationSchema = [];

        foreach ($schema as $key => $values) {
            for ($i = 0; $i < count($values); $i++) {
                [$idKey, $idVal] = explode("_", $values[$i]->value);
                $parsedValidationSchema[$key][$idKey] = $idVal;
            }
        }

        foreach ($parsedValidationSchema as $key => $val) {
            $type = $val['type'];
            $required = $val['required'] ?? false;
            $maxLength = $val['maxLength'] ?? null;
            $minLength = $val['minLength'] ?? null;
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
                // Check Matched Type
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


            // Check Length
            if ($maxLength !== null) {
                // String
                if ($currentType === "string" && strlen($val) > $maxLength) {
                    throw new ClientException("data/$key/length/over");
                }
                // Array
                else if ($currentType === "array" && count($val) > $maxLength) {
                    throw new ClientException("data/$key/length/over");
                }
                // Number
                else if (($currentType === "integer" || $currentType === "double") && $val > $maxLength) {
                    throw new ClientException("data/$key/length/over");
                }
            }
            if ($minLength !== null) {
                // String
                if ($currentType === "string" && strlen($val) < $minLength) {
                    throw new ClientException("data/$key/length/less");
                }
                // Array
                else if ($currentType === "array" && count($val) < $minLength) {
                    throw new ClientException("data/$key/length/less");
                }
                // Number
                else if (($currentType === "integer" || $currentType === "double") && $val < $minLength) {
                    throw new ClientException("data/$key/length/less");
                }
            }
        }
    }
}
