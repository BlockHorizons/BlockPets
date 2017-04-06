<?php

namespace Sandertv\BlockSniper\commands;

use BlockHorizons\BlockPets\pets\BasePet;
use pocketmine\command\CommandSender;
use pocketmine\nbt\tag\IntTag;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;
use BlockHorizons\BlockPets\Loader;
use BlockHorizons\BlockPets\commands\BaseCommand;

class SpawnPetCommand extends BaseCommand {

	public function __construct(Loader $loader) {
		parent::__construct($loader, "spawnpet", "Spawn a pet for yourself or other players", "<petType> [player] [size]", ["sp"]);
		$this->setPermission("blockpets.command.spawnpet");
	}

	public function execute(CommandSender $sender, $commandLabel, array $args) {
		if(!$this->testPermission($sender)) {
			$this->sendNoPermission($sender);
		}

		if(!$sender instanceof Player) {
			$this->sendConsoleError($sender);
			return true;
		}

		if(count($args) > 3) {
			$sender->sendMessage(TF::RED . "[Usage] " . $this->getUsage());
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
		if(isset($args[1])) {
			if(($player = $this->getLoader()->getServer()->getPlayer($args[1])) === null) {
				$sender->sendMessage(TF::RED . "[Warning] That player isn't online.");
				return true;
			}
		}

		if(isset($args[2]) && !is_numeric($args[2])) {
			$sender->sendMessage(TF::RED . "[Warning] The pet scale should be numeric.");
			return true;
		}
		$petName = $this->getLoader()->getPet($args[0]);
		$pet = $this->getLoader()->createPet($petName, $player, isset($args[2]) ? $args[2] : null);
		$pet->spawnToAll();
		return true;
	}
}
