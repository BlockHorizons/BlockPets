<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\datastorage\types;

use pocketmine\nbt\tag\CompoundTag;
use pocketmine\utils\UUID;

class PetData extends MinimalPetData {

	public static function new(string $name, string $type, string $owner, int $xp = 0, ?CompoundTag $namedtag = null): PetData {
		return new PetData(UUID::fromRandom(), $name, $type, $owner, $xp, $namedtag);
	}

	/** @var UUID */
	private $uuid;
	/** @var CompoundTag */
	private $namedtag;

	public function __construct(UUID $uuid, string $name, string $type, string $owner, int $points = 0, ?CompoundTag $namedtag = null) {
		$this->uuid = $uuid;
		parent::__construct($name, $type, $owner, $points);
		$this->setNamedTag($namedtag ?? new CompoundTag());
	}

	public function getUUID(): UUID {
		return $this->uuid;
	}

	public function getNamedTag(): CompoundTag {
		return $this->namedtag;
	}

	public function setNamedTag(CompoundTag $tag): void {
		$this->namedtag = $tag;
	}
}