<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\commands;

use BlockHorizons\BlockPets\commands\utils\BlockPetsCommandException;
use BlockHorizons\BlockPets\Loader;
use BlockHorizons\BlockPets\pets\BasePet;
use BlockHorizons\BlockPets\sessions\PlayerSession;
use BlockHorizons\BlockPets\sessions\PlayerSessionUtils;
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

	public function getOnlinePlayerSession(string $playerName, &$player = null): PlayerSession {
		$player = $this->loader->getServer()->getPlayer($playerName);
		if($player === null) {
			throw new BlockPetsCommandException($this->getLoader()->translate("commands.errors.player.not-found"));
		}

		return $this->getPlayerSession($player);
	}

	public function getPlayerSession(Player $player): PlayerSession {
		$session = PlayerSession::get($player);
		if($session === null) {
			throw new BlockPetsCommandException($this->getLoader()->translate("commands.errors.database.player-not-loaded-other", [$player->getName()]));
		}

		return $session;
	}

	public function getPetByName(string $petName, CommandSender $sender, &$session = null): BasePet {
		if($sender instanceof Player) {
			$pet = ($session = $this->getPlayerSession($sender))->getPetByName($petName);
			if($pet !== null) {
				return $pet;
			}
		}

		$pet = PlayerSessionUtils::getPetByName($petName);
		if($pet === null) {
			throw new BlockPetsCommandException($this->getLoader()->translate("commands.errors.player.no-pet"));
		}

		return $pet;
	}

	public function getPlayerPet(string $ownerName, string $petName, &$owner = null, &$session = null): BasePet {
		$pet = ($session = $this->getPlayerSession($ownerName, $owner))->getPetByName($petName);
		if($pet === null) {
			throw new BlockPetsCommandException($this->getLoader()->translate("commands.errors.player.no-pet-other"));
		}

		return $pet;
	}

	public function testCommandSenderValidity(CommandSender $sender): void {
		if($sender instanceof Player && PlayerSession::get($sender) === null) {
			throw new BlockPetsCommandException($this->loader->translate("commands.errors.database.player-not-loaded"));
		}
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
