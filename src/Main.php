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

declare(strict_types=1);

namespace AMGamer615\PersianChatFixer;

use pocketmine\block\utils\SignText;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;

class Main extends PluginBase implements Listener{
    use SingletonTrait;

    protected function onEnable(): void
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    /** @priority LOW */
    public function onPlayerChat(PlayerChatEvent $event): void
    {
        $event->setMessage(PersianTextEngine::process($event->getMessage()));
    }

    /** @priority LOW */
    public function onSignChange(SignChangeEvent $event): void
    {
        $signText = $event->getNewText();
        $lines = array_map(fn($line) => PersianTextEngine::process($line), $signText->getLines());
        $event->setNewText(new SignText($lines, $signText->getBaseColor(), $signText->isGlowing()));
    }
}
