<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\commands;

use BlockHorizons\BlockPets\Loader;
use BlockHorizons\BlockPets\pets\PetFactory;
use BlockHorizons\BlockPets\sessions\PlayerSession;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class ListPetsCommand extends SessionDependentCommand {

	public const ENTRIES_PER_PAGE = 10;//No. of pets to list per page.

	public function __construct(Loader $loader) {
		parent::__construct($loader, "listpets", "Lists all pets that you own", "/listpets [page=1]", ["lpets", "petslist"]);
		$this->setPermission("blockpets.command.listpets");
	}

	public function onCommand(CommandSender $sender, string $commandLabel, array $args): bool {
		if(!($sender instanceof Player)) {
			$this->sendConsoleError($sender, true);
			return true;
		}

		if(isset($args[0]) && is_numeric($args[0])) {
			$page = (int) $args[0];
		} else {
			$page = 1;
		}

		$pages = array_chunk(PlayerSession::get($sender)->getPets(), self::ENTRIES_PER_PAGE);
		if(empty($pages)) {
			$sender->sendMessage($this->getLoader()->translate("commands.listpets.no-pets-found"));
			return true;
		}

		$index = 0;
		$result = "";

		foreach($pages[$page - 1] ?? $pages[($page = 1) - 1] as $pet) {
			$result .= TextFormat::YELLOW . ++$index . ". " . $pet->getPetName() . TextFormat::GRAY . " (" .  PetFactory::getReadableName($pet::getPetSaveId()) . ")" . TextFormat::EOL;
		}

		$sender->sendMessage(
			TextFormat::GREEN . "--- Your Pets " . TextFormat::YELLOW . $page . "/" . count($pages) . TextFormat::GREEN . " ---" . TextFormat::EOL .
			$result
		);
		return true;
	}
}
