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
use pocketmine\event\player\PlayerResourcePackOfferEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\resourcepacks\ResourcePack;
use pocketmine\utils\SingletonTrait;

class Main extends PluginBase implements Listener
{
    use SingletonTrait;

    private const ResourcePackName = "PersianFontPack.zip";
    private static ResourcePack $resourcePack;

    /** @priority LOW */
    public function onPlayerChat(PlayerChatEvent $event): void
    {
        $event->setMessage(PersianTextEngine::process($event->getMessage()));
    }

    /** @priority LOW */
    public function onSignChange(SignChangeEvent $event): void
    {
        $oldSignText = $event->getNewText();
        $originalLines = $oldSignText->getLines();

        $wrappedLines = [];

        foreach ($originalLines as $line) {
            if (mb_strlen($line, "UTF-8") > 14) {
                $lineParts = mb_str_split($line, 14, "UTF-8");
                foreach ($lineParts as $part) {
                    $wrappedLines[] = $part;
                }
            } else {
                $wrappedLines[] = $line;
            }
        }

        $processedLines = array_map(static fn($line) => PersianTextEngine::process($line), array_slice($wrappedLines, 0, 4));

        $event->setNewText(new SignText(
            $processedLines,
            $oldSignText->getBaseColor(),
            $oldSignText->isGlowing()
        ));
    }

    /** @priority LOW */
    public function onPlayerResourcePackOffer(PlayerResourcePackOfferEvent $event): void
    {
        $event->addResourcePack(self::$resourcePack, "AMGamer615");
    }

    protected function onEnable(): void
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }
}
