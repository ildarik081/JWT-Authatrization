<?php

namespace App\Component\Utils;

class Postman
{
    private static ?self $instance = null;

    private function __construct()
    {
    }

    public static function getInstance(): Postman
    {
        if (null !== self::$instance) {
            return self::$instance;
        }

        self::$instance = new self();

        return self::$instance;
    }
}
