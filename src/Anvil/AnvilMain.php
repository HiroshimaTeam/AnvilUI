<?php

namespace Anvil;

use Anvil\Commands\AnvilCommand;
use Anvil\Commands\AnvilReloadCommand;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;

class AnvilMain extends PluginBase{
    private static $main;
    public function onEnable()
    {
        // Message
        $this->getLogger()->info("AnvilUI on by Digueloulou12");
        self::$main = $this;

        // Config
        $config = $this->getConfig();
        $this->saveDefaultConfig();
        if ($config->get("config") != 2){
            $this->getLogger()->info("Your configurations are more up to date! Remove them and restart your server! ");
        }

        // Commands
        $config = $this->getConfig();
        if ($config->get("command") === true){
            $this->getServer()->getCommandMap()->register("anvil", new AnvilCommand($this));
        }
        $this->getServer()->getCommandMap()->register("anvilreload", new AnvilReloadCommand($this));

        // Event
        $this->getServer()->getPluginManager()->registerEvents(new AnvilEvent(), $this);
    }

    public function onDisable()
    {
        // Unload
        $this->getLogger()->info("AnvilUI off by Digueloulou12");
    }

    public static function getInstance(): AnvilMain{
        return self::$main;
    }
}