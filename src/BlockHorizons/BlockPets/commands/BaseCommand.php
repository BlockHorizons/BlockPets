<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\commands;

use BlockHorizons\BlockPets\Loader;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;
use pocketmine\utils\TextFormat as TF;

abstract class BaseCommand extends Command implements PluginOwned {

	public function __construct(protected Loader $loader, string $name, string $description = "", string $usageMessage = "", array $aliases = []) {
		if($usageMessage !== "") {
			$usageMessage = TF::RED . "Usage: " . $usageMessage;
		}
		parent::__construct($name, $description, $usageMessage, $aliases);
	}

	public function sendConsoleError(CommandSender $sender, bool $noConsoleUse = false): void {
		if($noConsoleUse) {
			$this->sendWarning($sender, $this->getLoader()->translate("commands.errors.no-console-use"));
			return;
		}
		$this->sendWarning($sender, $this->getLoader()->translate("commands.errors.console-use"));
	}

	public function sendWarning(CommandSender $sender, string $text): void {
		$sender->sendMessage(TF::RED . $this->getLoader()->translate("prefix.warning") . " " . $text);
	}

	public function sendPermissionMessage(CommandSender $sender): void {
		$this->sendWarning($sender, $this->getLoader()->translate("commands." . $this->getName() . ".no-permission") ?? $this->getLoader()->translate("commands.no-permission"));
	}

	public function getLoader(): Loader {
		return $this->loader;
	}

	public function getPlugin(): Plugin {
		return $this->loader;
	}

	/**
	 * @param string[] $args
	 */
	public final function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!$this->testPermissionSilent($sender)) {
			$this->sendPermissionMessage($sender);
			return;
		}

		if(!$this->onCommand($sender, $commandLabel, $args) && $this->usageMessage !== "") {
			$sender->sendMessage(str_replace("/" . $this->getName(), "/" . $commandLabel, $this->getUsage()));
			return;
		}
	}

	public abstract function onCommand(CommandSender $sender, string $commandLabel, array $args): bool;
}
