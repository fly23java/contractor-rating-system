<?php

namespace App\Helpers;

use ArPHP\I18N\Arabic;

class ArabicHtml
{
    public static function reshape($text)
    {
        $arabic = new Arabic('Glyphs');
        return $arabic->utf8Glyphs($text);
    }
}
