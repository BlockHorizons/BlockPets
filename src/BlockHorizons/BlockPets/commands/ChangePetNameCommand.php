<?php

namespace BlockHorizons\BlockPets\commands;

use BlockHorizons\BlockPets\Loader;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

class ChangePetNameCommand extends BaseCommand {

	public function __construct(Loader $loader) {
		parent::__construct($loader, "changepetname", "Changes the name of a pet", "/changepetname <pet name> <new name> [player]", ["cpn, chpn"]);
		$this->setPermission("blockpets.command.changepetname.use");
	}

	public function execute(CommandSender $sender, $commandLabel, array $args) {
		if(!$this->testPermission($sender)) {
			$this->sendNoPermission($sender);
			return true;
		}

		if(!$sender instanceof Player) {
			$this->sendConsoleError($sender);
			return true;
		}
		$newName = $args[1];

		if(isset($args[2])) {
			if($sender->hasPermission("blockpets.command.changepetname.others")) {
				$sender->sendMessage(TF::RED . "[Warning] You don't have permission to change the name of pets from other players.");
				return true;
			}
			if(($player = $this->getLoader()->getServer()->getPlayer($args[2])) === null) {
				$sender->sendMessage(TF::RED . "[Warning] The given player could not be found.");
				return true;
			}
			if(($pet = $this->getLoader()->getPetByName($args[0], $player)) === null) {
				$sender->sendMessage(TF::RED . "[Warning] The given player does not own a pet with that name.");
				return true;
			}
			$oldName = $pet->getPetName();
			$pet->changeName($newName);
			$sender->sendMessage(TF::GREEN . "Successfully changed the name of " . $oldName . TF::RESET . TF::GREEN . "  to " . $newName);
			return true;
		} else {
			if(($pet = $this->getLoader()->getPetByName($args[0], $sender)) === null) {
				$sender->sendMessage(TF::RED . "[Warning] You don't own a pet with the given name");
				return true;
			}
		}

		$oldName = $pet->getPetName();
		$pet->changeName($newName);
		$sender->sendMessage(TF::GREEN . "Successfully changed the name of " . $oldName . TF::RESET . TF::GREEN . "  to " . $newName);
		return true;
	}
}