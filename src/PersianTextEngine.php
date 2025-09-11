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

        $chars = mb_str_split($text);
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

            if ($connectsBefore) {
                if ($connectsAfter) {
                    $form = 2;
                } else {
                    $form = 3;
                }
            } else if ($connectsAfter) {
                $form = 1;
            } else {
                $form = 0;
            }
            $result[] = $glyphs[$curr][$form];
        }

        return implode('', $result);
    }

    public static function reversePersianText(string $text): string
    {
        static $hasArabicRegex = null;
        static $numberRegex    = null;
        static $symbolRegex    = null;

        if ($hasArabicRegex === null) {
            $hasArabicRegex = '/\p{Arabic}/u';
            $numberRegex    = '/^\p{N}+$/u';
            $symbolRegex    = '/^[><\[\]]+$/';
        }

        // extract leading color codes like §c§1...
        $leadingColor = '';
        if (preg_match('/^(?:§.)+/u', $text, $lm)) {
            $leadingColor = $lm[0];
            $text = mb_substr($text, mb_strlen($leadingColor, 'UTF-8'), null, 'UTF-8');
        }

        // extract leading symbol(s) like >>> or > or [ etc plus following spaces
        $leadingSymbol = '';
        if (preg_match('/^([><\[\]]+)(\s*)/u', $text, $sm)) {
            $leadingSymbol = $sm[1] . ($sm[2] ?? '');
            $text = mb_substr($text, mb_strlen($leadingSymbol, 'UTF-8'), null, 'UTF-8');
        }

        // if there's no Arabic at all, return quickly (preserve leading color & symbol)
        if (!preg_match($hasArabicRegex, $text)) {
            return $leadingColor . $leadingSymbol . $text;
        }

        // tokenize: keep bracketed groups, whitespace, or other chunks
        preg_match_all('/(?:§.)*(?:\([^)]*\)|\[[^]]*]|\{[^}]*}|<[^>]*>|\s+|[^\s(){}\[\]<>]+)/u', $text, $m);
        $tokens = $m[0];

        // helper: split token into prefix(color codes) and core
        $splitPrefix = static function (string $tok): array {
            if (preg_match('/^(?:§.)+/u', $tok, $pm)) {
                $pref = $pm[0];
                $core = mb_substr($tok, mb_strlen($pref, 'UTF-8'), null, 'UTF-8');
                return [$pref, $core];
            }
            return ['', $tok];
        };

        // Merge contiguous Latin (ASCII/digit) runs so their internal order stays intact after reverse
        $merged = [];
        $n = count($tokens);
        $i = 0;
        while ($i < $n) {
            $t = $tokens[$i];

            if (preg_match('/^\s+$/u', $t)) {
                $merged[] = $t;
                $i++;
                continue;
            }

            if (preg_match('/^[(\[{<].*[)\]}>]$/us', $t)) {
                $merged[] = $t;
                $i++;
                continue;
            }

            [, $core] = $splitPrefix($t);
            if (preg_match('/[A-Za-z0-9]/u', $core)) {
                $buf = $t;
                $j = $i + 1;
                while ($j + 1 < $n) {
                    if (!preg_match('/^\s+$/u', $tokens[$j])) {
                        break;
                    }
                    [, $nextCore] = $splitPrefix($tokens[$j + 1]);
                    if (!preg_match('/[A-Za-z0-9]/u', $nextCore)) {
                        break;
                    }
                    $buf .= $tokens[$j] . $tokens[$j + 1];
                    $j += 2;
                }
                $merged[] = $buf;
                $i = $j;
                continue;
            }

            $merged[] = $t;
            $i++;
        }

        // First pass (left -> right): determine applied color for each merged token
        $lastPrefix = $leadingColor; // last seen prefix (affects subsequent tokens without their own prefix)
        $tokenObjs = [];

        foreach ($merged as $t) {
            if (preg_match('/^\s+$/u', $t)) {
                // whitespace: keep as-is, no applied color
                $tokenObjs[] = [
                    'raw'     => $t,
                    'prefix'  => '',
                    'core'    => $t,
                    'applied' => ''
                ];
                continue;
            }

            [$pref, $core] = $splitPrefix($t);

            if ($pref !== '') {
                // token carries its own prefix: update lastPrefix and applied = pref
                $lastPrefix = $pref;
                $applied = $pref;
            } else {
                // no prefix: applied color is lastPrefix (may be empty)
                $applied = $lastPrefix;
            }

            $tokenObjs[] = [
                'raw'     => $t,
                'prefix'  => $pref,
                'core'    => $core,
                'applied' => $applied
            ];
        }

        // reverse tokens for output
        $tokenObjs = array_reverse($tokenObjs);

        // Build output using token-specific applied colors
        $outParts = [];
        foreach ($tokenObjs as $tokObj) {
            $raw = $tokObj['raw'];
            $core = $tokObj['core'];
            $colorForToken = $tokObj['applied'];

            if (preg_match('/^\s+$/u', $raw)) {
                $outParts[] = $raw;
                continue;
            }

            // Bracketed groups: recursively reverse inner content, keep the token's applied color
            if (preg_match('/^(?:§.)*([(\[{<])/u', $raw)) {
                if (preg_match('/^\((.*)\)$/us', $core, $im)) {
                    $inner = self::reversePersianText($im[1]);
                    $outParts[] = $colorForToken . '(' . $inner . ')';
                    continue;
                }
                if (preg_match('/^\[(.*)]$/us', $core, $im)) {
                    $inner = self::reversePersianText($im[1]);
                    $outParts[] = $colorForToken . '[' . $inner . ']';
                    continue;
                }
                if (preg_match('/^\{(.*)}$/us', $core, $im)) {
                    $inner = self::reversePersianText($im[1]);
                    $outParts[] = $colorForToken . '{' . $inner . '}';
                    continue;
                }
                if (preg_match('/^<(.*)>$/us', $core, $im)) {
                    $inner = self::reversePersianText($im[1]);
                    $outParts[] = $colorForToken . '<' . $inner . '>';
                    continue;
                }
            }

            // symbol-only tokens like >>> or >>> with color
            if (preg_match($symbolRegex, $core)) {
                $outParts[] = $colorForToken . $core;
                continue;
            }

            // Latin/ASCII words with trailing punctuation: move punctuation in front
            if (!preg_match($hasArabicRegex, $core) && preg_match('/[A-Za-z0-9]/u', $core)) {
                if (preg_match('/^(.*?)([\p{P}\p{S}]+)$/u', $core, $pm)) {
                    [, $word, $pun] = $pm;
                    $core = $pun . $word;
                }
                $outParts[] = $colorForToken . $core;
                continue;
            }

            // Arabic words (non-numeric): reverse character order
            if (preg_match($hasArabicRegex, $core) && !preg_match($numberRegex, $core)) {
                $chars = preg_split('//u', $core, -1, PREG_SPLIT_NO_EMPTY);
                $chars = array_reverse($chars);
                $outParts[] = $colorForToken . implode('', $chars);
                continue;
            }

            // default: emit with applied color
            $outParts[] = $colorForToken . $core;
        }

        return $leadingSymbol . implode('', $outParts);
    }

    public static function process(string $text): string
    {
        return self::reversePersianText(self::correctPersianText($text));
    }
}