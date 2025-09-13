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

require_once __DIR__ . "/../src/PersianTextEngine.php";

class PersianTextEngineTest extends TestCase
{
    public function testReverse_PersianWithEnglishAndPunctuation(): void
    {
        $this->assertEquals("؟یبوخ (Ali یلع) مالس", PersianTextEngine::reversePersianText("سلام (علی Ali) خوبی؟"));
        $this->assertEquals("؟یبوخ Ali Agha مالس", PersianTextEngine::reversePersianText("سلام Ali Agha خوبی؟"));
        $this->assertEquals("؟یبوخ {یلع} مالس", PersianTextEngine::reversePersianText("سلام {علی} خوبی؟"));
        $this->assertEquals("۱۲۳ مالس", PersianTextEngine::reversePersianText("سلام ۱۲۳"));
        $this->assertEquals(">>> مالس", PersianTextEngine::reversePersianText(">>> سلام"));
        $this->assertEquals("؟یبوخ [Ali] مالس", PersianTextEngine::reversePersianText("سلام [Ali] خوبی؟"));
        $this->assertEquals("<!مبوخ> :تفگ", PersianTextEngine::reversePersianText("گفت: <خوبم!>"));
        $this->assertEquals("؟یبوخ ،مالس", PersianTextEngine::reversePersianText("سلام، خوبی؟"));
    }

    public function testReverse_EnglishOnly(): void
    {
        $this->assertEquals("Salam Ali Khobi?", PersianTextEngine::reversePersianText("Salam Ali Khobi?"));
    }

    public function testCorrectPersianText_SimpleWords(): void
    {
        $this->assertEquals("ﺳﻼﻡ", PersianTextEngine::correctPersianText("سلام"));
        $this->assertEquals("ﺩﻧﯿﺎ", PersianTextEngine::correctPersianText("دنیا"));
        $this->assertEquals("ﮐﺘﺎﺏ", PersianTextEngine::correctPersianText("کتاب"));
        $this->assertEquals("ﻣﺪﺭﺳﻪ", PersianTextEngine::correctPersianText("مدرسه"));
    }

    public function testProcess_SimpleWords(): void
    {
        $this->assertEquals("ﻡﻼﺳ", PersianTextEngine::process("سلام"));
        $this->assertEquals("ﺎﯿﻧﺩ", PersianTextEngine::process("دنیا"));
        $this->assertEquals("ﺏﺎﺘﮐ", PersianTextEngine::process("کتاب"));
        $this->assertEquals("ﻪﺳﺭﺪﻣ", PersianTextEngine::process("مدرسه"));
        $this->assertEquals("ﻩﺩﺎﻔﺘﺳﺍ", PersianTextEngine::process("استفاده"));
        $this->assertEquals("ﺩﺍﺪﻣ", PersianTextEngine::process("مداد"));
        $this->assertEquals("ﻭﺭﺎﮐ", PersianTextEngine::process("کارو"));
    }

    public function testProcess_MixedTextWithPunctuation(): void
    {
        $this->assertEquals("!ﺎﯿﻧﺩ ،ﻡﻼﺳ", PersianTextEngine::process("سلام، دنیا!"));
        $this->assertEquals("؟ﯽﺑﻮﺧ", PersianTextEngine::process("خوبی؟"));
        $this->assertEquals(";ﺎﯿﻧﺩ :ﻡﻼﺳ", PersianTextEngine::process("سلام: دنیا;"));
    }

    public function testProcess_EnglishOrMixedLanguage(): void
    {
        $this->assertEquals("<salam>", PersianTextEngine::process("<salam>"));
        $this->assertEquals("؟ﯽﺑﻮﺧ Ali ﻡﻼﺳ", PersianTextEngine::process("سلام Ali خوبی؟"));
        $this->assertEquals("ﻡﻼﺳ Ali", PersianTextEngine::process("Ali سلام"));
        $this->assertEquals("Book ﺏﺎﺘﮐ", PersianTextEngine::process("کتاب Book"));
        $this->assertEquals("ﯼﺩﻼﯿﻣ 2025 ﻝﺎﺳ", PersianTextEngine::process("سال 2025 میلادی"));
        $this->assertEquals("ﺪﯿﻨﮐ ﯼﺭﺍﺪﯾﺮﺧ ﺍﺭ ﺩﻮﺧ ﺯﺎﯿﻧ ﺩﺭﻮﻣ ﻞﯾﺎﺳﻭ /shop ﺭﻮﺘﺳﺩ ﺯﺍ ﻩﺩﺎﻔﺘﺳﺍ ﺎﺑ", PersianTextEngine::process("با استفاده از دستور /shop وسایل مورد نیاز خود را خریداری کنید"));
        $this->assertEquals("ﺖﺴﺗ 123", PersianTextEngine::process("123 تست"));
        $this->assertEquals("456 ﺖﺴﺗ", PersianTextEngine::process("تست 456"));
        $this->assertEquals("!ﺎﯿﻧﺩ Ali ﻡﻼﺳ", PersianTextEngine::process("سلام Ali دنیا!"));
        $this->assertEquals(".ﻡﺪﯾﺩ ﻭﺭ Ali ﺯﻭﺮﻣﺍ", PersianTextEngine::process("امروز Ali رو دیدم."));
        $this->assertEquals("!ﻪﯿﻟﺎﻋ X2025 ﻝﺪﻣ Tesla ﻭﺭﺩﻮﺧ", PersianTextEngine::process("خودرو Tesla مدل X2025 عالیه!"));
    }

    public function testProcess_LaLigature(): void
    {
        $this->assertEquals("ﮎﻻ", PersianTextEngine::process("لاک"));
        $this->assertEquals("ﺎﯿﻧﺩ ﻻ ﻡﻼﺳ", PersianTextEngine::process("سلام لا دنیا"));
        $this->assertEquals("ﮎﻻ Ali", PersianTextEngine::process("Ali لاک"));
    }

    public function testProcess_QuotesAndParentheses(): void
    {
        $this->assertEquals("(Book) ﺏﺎﺘﮐ", PersianTextEngine::process("کتاب (Book)"));
        $this->assertEquals('Ali "ﻡﻼﺳ"', PersianTextEngine::process("\"سلام\" Ali"));
        $this->assertEquals("Ali 'ﻡﻼﺳ'", PersianTextEngine::process("'سلام' Ali"));
    }

    public function testProcess_EnglishOnlyLowercase(): void
    {
        $this->assertEquals("salam khobi?", PersianTextEngine::process("salam khobi?"));
    }

    public function testProcess_ComplexSentences(): void
    {
        $this->assertEquals(".ﻡﺪﯾﺩ ﻭﺭ Ali ﺯﻭﺮﻣﺍ ؟ﯽﺑﻮﺧ .ﻡﻼﺳ", PersianTextEngine::process("سلام. خوبی؟ امروز Ali رو دیدم."));
    }

    public function testProcess_Colorize(): void
    {
        $this->assertEquals("§csalam khobi?", PersianTextEngine::process("§csalam khobi?"));
        $this->assertEquals("§cﻡﻼﺳ", PersianTextEngine::process("§cسلام"));
        $this->assertEquals("§c?khobi §cﻡﻼﺳ", PersianTextEngine::process("§cسلام khobi?"));
        $this->assertEquals("§c؟ﯽﺑﻮﺧ §2Ali §cﻡﻼﺳ", PersianTextEngine::process("§cسلام §2Ali §cخوبی؟"));
        $this->assertEquals("§c؟ﯽﺑﻮﺧ §csalam", PersianTextEngine::process("§csalam خوبی؟"));
        $this->assertEquals("§2؟ﯽﺑﻮﺧ §2Ali §cﻡﻼﺳ", PersianTextEngine::process("§cسلام §2Ali خوبی؟"));
        $this->assertEquals("§6?khobi §6ﯽﻠﻋ §6salam §3؟ﯽﺑﻮﺧ §3ali §3ﻡﻼﺳ§3  §2؟ﯽﺑﻮﺧ §2ﯽﻠﻋ §2ﻡﻼﺳ §c?salam ali khobi", PersianTextEngine::process("§csalam ali khobi? §2سلام علی خوبی؟ §3 سلام ali خوبی؟ §6salam علی khobi?"));
    }
}