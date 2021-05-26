<?php

namespace Anvil;

use jojoe77777\FormAPI\CustomForm;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\item\Armor;
use pocketmine\item\Tool;
use pocketmine\Player;
use pocketmine\utils\Config;

class AnvilUI
{
    public static function formMain($player)
    {
        $config = new Config(AnvilMain::getInstance()->getDataFolder()."config.yml", Config::YAML);
        $form = new SimpleForm(function (Player $player, int $data = null) use ($config){
            $result = $data;
            if ($result === null){
                return false;
            }
            switch ($result){
                case 0:
                    if ($player->getInventory()->getItemInHand()->getId() != 0){
                        self::rename($player);
                    }else $player->sendMessage($config->get("noitem"));
                    break;
                case 1:
                    $item = $player->getInventory()->getItemInHand();
                    if ($item instanceof Tool or $item instanceof Armor){
                        if ($item->getDamage() != 0){
                            self::repair($player);
                        }else $player->sendMessage($config->get("norepairmeta"));
                    }else $player->sendMessage($config->get("norepair"));
                    break;
            }
        });
        $form->setTitle($config->getNested("mainform.title"));
        $form->setContent($config->getNested("mainform.content"));
        $form->addButton($config->getNested("mainform.rename"));
        $form->addButton($config->getNested("mainform.repair"));
        $form->sendToPlayer($player);
        return $form;
    }

    public static function rename($player)
    {
        $config = new Config(AnvilMain::getInstance()->getDataFolder()."config.yml", Config::YAML);
        $form = new CustomForm(function (Player $player, array $data = null) use ($config) {
            if ($data === null) {
                return true;
            }
            if ($data[1] === null) {
                $player->sendMessage($config->get("renamenull"));
            }else{
                if ($player->getXpLevel() >= $config->get("xprename")){
                    $item = $player->getInventory()->getItemInHand();
                    $item->setCustomName($data[1]);
                    $player->getInventory()->setItemInHand($item);
                    $player->subtractXpLevels($config->get("xprename"));
                    $player->sendMessage(str_replace(strtolower("{name}"), "$data[1]", $config->get("renamegood")));
                }else $player->sendMessage($config->get("noxprename"));
            }
        });
        $form->setTitle($config->getNested("renameform.title"));
        $form->addLabel($config->getNested("renameform.content"));
        $form->addInput($config->getNested("renameform.name"));
        $form->sendToPlayer($player);
        return $form;
    }

    public static function repair($player){
        $config = new Config(AnvilMain::getInstance()->getDataFolder()."config.yml", Config::YAML);
        $form = new SimpleForm(function (Player $player, int $data = null) use ($config){
            $result = $data;
            if ($result === null){
                return false;
            }
            switch ($result){
                case 0:
                    if ($player->getXpLevel() >= $config->get("xprepair")){
                        $item = $player->getInventory()->getItemInHand();
                        $item->setDamage(0);
                        if ($item->getNamedTag()->offsetExists("Durabilité")) $item->getNamedTag()->setString("Durabilité", $item->getMaxDurability());
                        $player->getInventory()->setItemInHand($item);
                        $player->subtractXpLevels($config->get("xprepair"));
                        $player->sendMessage($config->get("repairgood"));
                    }else $player->sendMessage($config->get("noxprepair"));
                    break;
                case 1:
                    self::formMain($player);
                    break;
            }
        });
        $form->setTitle($config->getNested("repairform.title"));
        $form->setContent($config->getNested("repairform.content"));
        $form->addButton($config->getNested("repairform.yes"));
        $form->addButton($config->getNested("repairform.no"));
        $form->sendToPlayer($player);
        return $form;
    }
}