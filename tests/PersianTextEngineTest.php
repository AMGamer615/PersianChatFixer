<?php

use AMGamer615\PersianChatFixer\PersianTextEngine;
use PHPUnit\Framework\TestCase;

require_once __DIR__. '/../src/PersianTextEngine.php';

class PersianTextEngineTest extends TestCase
{
    public function test1()
    {
        $this->assertEquals("؟یبوخ (Ali یلع) مالس", PersianTextEngine::reversePersianText("سلام (علی Ali) خوبی؟"));
    }

    public function test2()
    {
        $this->assertEquals("Salam Ali Khobi?", PersianTextEngine::reversePersianText("Salam Ali Khobi?"));
    }

    public function test3()
    {
        $this->assertEquals("؟یبوخ Ali Agha مالس", PersianTextEngine::reversePersianText("سلام Ali Agha خوبی؟"));
    }

    public function test4()
    {
        $this->assertEquals("؟یبوخ {یلع} مالس", PersianTextEngine::reversePersianText("سلام {علی} خوبی؟"));
    }

    public function test5()
    {
        $this->assertEquals("؟یبوخ [Ali] مالس", PersianTextEngine::reversePersianText("سلام [Ali] خوبی؟"));
    }

    public function test6()
    {
        $this->assertEquals("<!مبوخ> :تفگ", PersianTextEngine::reversePersianText("گفت: <خوبم!>"));
    }

    public function test7()
    {
        $this->assertEquals("؟یبوخ ،مالس", PersianTextEngine::reversePersianText("سلام، خوبی؟"));
    }
}