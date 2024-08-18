<?php

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
}
