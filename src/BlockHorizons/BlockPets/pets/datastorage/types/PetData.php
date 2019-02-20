<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\datastorage\types;

class PetData {

	/** @var string */
	private $name;
	/** @var string */
	private $type;
	/** @var string */
	private $owner;
	/** @var int */
	public $level = 0;
	/** @var int */
	public $level_points = 0;
	/** @var float|null */
	public $scale;
	/** @var bool */
	public $is_baby = false;
	/** @var bool */
	public $is_chested = false;
	/** @var bool */
	public $is_visible = true;
	/** @var PetInventoryManager */
	public $inventory_manager;

	public function __construct(string $name, string $type, string $owner) {
		$this->name = $name;
		$this->type = $type;
		$this->owner = $owner;
		$this->inventory_manager = new PetInventoryManager($this);
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
	 * Returns the pet owner's name.
	 *
	 * @return string
	 */
	public function getOwner(): string {
		return $this->owner;
	}

	/**
	 * Returns the pet's save ID. This is used to
	 * get the right pet type (BatPet, CowPet etc)
	 * associated with this pet data.
	 *
	 * @return string
	 */
	public function getType(): string {
		return $this->type;
	}
}