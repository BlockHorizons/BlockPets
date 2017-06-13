<?php

namespace BlockHorizons\BlockPets\commands;


use BlockHorizons\BlockPets\Loader;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class PetCommand extends BaseCommand {

	public function __construct(Loader $loader) {
		parent::__construct($loader, "pet", "Show info or reload BlockPets", "/pet [help|info|reload]", []);
		$this->setPermission("blockpets.command.pet");
	}

	public function execute(CommandSender $sender, $commandLabel, array $args): bool {
		if(!$this->testPermission($sender)) {
			$this->sendNoPermission($sender);
			return true;
		}

		if(!isset($args[0])) {
			$args[0] = "help";
		}

		switch(strtolower($args[0])) {
			case "help":
				if(!isset($args[1])) {
					$args[1] = 1;
				}
				if(!is_numeric($args[1])) {
					$args[1] = 1;
				}
				if($args[1] > 2) {
					$args[1] = 1;
				}
				$sender->sendMessage(TextFormat::GREEN . "--- BlockPets Help Page " . TextFormat::YELLOW . $args[1] . TextFormat::GREEN . " ---");
				switch($args[1]) {
					case 1:
						$sender->sendMessage(
							TextFormat::GREEN . "/spawnpet <petType> [petName|select] [size] [isBaby] [player]: " . TextFormat::YELLOW . "Spawns a new pet with the given data." . PHP_EOL .
							TextFormat::GREEN . "/removepet <name> [player]: " . TextFormat::YELLOW . "Removes the first pet with the given name, and checks for a player if given." . PHP_EOL .
							TextFormat::GREEN . "/togglepet [all/name]: " . TextFormat::YELLOW . "Toggles your pets on/off, depending on the current state." . PHP_EOL .
							TextFormat::GREEN . "/healpet <name> [player]: " . TextFormat::YELLOW . "Heals the first pet with the given name, and checks for a player if given."
						);
						break;

					case 2:
						$sender->sendMessage(
							TextFormat::GREEN . "/leveluppet <name> [amount] [player]: " . TextFormat::YELLOW . "Levels up the first pet with the given name by the amount, and checks for player if given." . PHP_EOL .
							TextFormat::GREEN . "/clearpet <name>: " . TextFormat::YELLOW . "Clears one of your own pets with the given name."
						);
				}
				break;

			default:
			case "info":
				$sender->sendMessage(TextFormat::AQUA . "[BlockPets] Information\n" .
					TextFormat::GREEN . "Version: " . TextFormat::YELLOW . Loader::VERSION . "\n" .
					TextFormat::GREEN . "Target API: " . TextFormat::YELLOW . Loader::API_TARGET . "\n" .
					TextFormat::GREEN . "Organization: " . TextFormat::YELLOW . "BlockHorizons (https://github.com/BlockHorizons/BlockPets)\n" .
					TextFormat::GREEN . "Authors: " . TextFormat::YELLOW . "Sandertv (@Sandertv)");
				break;

			case "reload":
				$sender->sendMessage(TextFormat::GREEN . "Reloading...");
				$this->getLoader()->onEnable();
				$sender->sendMessage(TextFormat::GREEN . "Reload complete.");
				break;
		}
		return true;
	}
}