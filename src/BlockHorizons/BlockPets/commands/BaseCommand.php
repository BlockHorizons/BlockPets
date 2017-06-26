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
		$this->sendWarning($sender, $this->getLoader()->translate("commands.errors.console-use"));
	}
	
	/**
	 * @param CommandSender $sender
	 */
	public function sendPermissionMessage(CommandSender $sender) {
	    $this->sendWarning($sender, $this->getLoader()->translate("commands." . $this->getName() . ".no-permission") ?? $this->getLoader()->translate("commands.no-permission"));
	}
	
	/**
	 * @param CommandSender $sender
	 * @param string        $text
	 */
	public function sendWarning(CommandSender $sender, string $text) {
	    $sender->sendMessage(TF::RED . $this->getLoader()->translate("prefix.warning") . " " . $text);
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

	public function generateCustomCommandData(Player $player): array {
		$commandData = parent::generateCustomCommandData($player);
		$commandData["permission"] = $this->getPermission();
		$commandData["overloads"]["default"]["input"]["parameters"] = CommandOverloads::getOverloads($this->getName());

		return $commandData;
	}
}
