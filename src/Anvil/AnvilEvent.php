<?php

namespace Anvil;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\utils\Config;

class AnvilEvent implements Listener{
    public function onInteract(PlayerInteractEvent $event){
        $config = new Config(AnvilMain::getInstance()->getDataFolder()."config.yml", Config::YAML);
        $player = $event->getPlayer();
        $anvil = $event->getBlock();
        $action = $event->getAction();
        $item = $event->getItem();

        $event->setCancelled(true);
        if ($anvil->getId() === 145){
            if ($action === 1){
                if ($item->getId() != 0){
                    AnvilUI::formMain($player);
                }else $player->sendMessage($config->get("noitem"));
            }
        }
    }
}