<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('money', [$this, 'formatCents']),
        ];
    }

    public function formatCents(int $cents)
    {
        return $cents / 100;
    }
}
