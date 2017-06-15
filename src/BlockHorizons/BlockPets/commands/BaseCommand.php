<?php

namespace BlockHorizons\BlockPets\commands;

use BlockHorizons\BlockPets\Loader;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

abstract class BaseCommand extends Command implements PluginIdentifiableCommand {

	protected $loader;

	public function __construct(Loader $loader, $name, $description = "", $usageMessage = null, array $aliases = []) {
		parent::__construct($name, $description, $usageMessage, $aliases);
		$this->loader = $loader;
		$this->setUsage($usageMessage);
	}

	/**
	 * @param CommandSender $sender
	 */
	public function sendConsoleError(CommandSender $sender) {
		$sender->sendMessage(TF::RED . "[Warning] That command can only be used by players.");
	}

	/**
	 * @return Loader
	 */
	public function getPlugin(): Loader {
		return $this->loader;
	}

	/**
	 * @return Loader
	 */
	public function getLoader(): Loader {
		return $this->loader;
	}

	/**
	 * @param CommandSender $sender
	 */
	public function sendNoPermission(CommandSender $sender) {
		$sender->sendMessage(TF::RED . "[Warning] You don't have permission to use that command.");
	}

	public function generateCustomCommandData(Player $player): array {
		$commandData = parent::generateCustomCommandData($player);
		$commandData["permission"] = $this->getPermission();
		if($this->getName() === "spawnpet") {
			return $commandData;
		}
		$commandData["overloads"]["default"]["input"]["parameters"] = CommandOverloads::getOverloads($this->getName());

		return $commandData;
	}
}
