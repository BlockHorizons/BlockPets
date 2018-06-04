<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\commands;

use BlockHorizons\BlockPets\Loader;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\utils\TextFormat;

class PetCommand extends BaseCommand {

	const HELP_MESSAGE_TITLE = TextFormat::GREEN . "--- BlockPets Help Page " . TextFormat::YELLOW . "{CURRENT_PAGE}/{TOTAL_PAGES}" . TextFormat::GREEN . " ---";

	const HELP_MESSAGES = [
		"/spawnpet <petType> [petName|select] [size] [isBaby] [player]: Spawns a new pet with the given data.",
		"/removepet <name> [player]: Removes the first pet with the given name, and checks for a player if given.",
		"/togglepet <all/pet name> [player]: Toggles a pet on/off, depending on the current state.",
		"/healpet <name> [player]: Heals the first pet with the given name, and checks for a player if given.",
		"/leveluppet <name> [amount] [player]: Levels up the first pet with the given name by the amount, and checks for player if given.",
		"/clearpet <name>: Clears one of your own pets with the given name.",
		"/changepetname <old name> <new name> [player]: Changes the name of one of your pets, or the pet of other players if specified.",
		"/listpets [EntityName] [page=1]: Lists all your pets.",
		"/petstop [page=1]: Displays pets leaderboard."
	];

	const HELP_MESSAGES_PER_PAGE = 4;//When executed in console, all help messages are displayed irrespective of the page specified.

	/** @var string[][] */
	private $help_messages = [];

	public function __construct(Loader $loader) {
		parent::__construct($loader, "pet", "Show info or reload BlockPets", "/pet [help|info|reload]", ["pets"]);
		$this->setPermission("blockpets.command.pet");
		$this->formatHelpMessages();
	}

	private function formatHelpMessages(): void {
		$messages = [];
		$sorted_messages = self::HELP_MESSAGES;
		sort($sorted_messages, SORT_STRING);

		foreach($sorted_messages as $message) {
			[$command_part, $description] = explode(":", $message, 2);
			[$command, $args_string] = explode(" ", $command_part, 2);

			$search_for = null;
			$starting_offset = NAN;
			$is_optional = null;
			$args = [];

			foreach(str_split($args_string) as $offset => $char) {
				if($search_for !== null) {
					if($char === $search_for) {
						$arg = substr($args_string, $starting_offset, $offset - $starting_offset + 1);
						if($is_optional) {
							$arg = TextFormat::GRAY . $arg;
						} else {
							$arg = TextFormat::RED . $arg;
						}
						$args[] = $arg;

						$starting_offset = -1;
						$search_for = null;
						$is_optional = null;
					}
				} else {
					switch($char) {
						case "<":
							$search_for = ">";
							$starting_offset = $offset;
							$is_optional = false;
							break;
						case "[":
							$search_for = "]";
							$starting_offset = $offset;
							$is_optional = true;
							break;
					}
				}
			}

			$messages[substr($command, 1)] = TextFormat::GREEN . $command . " " . implode(" ", $args) . TextFormat::YELLOW . ":" . $description;
		}

		$this->help_messages = array_chunk($messages, self::HELP_MESSAGES_PER_PAGE, true);
	}

	public function onCommand(CommandSender $sender, string $commandLabel, array $args): bool {
		if(!isset($args[0])) {
			$args[0] = "help";
		}

		switch(strtolower($args[0])) {
			case "help":
				if($sender instanceof ConsoleCommandSender) {
					$message = "";
					foreach($this->help_messages as $messages) {
						foreach($messages as $msg) {
							$message .= $msg . TextFormat::EOL;
						}
					}

					$sender->sendMessage(strtr(self::HELP_MESSAGE_TITLE, [
						"{CURRENT_PAGE}" => 1,
						"{TOTAL_PAGES}" => 1
					]) . TextFormat::EOL . $message);
					return true;
				}

				if(!isset($args[1]) || !is_numeric($args[1])) {
					$args[1] = 1;
				}

				$page = (int) $args[1];
				if(!isset($this->help_messages[$page - 1])) {
					$page = 1;
				}

				$sender->sendMessage(strtr(self::HELP_MESSAGE_TITLE, [
					"{CURRENT_PAGE}" => $page,
					"{TOTAL_PAGES}" => count($this->help_messages)
				]) . TextFormat::EOL . implode(TextFormat::EOL, $this->help_messages[$page - 1]));
				break;
			case "reload":
				$sender->sendMessage(TextFormat::GREEN . "Reloading...");
				$this->getLoader()->onEnable();
				$sender->sendMessage(TextFormat::GREEN . "Reload complete.");
				break;
			default:
			case "info":
				$sender->sendMessage(TextFormat::AQUA . "[BlockPets] Information\n" .
					TextFormat::GREEN . "Version: " . TextFormat::YELLOW . $loader->getDescription()->getVersion() . "\n" .
					TextFormat::GREEN . "Target API: " . TextFormat::YELLOW . implode(", ", $loader->getDescription()->getCompatibleApis()) . "\n" .
					TextFormat::GREEN . "Organization: " . TextFormat::YELLOW . "BlockHorizons (https://github.com/BlockHorizons/BlockPets)\n" .
					TextFormat::GREEN . "Authors: " . TextFormat::YELLOW . "Sandertv (@Sandertv), TheDiamondYT (@TheDiamondYT1)");
				break;
		}
		return true;
	}
}
