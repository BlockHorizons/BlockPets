<?php

namespace BlockHorizons\BlockPets\commands;

use BlockHorizons\BlockPets\Loader;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

class LevelUpPetCommand extends BaseCommand {

	public function __construct(Loader $loader) {
		parent::__construct($loader, "leveluppet", "Level up a pet", "/leveluppet <petName> [amount] [player]", ["lup"]);
		$this->setPermission("blockpets.command.leveluppet");
	}

	public function execute(CommandSender $sender, $commandLabel, array $args): bool {
		if(!$this->testPermission($sender)) {
			$this->sendPermissionMessage($sender);
			return true;
		}

		if(!$sender instanceof Player) {
			$this->sendConsoleError($sender);
			return true;
		}

		if(($pet = $this->getLoader()->getPetByName($args[0])) === null) {
			$this->sendWarning($sender, $this->getLoader()->translate("commands.errors.pet.doesnt-exist"));
			return true;
		}

		$amount = 1;
		if(isset($args[1])) {
			if(is_numeric($args[1])) {
				$amount = $args[1];
			}
		}

		if(isset($args[2])) {
			if(($player = $this->getLoader()->getServer()->getPlayer($args[2])) === null) {
				$this->sendWarning($sender, $this->getLoader()->translate("commands.errors.player.not-found"));
				return true;
			}
			if(($pet = $this->getLoader()->getPetByName($args[0], $player)) === null) {
				$this->sendWarning($sender, $this->getLoader()->translate("commands.errors.player.no-pet-other"));
				return true;
			}
			$pet->levelUp($amount);
			$sender->sendMessage(TF::GREEN . "Successfully leveled up the pet: " . TF::AQUA . $pet->getPetName() . TF::GREEN . ($amount === 1 ? " once!" : " " . $amount . " times!"));
			return true;
		}

		$pet->levelUp($amount);
		$sender->sendMessage(TF::GREEN . "Successfully leveled up the pet: " . TF::AQUA . $pet->getPetName() . TF::GREEN . ($amount === 1 ? " once!" : " " . $amount . " times!"));
		return true;
	}
}
