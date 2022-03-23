<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\datastorage;

use BlockHorizons\BlockPets\pets\BasePet;
use function base64_decode;
use function base64_encode;

class SQLiteDataStorer extends SQLDataStorer {

	public function load(string $player, callable $callable): void {
		$this->database->executeSelect(SQLDataStorer::LOAD_PLAYER_PETS, [
			"player" => $player
		], function(array $rows) use($callable): void {
			foreach($rows as &$row) {
				if(isset($row["Inventory"])) {
					$row["Inventory"] = base64_decode($row["Inventory"]);
				}
			}
			$callable($rows);
		});
	}

	public function updateInventory(BasePet $pet): void {
		$this->database->executeChange(SQLDataStorer::UPDATE_PET_INVENTORY, [
			"inventory" => base64_encode($pet->getInventoryManager()->compressContents()),
			"player" => $pet->getPetOwnerName(),
			"petname" => $pet->getPetName()
		]);
	}
}