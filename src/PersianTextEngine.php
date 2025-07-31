<?php

namespace AMGamer615\PersianChatFixer;

class PersianTextEngine
{
    public static function correctPersianText(string $text): string
    {
        return $text;
    }

    public static function reversePersianText(string $text): string
    {
        if (!preg_match('/\p{Arabic}/u', $text)) {
            return $text;
        }

        preg_match_all(
            '/(\s+|[(\[{<][^)\]}>]*[)\]}>]|[^\s()\[\]{}<>]+)/u',
            $text,
            $matches
        );
        $rawTokens = $matches[1];

        $tokens = [];
        $buffer = '';
        $count = count($rawTokens);
        for ($i = 0; $i < $count; $i++) {
            $tok = $rawTokens[$i];
            $next = $i + 1 < $count ? $rawTokens[$i + 1] : null;

            if (preg_match('/\p{Arabic}/u', $tok)) {
                if ($buffer !== '') {
                    $tokens[] = $buffer;
                    $buffer = '';
                }
                $tokens[] = $tok;
            }
            elseif (preg_match('/^\s+$/u', $tok)) {
                if ($buffer !== '' && $next !== null && !preg_match('/\p{Arabic}/u', $next)) {
                    $buffer .= $tok;
                } else {
                    if ($buffer !== '') {
                        $tokens[] = $buffer;
                        $buffer = '';
                    }
                    $tokens[] = $tok;
                }
            }
            else {
                $buffer .= $tok;
            }
        }
        if ($buffer !== '') {
            $tokens[] = $buffer;
        }

        $tokens = array_reverse($tokens);

        $result = '';
        foreach ($tokens as $tok) {
            if (preg_match('/^([(\[{<])(.*)([)\]}>])$/us', $tok, $m)) {
                $open  = $m[1];
                $inner = $m[2];
                $close = $m[3];
                $innerReversed = self::reversePersianText($inner);
                $result .= $open . $innerReversed . $close;
            }
            elseif (preg_match('/\p{Arabic}/u', $tok)) {
                $chars = mb_str_split($tok);
                $chars = array_reverse($chars);
                $result .= implode('', $chars);
            }
            else {
                $result .= $tok;
            }
        }

        return $result;
    }
}