<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\commands;

use BlockHorizons\BlockPets\commands\utils\BlockPetsCommandException;
use BlockHorizons\BlockPets\Loader;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat as TF;

abstract class BaseCommand extends Command implements PluginIdentifiableCommand {

	/** @var Loader */
	protected $loader;

	public function __construct(Loader $loader, string $name, string $description = "", string $usageMessage = "", array $aliases = []) {
		if($usageMessage !== "") {
			$usageMessage = TF::RED . "Usage: " . $usageMessage;
		}

		parent::__construct($name, $description, $usageMessage, $aliases);
		$this->loader = $loader;
	}

	/**
	 * @param CommandSender $sender
	 */
	public function sendConsoleError(CommandSender $sender, bool $noConsoleUse = false): void {
		if($noConsoleUse) {
			$this->sendWarning($sender, $this->getLoader()->translate("commands.errors.no-console-use"));
			return;
		}
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
	 * @param CommandSender $sender
	 */
	public function sendPermissionMessage(CommandSender $sender): void {
		$this->sendWarning($sender, $this->getLoader()->translate("commands." . $this->getName() . ".no-permission") ?? $this->getLoader()->translate("commands.no-permission"));
	}

	/**
	 * @return Loader
	 */
	public function getLoader(): Loader {
		return $this->loader;
	}

	/**
	 * @return Loader
	 */
	public function getPlugin(): Plugin {
		return $this->loader;
	}

	public function testCommandSenderValidity(CommandSender $sender): void {
	}

	/**
	 * @param CommandSender $sender
	 * @param string        $commandLabel
	 * @param string[]      $args
	 *
	 * @return mixed
	 */
	public final function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!$this->testPermissionSilent($sender)) {
			$this->sendPermissionMessage($sender);
			return;
		}

		try {
			$this->testCommandSenderValidity($sender);
			if(!$this->onCommand($sender, $commandLabel, $args) && $this->usageMessage !== "") {
				$sender->sendMessage(str_replace("/" . $this->getName(), "/" . $commandLabel, $this->getUsage()));
				return;
			}
		} catch (BlockPetsCommandException $e) {
			$this->sendWarning($sender, $e->getMessage());
		}
	}

	public abstract function onCommand(CommandSender $sender, string $commandLabel, array $args): bool;
}
