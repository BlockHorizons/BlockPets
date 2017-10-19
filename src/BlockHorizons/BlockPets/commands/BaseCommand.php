<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\commands;

use BlockHorizons\BlockPets\Loader;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat as TF;

abstract class BaseCommand extends Command implements PluginIdentifiableCommand {

	protected $loader;

	public function __construct(Loader $loader, string $name, string $description = "", string $usageMessage = "", array $aliases = []) {
		parent::__construct($name, $description, $usageMessage, $aliases);
		$this->loader = $loader;
		$this->setUsage($usageMessage);
	}

	/**
	 * @param CommandSender $sender
	 */
	public function sendConsoleError(CommandSender $sender): void {
		$this->sendWarning($sender, $this->getLoader()->translate("commands.errors.console-use"));
	}

	/**
	 * @param CommandSender $sender
	 * @param string        $text
	 */
	public function sendWarning(CommandSender $sender, string $text): void {
		$sender->sendMessage(TF::RED . $this->getLoader()->translate("prefix.warning") . " " . $text);
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
	public function sendPermissionMessage(CommandSender $sender): void {
		$this->sendWarning($sender, $this->getLoader()->translate("commands." . $this->getName() . ".no-permission") ?? $this->getLoader()->translate("commands.no-permission"));
	}

	/**
	 * @return Loader
	 */
	public function getPlugin(): Plugin {
		return $this->loader;
	}
}
