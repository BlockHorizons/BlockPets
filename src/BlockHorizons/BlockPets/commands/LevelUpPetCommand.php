<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\commands;

use BlockHorizons\BlockPets\Loader;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as TF;

class LevelUpPetCommand extends BaseCommand {

	public function __construct(Loader $loader) {
		parent::__construct($loader, "leveluppet", "Level up a pet", "/leveluppet <petName> [amount] [player]", ["lup"]);
		$this->setPermission("blockpets.command.leveluppet");
	}

	public function onCommand(CommandSender $sender, string $commandLabel, array $args): bool {
		if(!isset($args[0])) {
			return false;
		}

		$loader = $this->getLoader();
		if(($pet = $loader->getPetByName($args[0])) === null) {
			$this->sendWarning($sender, $loader->translate("commands.errors.pet.doesnt-exist"));
			return true;
		}

		$amount = 1;
		if(isset($args[1])) {
			if(is_numeric($args[1])) {
				$amount = (int) $args[1];
			}
		}

		if(isset($args[2])) {
			if(($player = $loader->getServer()->getPlayerByPrefix($args[2])) === null) {
				$this->sendWarning($sender, $loader->translate("commands.errors.player.not-found"));
				return true;
			}
			if(($pet = $loader->getPetByName($args[0], $player->getName())) === null) {
				$this->sendWarning($sender, $loader->translate("commands.errors.player.no-pet-other"));
				return true;
			}
			$pet->levelUp($amount);
			$sender->sendMessage(TF::GREEN . $loader->translate("commands.leveluppet.success", [$pet->getPetName(), ($amount === 1 ? " once" : " " . $amount . " times")]));
			return true;
		}

		$pet->levelUp($amount);
		$sender->sendMessage(TF::GREEN . $loader->translate("commands.leveluppet.success", [$pet->getPetName(), ($amount === 1 ? " once" : " " . $amount . " times")]));
		return true;
	}
}
