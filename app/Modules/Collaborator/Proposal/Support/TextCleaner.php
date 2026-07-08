<?php

namespace App\Modules\Collaborator\Proposal\Support;

class TextCleaner
{
    private const REPLACEMENTS = [
        ' de nico ' => ' de um técnico ',
        ' nico '    => ' técnico ',
        ' tecnico ' => ' técnico ',
        'Tecnico'   => 'Técnico',
    ];

    public static function clean(string $text): string
    {
        $text = trim(preg_replace('/\s+/', ' ', $text));

        return str_replace(
            array_keys(self::REPLACEMENTS),
            array_values(self::REPLACEMENTS),
            $text
        );
    }

    public static function lines(?string $text): array
    {
        return collect(preg_split('/\r\n|\r|\n|;/', (string) $text))
            ->map(fn (string $line): string => trim($line))
            ->filter()
            ->values()
            ->all();
    }
}
