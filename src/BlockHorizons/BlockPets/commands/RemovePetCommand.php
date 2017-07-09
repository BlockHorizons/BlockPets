<?php

namespace BlockHorizons\BlockPets\commands;

use BlockHorizons\BlockPets\Loader;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as TF;

class RemovePetCommand extends BaseCommand {

	public function __construct(Loader $loader) {
		parent::__construct($loader, "removepet", "Remove a pet", "/removepet <petName> [player]", ["rmp"]);
		$this->setPermission("blockpets.command.removepet");
	}

	public function execute(CommandSender $sender, $commandLabel, array $args): bool {
		if(!$this->testPermission($sender)) {
			$this->sendPermissionMessage($sender);
			return true;
		}

		if(($pet = $this->getLoader()->getPetByName($args[0])) === null) {
			$this->sendWarning($sender, $this->getLoader()->translate("commands.errors.pet.doesnt-exist"));
			return true;
		}
		if(isset($args[1])) {
			if(($player = $this->getLoader()->getServer()->getPlayer($args[1])) === null) {
				$this->sendWarning($sender, $this->getLoader()->translate("commands.errors.player.not-found"));
				return true;
			}
			if(($pet = $this->getLoader()->getPetByName($args[0], $player)) === null) {
				$this->sendWarning($sender, $this->getLoader()->translate("commands.errors.player.no-pet-other"));
				return true;
			}
			if($this->getLoader()->removePet($pet->getPetName(), $player) === false) {
				$this->sendWarning($sender, $this->getLoader()->translate("commands.errors.plugin-cancelled"));
				return true;
			}
			$sender->sendMessage(TF::GREEN . $this->getLoader()->translate("commands.removepet.success", [$pet->getPetName()]));
			return true;
		}

		if($this->getLoader()->removePet($args[0])) {
			$sender->sendMessage(TF::GREEN . $this->getLoader()->translate("commands.removepet.success", [$pet->getPetName()]));
		} else {
			$this->sendWarning($sender, $this->getLoader()->translate("commands.errors.plugin-cancelled"));
		}
		return true;
	}
}
