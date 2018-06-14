<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\commands;

use BlockHorizons\BlockPets\Loader;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

class AddPetPointsCommand extends BaseCommand {

	public function __construct(Loader $loader) {
		parent::__construct($loader, "addpetpoints", "Add level points to a pet", "/addpetpoints <petName> [amount] [player]", ["app"]);
		$this->setPermission("blockpets.command.addpetpoints");
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
			if(($player = $loader->getServer()->getPlayer($args[2])) === null) {
				$this->sendWarning($sender, $loader->translate("commands.errors.player.not-found"));
				return true;
			}
			if(($pet = $loader->getPetByName($args[0], $player->getName())) === null) {
				$this->sendWarning($sender, $loader->translate("commands.errors.player.no-pet-other"));
				return true;
			}
			$pet->addPetLevelPoints($amount);
			$sender->sendMessage(TF::GREEN . $loader->translate("commands.addpetpoints.success", [$amount, $pet->getPetName()]));
			return true;
		}

		$pet->addPetLevelPoints($amount);
		$sender->sendMessage(TF::GREEN . $loader->translate("commands.addpetpoints.success", [$amount, $pet->getPetName()]));
		return true;
	}
}
