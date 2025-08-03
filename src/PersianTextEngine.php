<?php

/*
 *     ___    __  _________                          ______________
 *    /   |  /  |/  / ____/___ _____ ___  ___  _____/ ___<  / ____/
 *   / /| | / /|_/ / / __/ __ `/ __ `__ \/ _ \/ ___/ __ \/ /___ \
 *  / ___ |/ /  / / /_/ / /_/ / / / / / /  __/ /  / /_/ / /___/ /
 * /_/  |_/_/  /_/\____/\__,_/_/ /_/ /_/\___/_/   \____/_/_____/
 *
 * MIT License - Copyright (c) 2025 AMGamer615
 * Permission is granted to use, copy, modify, and distribute this software,
 * provided the copyright notice and this permission notice are included.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND.
 *
 * @Author: AMGamer615
 * @Link: https://github.com/AMGamer615
 *
 */

namespace AMGamer615\PersianChatFixer;

class PersianTextEngine
{
    private static array $glyphs = [
        "ا"=>["ا","ﺍ","ﺎ","ﺎ"], "آ"=>["آ","ﺁ","ﺂ","ﺂ"],
        "ب"=>["ب","ﺑ","ﺒ","ﺐ"], "پ"=>["پ","ﭘ","ﭙ","ﭗ"],
        "ت"=>["ت","ﺗ","ﺘ","ﺖ"], "ث"=>["ث","ﺛ","ﺜ","ﺚ"], "ج"=>["ج","ﺟ","ﺠ","ﺞ"],
        "چ"=>["چ","ﭼ","ﭽ","ﭻ"], "ح"=>["ح","ﺣ","ﺤ","ﺢ"], "خ"=>["خ","ﺧ","ﺨ","ﺦ"],
        "د"=>["د","ﺩ","ﺪ","ﺪ"], "ذ"=>["ذ","ﺫ","ﺬ","ﺬ"], "ر"=>["ر","ﺭ","ﺮ","ﺮ"],
        "ز"=>["ز","ﺯ","ﺰ","ﺰ"], "ژ"=>["ژ","ﮊ","ﮋ","ﮋ"], "س"=>["س","ﺳ","ﺴ","ﺲ"],
        "ش"=>["ش","ﺷ","ﺸ","ﺶ"], "ص"=>["ص","ﺻ","ﺼ","ﺺ"], "ض"=>["ض","ﺿ","ﻀ","ﺾ"],
        "ط"=>["ط","ﻃ","ﻄ","ﻂ"], "ظ"=>["ظ","ﻇ","ﻈ","ﻆ"], "ع"=>["ع","ﻋ","ﻌ","ﻊ"],
        "غ"=>["غ","ﻏ","ﻐ","ﻎ"], "ف"=>["ف","ﻓ","ﻔ","ﻒ"], "ق"=>["ق","ﻗ","ﻘ","ﻖ"],
        "ک"=>["ک","ﮐ","ﮑ","ﮏ"], "گ"=>["گ","ﮔ","ﮕ","ﮓ"], "ل"=>["ل","ﻟ","ﻠ","ﻞ"],
        "م"=>["م","ﻣ","ﻤ","ﻢ"], "ن"=>["ن","ﻧ","ﻨ","ﻦ"], "و"=>["و","ﻭ","ﻮ","ﻮ"],
        "ه"=>["ه","ﻫ","ﻬ","ﻪ"], "ی"=>["ی","ﯾ","ﯿ","ﯽ"],
    ];

    private static array $nonConnectors = ["ا"=>1,"آ"=>1,"د"=>1,"ذ"=>1,"ر"=>1,"ز"=>1,"ژ"=>1,"و"=>1];

    public static function correctPersianText(string $text): string
    {
        $glyphs = self::$glyphs;
        $nonConnectors = self::$nonConnectors;

        $chars = self::mb_str_split($text);
        $count = count($chars);
        $result = [];

        for ($i = 0; $i < $count; $i++) {
            $curr = $chars[$i];

            $prev = $i > 0 ? $chars[$i - 1] : null;
            $next = $i < $count - 1 ? $chars[$i + 1] : null;

            if ($curr === 'ل' && $next === 'ا') {
                $hasPrev = $prev !== null;
                $prevGlyph = $hasPrev && isset($glyphs[$prev]);
                $connectsBefore = $prevGlyph && !isset($nonConnectors[$prev]);

                $result[] = $connectsBefore ? 'ﻼ' : 'ﻻ';
                $i++;
                continue;
            }

            if (!isset($glyphs[$curr])) {
                $result[] = $curr;
                continue;
            }

            $hasPrev = $prev !== null;
            $hasNext = $next !== null;
            $prevGlyph = $hasPrev && isset($glyphs[$prev]);
            $nextGlyph = $hasNext && isset($glyphs[$next]);

            $connectsBefore = $prevGlyph && !isset($nonConnectors[$prev]);
            $connectsAfter = !isset($nonConnectors[$curr]) && $nextGlyph;

            $form = ($connectsBefore ? ($connectsAfter ? 2 : 3) : ($connectsAfter ? 1 : 0));
            $result[] = $glyphs[$curr][$form];
        }

        return implode('', $result);
    }

    public static function reversePersianText(string $text): string
    {
        static $hasArabicRegex    = null;
        static $tokenizeRegex     = null;
        static $bracketWrapRegex  = null;
        static $numberRegex       = null;
        static $latinRegex        = null;
        static $symbolRegex       = null;

        if ($hasArabicRegex === null) {
            $hasArabicRegex    = '/\p{Arabic}/u';
            $tokenizeRegex     = '/([(\[{<][^)\]}>]*[)\]}>]|\p{Latin}[\p{Latin}\d ]*|\s+|\S+)/u';
            $bracketWrapRegex  = '/^([(\[{<])(.*)([)\]}>])$/us';
            $numberRegex       = '/^\p{N}+$/u';
            $latinRegex        = '/^[A-Za-z0-9 ]+$/u';
            $symbolRegex       = '/^[><\]\[]+$/u';
        }

        if (!preg_match($hasArabicRegex, $text)) {
            return $text;
        }

        preg_match_all($tokenizeRegex, $text, $matches);
        $tokens = $matches[0];
        $processedTokens = [];

        foreach (array_reverse($tokens) as $tok) {
            $trimmed = trim($tok);
            if ($trimmed === '') {
                continue;
            }

            if (preg_match($bracketWrapRegex, $tok, $m)) {
                $open  = $m[1];
                $inner = $m[2];
                $close = $m[3];
                $innerReversed = self::reversePersianText($inner);
                $processedTokens[] = $open . $innerReversed . $close;
            }
            elseif (preg_match($symbolRegex, $tok)) {
                array_unshift($processedTokens, $tok);
            }
            elseif (preg_match($latinRegex, $tok)) {
                $processedTokens[] = $tok;
            }
            elseif (preg_match($hasArabicRegex, $tok) && !preg_match($numberRegex, $tok)) {
                $chars = preg_split('//u', $tok, -1, PREG_SPLIT_NO_EMPTY);
                $chars = array_reverse($chars);
                $processedTokens[] = implode('', $chars);
            }
            else {
                $processedTokens[] = $tok;
            }
        }

        $trimmedTokens = array_map('trim', $processedTokens);
        $result = implode(' ', $trimmedTokens);
        return rtrim($result);
    }

    public static function mb_str_split(string $string): array
    {
        $chars = [];
        $len = strlen($string);
        $i = 0;

        while ($i < $len) {
            $byte = ord($string[$i]);

            if ($byte < 0x80) {
                $clen = 1;
            } elseif ($byte < 0xE0) {
                $clen = 2;
            } elseif ($byte < 0xF0) {
                $clen = 3;
            } else {
                $clen = 4;
            }

            $chars[] = substr($string, $i, $clen);
            $i += $clen;
        }

        return $chars;
    }

    public static function process(string $text): string
    {
        return self::reversePersianText(self::correctPersianText($text));
    }
}