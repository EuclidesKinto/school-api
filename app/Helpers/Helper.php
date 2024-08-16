<?php

namespace App\Helpers;

class Helper
{

    public static function getInstallments($value, $installmentsNumber)
    {

        $installments = [
            '1' => $value,
            '2' => ($value / 2) + ($value * 0.036),
            '3' => ($value / 3) + ($value * 0.036),
            '4' => ($value / 4) + ($value * 0.036),
            '5' => ($value / 5) + ($value * 0.036),
            '6' => ($value / 6) + ($value * 0.036),
            '7' => ($value / 7) + ($value * 0.041),
            '8' => ($value / 8) + ($value * 0.041),
            '9' => ($value / 9) + ($value * 0.041),
            '10' => ($value / 10) + ($value * 0.041),
            '11' => ($value / 11) + ($value * 0.041),
            '12' => ($value / 12) + ($value * 0.041)
        ];

        return $installments[$installmentsNumber] * $installmentsNumber;
    }
}