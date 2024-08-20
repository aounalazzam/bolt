<?php

namespace Bolt\Lib;

use Bolt\Utils\{InternalServerErrorException};

class EmailSender
{
    static function sendEmail(array $options)
    {
        ['to' => $to, 'subject' => $subject, 'content' => $content] = $options;

        $headers = [
            'X-Mailer: PHP/' . phpversion(),
            'Content-type: text/html; charset=iso-8859-1',
            'MIME-Version: 1.0'
        ];

        return mail($to, $subject, $content, implode("\r\n", $headers));
    }

    static function render(string $path): string
    {
        if (!file_exists($path)) {
            throw new InternalServerErrorException("Template file not found: $path");
        }

        $content = file_get_contents($path);

        ob_start();
        eval("?>$content");
        return ob_get_clean();
    }
}
