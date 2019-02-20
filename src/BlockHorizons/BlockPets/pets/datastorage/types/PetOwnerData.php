<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\datastorage\types;

use pocketmine\Player;

class PetOwnerData {

	/** @var string */
	private $owner;
	/** @var PetData[] */
	private $pets = [];

	public function __construct(string $owner) {
		$this->owner = $owner;
	}

	public function getName(): string {
		return $this->owner;
	}

	public function getPet(string $pet): ?PetData {
		return $this->pets[strtolower($pet)] ?? null;
	}

	public function setPet(PetData $data): void {
		$this->pets[strtolower($data->getName())] = $data;
	}

	public function removePet(string $pet): void {
		unset($this->pets[strtolower($pet)]);
	}

	public function getPets(): array {
		return $this->pets;
	}
}