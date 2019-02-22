<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\datastorage\types;

use pocketmine\nbt\tag\CompoundTag;
use pocketmine\utils\UUID;

final class PetData {

	public static function new(string $name, string $type, string $owner, int $xp = 0, ?CompoundTag $namedtag = null): PetData {
		return new PetData(UUID::fromRandom(), $name, $type, $owner, $xp, $namedtag);
	}

	/** @var UUID */
	private $uuid;
	/** @var string */
	private $name;
	/** @var string */
	private $type;
	/** @var string */
	private $owner;

	/** @var int */
	private $xp = 0;
	/** @var CompoundTag */
	private $namedtag;

	public function __construct(UUID $uuid, string $name, string $type, string $owner, int $xp = 0, ?CompoundTag $namedtag = null) {
		$this->uuid = $uuid;
		$this->name = $name;
		$this->type = $type;
		$this->owner = $owner;

		$this->setXp($xp);
		$this->setNamedTag($namedtag ?? new CompoundTag());
	}

	public function getUUID(): UUID {
		return $this->uuid;
	}

	/**
	 * Returns the pet's name.
	 *
	 * @return string
	 */
	public function getName(): string {
		return $this->name;
	}

	/**
	 * Returns the pet's save ID. This is used to get
	 * the right pet type (BatPet, CowPet etc) associated
	 * with this pet data.
	 *
	 * @return string
	 */
	public function getType(): string {
		return $this->type;
	}

	/**
	 * Returns the pet owner's name.
	 *
	 * @return string
	 */
	public function getOwner(): string {
		return $this->owner;
	}

	public function getXp(): int {
		return $this->xp;
	}

	public function getNamedTag(): CompoundTag {
		return $this->namedtag;
	}

	public function setXp(int $xp): void {
		if($xp < 0) {
			throw new \InvalidArgumentException("Value of pet's points cannot be " . $xp . ".");
		}

		$this->xp = $xp;
	}

	public function setNamedTag(CompoundTag $tag): void {
		$this->namedtag = $tag;
	}
}