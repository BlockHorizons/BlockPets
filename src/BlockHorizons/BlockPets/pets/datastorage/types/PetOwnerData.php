<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\datastorage\types;

use pocketmine\utils\UUID;

class PetOwnerData {

	/** @var string */
	private $owner;
	/** @var PetData[] */
	private $pets = [];

	public function __construct(string $owner) {
		$this->owner = $owner;
	}

	/**
	 * Returns the pet owner's name.
	 *
	 * @return string
	 */
	public function getName(): string {
		return $this->owner;
	}

	public function getPet(UUID $uuid): ?PetData {
		return $this->getPetByRawUUID($uuid->toBinary());
	}

	public function getPetByRawUUID(string $rawUUID): ?PetData {
		return $this->pets[$rawUUID] ?? null;
	}

	public function setPet(PetData $data): void {
		$this->pets[$data->getUUID()->toBinary()] = $data;
	}

	public function removePet(UUID $uuid): void {
		$this->removePetByRawUUID($uuid->toBinary());
	}

	public function removePetByRawUUID(string $rawUUID): void {
		unset($this->pets[$rawUUID]);
	}

	public function getPets(): array {
		return $this->pets;
	}
}