<?php

namespace App\Service;

class LudoxHelper
{
    private const DICTIONARY = 'ABCDEFGHIJKLMNPQRSTUVWXYZ123456789';

    /**
     * Generates unique key.
     *
     * @param int $segments
     * @param int $chars
     * @param string $separator
     * @return string
     */
    public function generateUniqueKey(int $segments = 1, int $chars = 6, string $separator = '-'): string
    {
        $unique = '';

        for ($i = 0; $i < $segments; $i++) {
            $segment = '';

            for ($j = 0; $j < $chars; $j++) {
                $segment .= self::DICTIONARY[rand(0, strlen(self::DICTIONARY) - 1)];
            }

            $unique .= $segment;

            if ($i < ($segments - 1)) {
                $unique .= $separator;
            }
        }

        return $unique;
    }
}
