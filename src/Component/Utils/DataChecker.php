<?php

namespace App\Component\Utils;

class DataChecker
{
    /**
     * Проверка email
     *
     * @param string|null $email
     * @return string|null
     */
    public static function tryGetEmail(?string $email): ?string
    {
        if (null === $email) {
            return null;
        }

        $replacedEmail = (string) preg_replace(['/^[\s]+/u', '/[\s]+$/u'], '', $email);

        if (!empty($replacedEmail)) {
            return preg_match('/^[A-Za-z0-9._%-]+@[A-Za-zА-Яа-яё0-9.-]+[.][A-Za-zА-Яа-яё\D]+$/iu', $replacedEmail) === 1
                ? $email
                : null;
        }

        return null;
    }
}
