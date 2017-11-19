<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\commands;

use BlockHorizons\BlockPets\Loader;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

class SpawnPetCommand extends BaseCommand {

	public function __construct(Loader $loader) {
		parent::__construct($loader, "spawnpet", "Spawn a pet for yourself or other players", "/spawnpet <petType> [name] [size] [baby] [player]", ["sp"]);
		$this->setPermission("blockpets.command.spawnpet.use");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
		if(!$this->testPermission($sender)) {
			$this->sendPermissionMessage($sender);
			return true;
		}

		if(!$sender instanceof Player && count($args) != 5) {
			$this->sendConsoleError($sender);
			//$sender->sendMessage(TF::RED . "When using spawnpet from the console, all arguments must be provided.");
			$sender->sendMessage(TF::RED . "[Usage] " . $this->getUsage());
			return true;
		}

		if($sender instanceof Player){

			if(count($args) > 5 || count($args) < 1){
				$sender->sendMessage(TF::RED . "[Usage] " . $this->getUsage());
				return true;
			}

			if(!$sender->hasPermission("blockpets.pet." . strtolower($args[0]))){
				$this->sendPermissionMessage($sender);
				return true;
			}
		}

		$player = $sender;
		if(isset($args[4])){
			if(($player = $this->getLoader()->getServer()->getPlayer($args[4])) === null){
				$this->sendWarning($sender, $this->getLoader()->translate("commands.errors.player.not-found"));
				return true;
			}
			if(!$sender->hasPermission("blockpets.command.spawnpet.others")){
				$this->sendWarning($sender, $this->getLoader()->translate("commands.spawnpet.no-permission.others"));
				return true;
			}
		}

		if(empty(trim($args[1]))){
			$args[1] = $player->getDisplayName();
		}

		if(isset($args[2])) {
			if(!is_numeric($args[2])) {
				$this->sendWarning($sender, $this->getLoader()->translate("commands.errors.pet.numeric"));
				return true;
			}
		}

		if(isset($args[3])) {
			if($args[3] === "false" || $args === "no" ) {
				$args[3] = false;
			} else {
				$args[3] = true;
			}
		} else {
			$args[3] = false;
		}
		$petName = $this->getLoader()->getPet($args[0]);
		if($petName === null) {
			$this->sendWarning($sender, $this->getLoader()->translate("commands.errors.pet.doesnt-exist", [$args[0]]));
			return true;
		}
		if(count($this->getLoader()->getPetsFrom($player)) >= $this->getLoader()->getBlockPetsConfig()->getMaxPets() && !$player->hasPermission("blockpets.bypass-limit")) {
			$sender->sendMessage($sender, $this->getLoader()->translate("commands.spawnpet.exceeded-limit", [
				$player === $sender ? "You have " : "Your target has "
			]));
			return true;
		}
		if(!isset($args[1]) || strtolower($args[1]) === "select") {
			if($player !== $sender) {
				$sender->sendMessage(TF::GREEN . $this->getLoader()->translate("commands.spawnpet.selecting-name", [$player->getName()]));
			}
			$player->sendMessage(TF::GREEN . $this->getLoader()->translate("commands.spawnpet.name"));
			$this->getLoader()->selectingName[$player->getName()] = [
				"petType" => $petName,
				"scale" => isset($args[2]) ? (float) $args[2] : 1.0,
				"isBaby" => isset($args[3]) ? $args[3] : false
			];
			return true;
		}
		if($this->getLoader()->getPetByName($args[1], $player) !== null) {
			$this->sendWarning($sender, $this->getLoader()->translate("commands.errors.player.already-own-pet"));
			return true;
		}
		if($this->getLoader()->createPet((string) $petName, $player, $args[1], isset($args[2]) ? (float) $args[2] : 1.0, $args[3]) === null) {
			$this->sendWarning($sender, $this->getLoader()->translate("commands.errors.plugin-cancelled"));
			return true;
		}
		$sender->sendMessage(TF::GREEN . $this->getLoader()->translate("commands.spawnpet.success", [$args[1]]));
		if($player->getName() !== $sender->getName()) {
			$player->sendMessage(TF::GREEN . $this->getLoader()->translate("commands.spawnpet.success.other", [$args[1]]));
		}
		return true;
	}
}
