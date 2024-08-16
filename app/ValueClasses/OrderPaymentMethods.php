<?php

namespace App\ValueClasses;

class OrderPaymentMethods
{

    const BOLETO = 'boleto';
    const CARD = 'credit_card';
    const PIX = 'pix';

    private static $methodMap = [
        'card' => self::CARD,
        'credit' => self::CARD,
        'credit_card' => self::CARD,
        'boleto' => self::BOLETO,
        'pix' => self::PIX,
    ];

    public static function getMethodName($method)
    {
        return self::$methodMap[$method];
    }
}
