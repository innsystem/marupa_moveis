<?php

namespace App\Helpers;

class MessageHelper
{
    public static function getMessage($module, $channel, $action, $data = [])
    {
        $messages = include base_path("app/Messages/{$module}Messages.php");

        if (!isset($messages[$channel][$action])) {
            return null;
        }

        $message = $messages[$channel][$action];

        if (is_array($message)) {
            $message['subject'] = self::replacePlaceholders($message['subject'], $data);
            $message['body'] = self::replacePlaceholders($message['body'], $data);
            return $message;
        }

        return self::replacePlaceholders($message, $data);
    }

    private static function replacePlaceholders($content, $data)
    {
        // Substitui os placeholders normais
        foreach ($data as $key => $value) {
            $content = str_replace("{{{$key}}}", $value, $content);
        }

        // Remove blocos condicionais se o campo não existir ou estiver vazio
        $content = preg_replace_callback('/\{\#if ([^}]+)\}(.*?)\{\/if\}/s', function ($matches) use ($data) {
            $field = trim($matches[1]);
            if (!empty($data[$field])) {
                return $matches[2];
            }
            return '';
        }, $content);

        // Remove qualquer placeholder que sobrou
        $content = preg_replace('/\{\{[^}]+\}\}/', '', $content);

        return $content;
    }
}
