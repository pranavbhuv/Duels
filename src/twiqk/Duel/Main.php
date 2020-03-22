<?php

declare(strict_types=1);

namespace twiqk\Duel;

use jojoe77777\FormAPI\ModalForm;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

class Main extends PluginBase implements Listener
{
    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getLogger()->notice("Enabled.");
        $this->getLogger()->notice("For more plugins contact twiqk.");
        @mkdir($this->getDataFolder());
        $this->saveDefaultConfig();
    }
    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        global $active;

        switch ($command->getName())
        {
            case "duel":
                if ($sender instanceof Player)
                {
                    if (!isset($args[0]))
                    {
                        $sender->sendMessage(TextFormat::GRAY . "Usage: /duel (player_name)");
                    }
                        if (isset($args[0]))
                        {
                            $fighter = $args[0];
                            $player2 = $this->getServer()->getPlayer($fighter);
                            if ($player2 === null)
                            {
                                $sender->sendMessage(TextFormat::GRAY . "Player doesnt exist, or isnt online.");
                            }
                            else {
                                $this->accepter($player2, $sender);
                            }
                        }
                }
                break;
        }

        return false;
    }

    public function accepter(Player $player, Player $sender)
    {
        global $dn;
        $dn = $sender->getDisplayName();
        $player->sendMessage(TextFormat::GREEN . $dn . TextFormat::GRAY . " has sent you a duel request! Type accept or decline in chat to duel!");
        $api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = new ModalForm(function (Player $player, $data) use ($sender, $dn) {
            var_dump($data);
            if ($data === true)
            {
                {

                    $player->sendPopup(TextFormat::GRAY . "You have accepted " . TextFormat::GREEN . $dn . TextFormat::GRAY . " duel request!");
                    $sender->sendPopup(TextFormat::GRAY . "Your duel request has been accepted.");
                    $this->arena($player, $sender);
                }
            }
            else
                {
                    $player->sendPopup(TextFormat::RED . "You have denied the duel request!");
                    $sender->sendPopup(TextFormat::RED . "Your duel was denied.");
                    return false;
                }
            });
        $form->setTitle("Duels");
        $form->setContent(TextFormat::GREEN . $dn . TextFormat::GRAY . " has sent you a duel request!\nAccept or Decline?");
        $form->setButton1("Accept");
        $form->setButton2("Decline");
        $player->sendForm($form);
    }

    public function arena(Player $player, Player $sender)
    {
        $x = $this->getConfig()->get("x");
        $y = $this->getConfig()->get("y");
        $z = $this->getConfig()->get("z");
        $str = $this->getConfig()->get("level");
        $lvl = $this->getServer()->getLevelByName((string)$str);
        $sender->teleport(new Position($x, $y, $z, $lvl));
        $player->teleport(new Position($x, $y, $z, $lvl));
        $player->setHealth(20);
        $player->setFood(20);
        $sender->setHealth(20);
        $sender->setFood(20);
        $sender->sendMessage(TextFormat::RED . "Duels have begun!");
        $player->sendMessage(TextFormat::RED . "Duels have begun!");
    }

}
