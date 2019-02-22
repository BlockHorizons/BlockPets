<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\commands;

use BlockHorizons\BlockPets\Loader;
use BlockHorizons\BlockPets\pets\PetFactory;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class ListPetsCommand extends BaseCommand {

	public const ENTRIES_PER_PAGE = 10;//No. of pets to list per page.

	public function __construct(Loader $loader) {
		parent::__construct($loader, "listpets", "Lists all pets that you own", "/listpets [type=ANY] [page=1]", ["lpets", "petslist"]);
		$this->setPermission("blockpets.command.listpets");
	}

	public function onCommand(CommandSender $sender, string $commandLabel, array $args): bool {
		if(!($sender instanceof Player)) {
			$this->sendConsoleError($sender, true);
			return true;
		}

		if(isset($args[0])) {
			if(isset($args[1])) {
				[$list_type, $page] = $args;
			} else {
				if(is_numeric($args[0])) {
					$page = $args[0];
				} else {
					$list_type = $args[0];
				}
			}
		}

		$loader = $this->getLoader();

		if(isset($list_type)) {
			$list_type = PetFactory::getKnownPetId($list_type);
			if($list_type === null) {
				$sender->sendMessage($loader->translate("commands.errors.pet.doesnt-exist"));
				return true;
			}
		} else {
			$list_type = null;
		}

		if(isset($page)) {
			$page = max(1, (int) $page);
		} else {
			$page = 1;
		}

		$loader->getDatabase()->getPlayerPets(
			$sender->getName(),
			$list_type,
			function(array $rows) use($loader, $list_type, $page, $sender): void {
				$pets = [];
				$pets_c = 0;

				foreach($rows as ["PetName" => $petName, "EntityName" => $entityName, "Visible" => $isVisible]) {
					$row = TextFormat::YELLOW . ++$pets_c . ". " . $petName . ($list_type === null ? TextFormat::GRAY . " (" . PetFactory::getReadableName($entityName) . ") " : "");
					if(!$isVisible) {
						$row .= TextFormat::GRAY . "[INVISIBLE]";
					}
					$pets[] = $row;
				}

				if(empty($pets)) {
					if($list_type === null) {
						$sender->sendMessage($loader->translate("commands.listpets.no-pets-found"));
					} else {
						$sender->sendMessage($loader->translate("commands.listpets.no-pets-found-type", [$list_type]));
					}
				} else {
					$pages = (int) ceil($pets_c / self::ENTRIES_PER_PAGE);
					if($page > $pages) {
						$page = 1;
					}

					$message = TextFormat::GREEN . "--- Your " . ($list_type ?? "Pet") . "s " . TextFormat::YELLOW . $page . "/" . $pages . TextFormat::GREEN . " ---" . TextFormat::EOL;
					$message .= implode(TextFormat::EOL, array_slice($pets, 0, self::ENTRIES_PER_PAGE));
					$sender->sendMessage($message);
				}
			}
		);
		return true;
	}
}
