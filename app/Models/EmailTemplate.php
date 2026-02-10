<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $fillable = [
        'key',
        'subject',
        'body',
        'description',
    ];

    public static function render($key, $placeholders = [])
    {
        $template = self::where('key', $key)->first();

        if (!$template) {
            return null;
        }

        $subject = $template->subject;
        $body = $template->body;

        foreach ($placeholders as $placeholder => $value) {
            $subject = str_replace('{{ ' . $placeholder . ' }}', $value, $subject);
            $body = str_replace('{{ ' . $placeholder . ' }}', $value, $body);
        }

        return [
            'subject' => $subject,
            'body' => $body,
        ];
    }
}
