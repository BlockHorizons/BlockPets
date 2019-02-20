<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\datastorage;

use BlockHorizons\BlockPets\pets\datastorage\types\PetData;
use BlockHorizons\BlockPets\pets\datastorage\types\PetOwnerData;

class SQLiteDataStorer extends SQLDataStorer {

	public function registerPet(PetData $data): void {
		$this->database->executeChange(SQLDataStorer::REGISTER_PET, [
			"player" => $data->getOwner(),
			"petname" => $data->getName(),
			"entityname" => $data->getType(),
			"petsize" => $data->scale,
			"isbaby" => (int) $data->is_baby,
			"chested" => (int) $data->is_chested,
			"petlevel" => $data->level,
			"levelpoints" => $data->level_points,
			"visible" => $data->is_visible,
			"inventory" => base64_encode($data->inventory_manager->write())
		]);
	}

	public function load(string $owner, callable $callable): void {
		$this->database->executeSelect(SQLDataStorer::LOAD_PLAYER_PETS, [
			"player" => $owner
		], function(array $entries) use($owner, $callable): void {
			$player_data = new PetOwnerData($owner);

			foreach($entries as $entry) {
				$pet_data = new PetData($entry["PetName"], $entry["EntityName"], $owner);
				$pet_data->size = $entry["PetSize"];
				$pet_data->is_baby = (bool) $entry["IsBaby"];
				$pet_data->level = $entry["PetLevel"];
				$pet_data->level_points = $entry["LevelPoints"];
				$pet_data->is_chested = (bool) $entry["Chested"];
				$pet_data->is_visible = (bool) $entry["Visible"];
				$pet_data->inventory_manager->read(base64_decode($entry["Inventory"]));
				$player_data->setPet($pet_data);
			}

			$callable($player_data);
		});
	}
}