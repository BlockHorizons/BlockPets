<?php

namespace BlockHorizons\BlockPets\commands;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;
use BlockHorizons\BlockPets\Loader;

class SpawnPetCommand extends BaseCommand {

	public function __construct(Loader $loader) {
		parent::__construct($loader, "spawnpet", "Spawn a pet for yourself or other players", "<petType> <name> [player] [size]", ["sp"]);
		$this->setPermission("blockpets.command.spawnpet");
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

		if(count($args) > 4 || count($args) < 2) {
			$sender->sendMessage(TF::RED . "[Usage] " . $this->getUsage());
			return true;
		}

		if(!$this->getLoader()->petExists($args[0])) {
			$sender->sendMessage(TF::RED . "[Warning] That pet doesn't exist.");
			return true;
		}

		if(!$sender->hasPermission("blockpets.pet." . $args[0])) {
			$sender->sendMessage(TF::RED . "[Warning] You don't have permission to spawn that pet.");
			return true;
		}

		$player = $sender;
		if(isset($args[2])) {
			if(($player = $this->getLoader()->getServer()->getPlayer($args[2])) === null) {
				$sender->sendMessage(TF::RED . "[Warning] That player isn't online.");
				return true;
			}
		}

		if(isset($args[3]) && !is_numeric($args[3])) {
			$sender->sendMessage(TF::RED . "[Warning] The pet scale should be numeric.");
			return true;
		}
		$petName = $this->getLoader()->getPet($args[0]);
		$pet = $this->getLoader()->createPet($petName, $player, isset($args[3]) ? $args[3] : null);
		$pet->setNameTag($args[1]);
		$pet->setNameTag($pet->getPetName());
		$pet->spawnToAll();
		return true;
	}
}
