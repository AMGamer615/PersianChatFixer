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

use AMGamer615\PersianChatFixer\PersianTextEngine;
use PHPUnit\Framework\TestCase;

require_once __DIR__. '/../src/PersianTextEngine.php';

class PersianTextEngineTest extends TestCase
{
    public function testReverse_PersianWithEnglishAndPunctuation()
    {
        $this->assertEquals("؟یبوخ (Ali یلع) مالس", PersianTextEngine::reversePersianText("سلام (علی Ali) خوبی؟"));
        $this->assertEquals("؟یبوخ Ali Agha مالس", PersianTextEngine::reversePersianText("سلام Ali Agha خوبی؟"));
        $this->assertEquals("؟یبوخ {یلع} مالس", PersianTextEngine::reversePersianText("سلام {علی} خوبی؟"));
        $this->assertEquals("؟یبوخ [Ali] مالس", PersianTextEngine::reversePersianText("سلام [Ali] خوبی؟"));
        $this->assertEquals("<!مبوخ> :تفگ", PersianTextEngine::reversePersianText("گفت: <خوبم!>"));
        $this->assertEquals("؟یبوخ ،مالس", PersianTextEngine::reversePersianText("سلام، خوبی؟"));
    }

    public function testReverse_EnglishOnly()
    {
        $this->assertEquals("Salam Ali Khobi?", PersianTextEngine::reversePersianText("Salam Ali Khobi?"));
    }

    public function testCorrectPersianText_SimpleWords()
    {
        $this->assertEquals('ﺳﻼم', PersianTextEngine::correctPersianText("سلام"));
        $this->assertEquals('دﻧﯿﺎ', PersianTextEngine::correctPersianText("دنیا"));
        $this->assertEquals('ﮐﺘﺎب', PersianTextEngine::correctPersianText("کتاب"));
        $this->assertEquals('ﻣﺪرﺳﻪ', PersianTextEngine::correctPersianText("مدرسه"));
    }

    public function testProcess_SimpleWords()
    {
        $this->assertEquals('مﻼﺳ', PersianTextEngine::process("سلام"));
        $this->assertEquals('ﺎﯿﻧد', PersianTextEngine::process("دنیا"));
        $this->assertEquals('بﺎﺘﮐ', PersianTextEngine::process("کتاب"));
        $this->assertEquals('ﻪﺳرﺪﻣ', PersianTextEngine::process("مدرسه"));
        $this->assertEquals('داﺪﻣ', PersianTextEngine::process("مداد"));
        $this->assertEquals('ورﺎﮐ', PersianTextEngine::process("کارو"));
    }

    public function testProcess_MixedTextWithPunctuation()
    {
        $this->assertEquals('!ﺎﯿﻧد ،مﻼﺳ', PersianTextEngine::process("سلام، دنیا!"));
        $this->assertEquals('؟ﯽﺑﻮﺧ', PersianTextEngine::process("خوبی؟"));
        $this->assertEquals(';ﺎﯿﻧد :مﻼﺳ', PersianTextEngine::process("سلام: دنیا;"));
    }

    public function testProcess_EnglishOrMixedLanguage()
    {
        $this->assertEquals('<salam>', PersianTextEngine::process("<salam>"));
        $this->assertEquals('؟ﯽﺑﻮﺧ Ali مﻼﺳ', PersianTextEngine::process("سلام Ali خوبی؟"));
        $this->assertEquals('مﻼﺳ Ali', PersianTextEngine::process("Ali سلام"));
        $this->assertEquals('Book بﺎﺘﮐ', PersianTextEngine::process("کتاب Book"));
        $this->assertEquals('یدﻼﯿﻣ 2025 لﺎﺳ', PersianTextEngine::process("سال 2025 میلادی"));
        $this->assertEquals('ﺖﺴﺗ 123', PersianTextEngine::process("123 تست"));
        $this->assertEquals('456 ﺖﺴﺗ', PersianTextEngine::process("تست 456"));
        $this->assertEquals('!ﺎﯿﻧد Ali مﻼﺳ', PersianTextEngine::process("سلام Ali دنیا!"));
        $this->assertEquals('.مﺪﯾد ور Ali زوﺮﻣا', PersianTextEngine::process("امروز Ali رو دیدم."));
        $this->assertEquals('!ﻪﯿﻟﺎﻋ X2025 لﺪﻣ Tesla وردﻮﺧ', PersianTextEngine::process("خودرو Tesla مدل X2025 عالیه!"));
    }

    public function testProcess_LaLigature()
    {
        $this->assertEquals('کﻻ', PersianTextEngine::process("لاک"));
        $this->assertEquals('ﺎﯿﻧد ﻻ مﻼﺳ', PersianTextEngine::process("سلام لا دنیا"));
        $this->assertEquals('کﻻ Ali', PersianTextEngine::process("Ali لاک"));
    }

    public function testProcess_QuotesAndParentheses()
    {
        $this->assertEquals('(Book) بﺎﺘﮐ', PersianTextEngine::process("کتاب (Book)"));
        $this->assertEquals('Ali "مﻼﺳ"', PersianTextEngine::process("\"سلام\" Ali"));
    }

    public function testProcess_EnglishOnlyLowercase()
    {
        $this->assertEquals("salam khobi?", PersianTextEngine::process("salam khobi?"));
    }

    public function testProcess_ComplexSentences()
    {
        $this->assertEquals('.مﺪﯾد ور Ali زوﺮﻣا ؟ﯽﺑﻮﺧ .مﻼﺳ', PersianTextEngine::process("سلام. خوبی؟ امروز Ali رو دیدم."));
    }
}