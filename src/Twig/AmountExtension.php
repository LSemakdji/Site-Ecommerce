<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AmountExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('amount', [$this, 'amount'])
        ];
    }
    public function amount($value, string $symbol = '€', string $decsep = ',', string $thousandsep = ' ')
    {
        // on peut surcharger le twig en changeant le symbol ,le separateur des decimales , le separateur des miliers si on le souhaite
        //19229 => 192,29 €
        //192...29
        $finalValue = $value / 100;
        //192,,,29
        $finalValue = number_format($finalValue, 2, $decsep, $thousandsep);
        //192,29€
        return $finalValue . ' ' . $symbol;
    }
}
