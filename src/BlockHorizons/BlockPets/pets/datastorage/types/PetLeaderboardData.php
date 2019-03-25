<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\datastorage\types;

class PetsLeaderboardData {

	/** @var MinimalPetData[] */
	private $pets = [];

	public function getPets(): array {
		return $this->pets;
	}

	public function addEntry(MinimalPetData $data): void {
		$this->pets[spl_object_id($data)] = $data;
	}
}