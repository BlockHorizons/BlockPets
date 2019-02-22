<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\sessions\types;

use BlockHorizons\BlockPets\pets\datastorage\types\PetData;

use pocketmine\nbt\tag\CompoundTag;

class PetSelectionData {

	/** @var string */
	public $type;
	/** @var CompoundTag */
	public $namedtag;

	public function __construct(string $type, ?CompoundTag $namedtag = null) {
		$this->type = $type;
		$this->namedtag = $namedtag ?? new CompoundTag();
	}

	public function toPetData(string $name, string $owner): PetData {
		$data = PetData::new($name, $this->type, $owner, 0, $this->namedtag);
		return $data;
	}
}