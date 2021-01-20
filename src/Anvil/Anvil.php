<?php

namespace Anvil;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\Armor;
use pocketmine\item\Item;
use pocketmine\item\Tool;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;

class Anvil extends PluginBase implements Listener{

    private $config;

    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);

        @mkdir($this->getDataFolder());
        if (!file_exists($this->getDataFolder() . "config.yml")){
            $this->saveResource("config.yml");
        }
        $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        switch ($command->getName()){
            case "anvilreload":
                $this->reloadConfig();
                $sender->sendMessage($this->getConfig()->get("message.reload"));
                break;
        }
        return true;
    }

    public function onTouch(PlayerInteractEvent $event){
        $anvil = $event->getBlock();
        $player = $event->getPlayer();
        $main = $event->getItem();

        if ($anvil->getId() == 145){
            if ($event->getAction() == 1){
                if ($main->getId() == 0){
                    $player->sendMessage($this->getConfig()->get("air.item"));
                }else{
                    $event->setCancelled(true);
                    $this->AnvilUI($player);
                }
            }
        }
    }

    public function AnvilUI($player)
    {
        $api = Server::getInstance()->getPluginManager()->getPlugin("FormAPI");
        $form = $api->createSimpleForm(function (Player $player, int $data = null) {

            $result = $data;
            if ($result === null) {
                return true;
            }
            switch ($result) {

                case "0":
                    $main = $player->getInventory()->getItemInHand();
                    if ($main instanceof Item or $main instanceof Tool or $main instanceof Armor){
                        $this->Rename($player);
                    }else{
                        $player->sendMessage($this->getConfig()->get("air.item"));
                    }
                    break;
                case "1":
                    $main = $player->getInventory()->getItemInHand();
                    if ($main instanceof Tool or $main instanceof Armor){
                        $this->Repair($player);
                    }else{
                        $player->sendMessage($this->getConfig()->get("objet.non.reparable"));
                    }
                    break;
                case "2":
                    if ($this->getConfig()->get("true.or.false") == true){
                        $player->sendMessage($this->getConfig()->get("message"));
                    }
                    break;
            }
        });

        $form->setTitle($this->getConfig()->get("title"));
        $form->setContent($this->getConfig()->get("texte"));
        $form->addButton($this->getConfig()->get("rename"));
        $form->addButton($this->getConfig()->get("repair"));
        $form->addButton($this->getConfig()->get("close"));
        $form->sendToPlayer($player);
        return $form;
    }

    public function Repair($player)
    {
        $api = Server::getInstance()->getPluginManager()->getPlugin("FormAPI");
        $form = $api->createSimpleForm(function (Player $player, int $data = null) {

            $result = $data;
            if ($result === null) {
                return true;
            }
            switch ($result) {

                case "0":
                    $main = $player->getInventory()->getItemInHand();
                    if ($player->getXpLevel() >= $this->getConfig()->get("level.xp.pour.repare")){
                        if ($main->getDamage() == 0){
                            $player->sendMessage($this->getConfig()->get("item.full"));
                        }else{
                            $main->setDamage(0);
                            $player->getInventory()->setItemInHand($main);
                            $player->subtractXpLevels($this->getConfig()->get("level.xp.pour.repare"));
                            $player->sendMessage($this->getConfig()->get("repair.good"));
                        }
                    }else{
                        $player->sendMessage($this->getConfig()->get("no.xp"));
                    }
                    break;
                case "1":
                    $this->AnvilUI($player);
                    break;
            }
        });

        $form->setTitle($this->getConfig()->get("title.repair"));
        $form->setContent($this->getConfig()->get("texte.repair"));
        $form->addButton($this->getConfig()->get("yes.repair"));
        $form->addButton($this->getConfig()->get("no.repair"));
        $form->sendToPlayer($player);
        return $form;
    }

    public function Rename(Player $player)
    {
        $api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $api->createCustomForm(function (Player $player, array $data = null) {
            if ($data === null) {
                return true;
            }
            if ($data[1] !== null){
                if ($player->getXpLevel() >= $this->getConfig()->get("xp.rename")){
                    $name = $data[1];
                    $item = $player->getInventory()->getItemInHand();
                    $item->setCustomName($name);
                    $player->subtractXpLevels($this->getConfig()->get("xp.rename"));
                    $player->getInventory()->setItemInHand($item);
                    $custommessage = $this->getConfig()->get("rename.good");
                    $message = str_replace("{name}", $name, $custommessage);
                    $player->sendMessage($message);
                }else{
                    $player->sendMessage($this->getConfig()->get("no.xp"));
                }
            }else{
                $player->sendMessage($this->getConfig()->get("no.name"));
            }
        });

        $form->setTitle($this->getConfig()->get("title.rename"));
        $form->addLabel($this->getConfig()->get("texte.rename"));
        $form->addInput($this->getConfig()->get("name.rename"));
        $form->sendToPlayer($player);
        return $form;
    }
}