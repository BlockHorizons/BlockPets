<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\commands;

use BlockHorizons\BlockPets\Loader;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as TF;

class RemovePetCommand extends BaseCommand {

	public function __construct(Loader $loader) {
		parent::__construct($loader, "removepet", "Remove a pet", "/removepet <petName> [player]", ["rmp"]);
		$this->setPermission("blockpets.command.removepet");
	}

	public function onCommand(CommandSender $sender, string $commandLabel, array $args): bool {
		if(!isset($args[0])) {
			return false;
		}

		$loader = $this->getLoader();
		if(isset($args[1])) {
			if(($player = $loader->getServer()->getPlayer($args[1])) === null) {
				$this->sendWarning($sender, $loader->translate("commands.errors.player.not-found"));
				return true;
			}
			if(($pet = $loader->getPetByName($args[0], $player->getName())) === null) {
				$this->sendWarning($sender, $loader->translate("commands.errors.player.no-pet-other"));
				return true;
			}
			$loader->removePet($pet);
			$sender->sendMessage(TF::GREEN . $loader->translate("commands.removepet.success", [$pet->getPetName()]));
			return true;
		}

		if(($pet = $loader->getPetByName($args[0])) === null) {
			$this->sendWarning($sender, $loader->translate("commands.errors.pet.doesnt-exist"));
			return true;
		}

		$loader->removePet($pet);
		$sender->sendMessage(TF::GREEN . $loader->translate("commands.removepet.success", [$pet->getPetName()]));
		return true;
	}
}
