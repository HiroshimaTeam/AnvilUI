<?php

namespace Anvil\Commands;

use Anvil\AnvilMain;
use Anvil\AnvilUI;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\Player;
use pocketmine\utils\Config;

class AnvilCommand extends PluginCommand{
    public function __construct(AnvilMain $main)
    {
        parent::__construct("anvil", $main);
        $config = new Config(AnvilMain::getInstance()->getDataFolder() . "config.yml", Config::YAML);
        $this->setDescription($config->get("description"));
        $this->setPermission($config->get("permission"));
    }

    public function execute(CommandSender $player, string $commandLabel, array $args)
    {
        $config = new Config(AnvilMain::getInstance()->getDataFolder() . "config.yml", Config::YAML);
        if (!($player instanceof Player)) return $player->sendMessage($config->get("console"));
        if (!$player->hasPermission($config->get("permission"))) return $player->sendMessage($config->get("noperm"));

        AnvilUI::formMain($player);
        return true;
    }
}