<?php

namespace App\Support;

/**
 * Phone numbers in one comparable form, so a caller is recognised whatever
 * way their number was typed or reported.
 *
 *   "0300 1234567"    -> +923001234567   (local mobile)
 *   "92 300 1234567"  -> +923001234567
 *   "+1 (213) 777-1234" -> +12137771234  (already international)
 *
 * Pakistani numbers written locally (a leading 0) gain +92; anything already
 * international keeps its own country code.
 */
class Phone
{
    public const LOCAL_COUNTRY_CODE = '92';

    public static function normalize(?string $phone): ?string
    {
        if ($phone === null || trim($phone) === '') {
            return null;
        }

        $international = str_starts_with(trim($phone), '+') || str_starts_with(trim($phone), '00');
        $digits = preg_replace('/\D/', '', $phone);
        $digits = str_starts_with(trim($phone), '00') ? substr($digits, 2) : $digits;

        if (strlen($digits) < 7) {
            return null;
        }

        if (! $international) {
            if (str_starts_with($digits, '0')) {
                $digits = self::LOCAL_COUNTRY_CODE.substr($digits, 1);
            } elseif (! str_starts_with($digits, self::LOCAL_COUNTRY_CODE) && strlen($digits) === 10) {
                $digits = self::LOCAL_COUNTRY_CODE.$digits; // "300 1234567"
            }
        }

        return '+'.$digits;
    }
}
