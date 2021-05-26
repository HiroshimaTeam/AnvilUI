<?php

namespace Anvil\Commands;

use Anvil\AnvilMain;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\utils\Config;

class AnvilReloadCommand extends PluginCommand{
    public function __construct(AnvilMain $main)
    {
        parent::__construct("anvilreload", $main);
        $config = new Config(AnvilMain::getInstance()->getDataFolder() . "config.yml", Config::YAML);
        $this->setDescription($config->get("descriptionr"));
        $this->setPermission($config->get("permissionr"));
    }

    public function execute(CommandSender $player, string $commandLabel, array $args)
    {
        $config = new Config(AnvilMain::getInstance()->getDataFolder() . "config.yml", Config::YAML);
        if (isset($args[0]) and $args[0] === "info") return $player->sendMessage("AnvilUI by Digueloulou12. Version 2.0");
        if (!$player->hasPermission($config->get("permissionr"))) return $player->sendMessage($config->get("nopermr"));


        $config->reload();
        $player->sendMessage($config->get("reloadgood"));
        return true;
    }
}