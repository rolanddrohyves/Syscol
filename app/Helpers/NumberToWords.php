<?php
// app/Helpers/NumberToWords.php

namespace App\Helpers;

class NumberToWords
{
    public static function convert($number)
    {
        $units = ['', 'un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept', 'huit', 'neuf', 'dix', 
                  'onze', 'douze', 'treize', 'quatorze', 'quinze', 'seize', 'dix-sept', 'dix-huit', 'dix-neuf'];
        $tens = ['', 'dix', 'vingt', 'trente', 'quarante', 'cinquante', 'soixante', 'soixante-dix', 
                 'quatre-vingt', 'quatre-vingt-dix'];
        
        if ($number == 0) return 'zéro';
        
        $words = [];
        
        if ($number >= 1000) {
            $thousands = floor($number / 1000);
            $words[] = $thousands == 1 ? 'mille' : self::convert($thousands) . ' mille';
            $number %= 1000;
        }
        
        if ($number >= 100) {
            $hundreds = floor($number / 100);
            $words[] = $hundreds == 1 ? 'cent' : $units[$hundreds] . ' cent';
            $number %= 100;
        }
        
        if ($number >= 20) {
            $ten = floor($number / 10);
            $unit = $number % 10;
            
            if ($ten == 7 || $ten == 9) {
                $words[] = $tens[$ten - 1] . '-' . $units[$unit + 10];
            } else {
                $word = $tens[$ten];
                if ($unit == 1 && $ten < 8) {
                    $word .= ' et';
                }
                if ($unit > 0) {
                    $word .= '-' . $units[$unit];
                }
                $words[] = $word;
            }
        } elseif ($number > 0) {
            $words[] = $units[$number];
        }
        
        return implode(' ', $words);
    }
}