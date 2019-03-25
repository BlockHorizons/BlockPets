<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\datastorage\types;

use BlockHorizons\BlockPets\pets\utils\LevelCalculator;

class MinimalPetData {

	/** @var string */
	private $name;
	/** @var string */
	private $type;
	/** @var string */
	private $owner;
	/** @var int */
	private $points = 0;

	public function __construct(string $name, string $type, string $owner, int $points = 0) {
		$this->name = $name;
		$this->type = $type;
		$this->owner = $owner;
		$this->setPoints($points);
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

	public function getPoints(): int {
		return $this->points;
	}

	public function setPoints(int $points): void {
		if($points < 0) {
			throw new \InvalidArgumentException("Value of points cannot be less than 0, got " . $points . ".");
		}

		$this->points = $points;
	}

	public function getLevel(): int {
		return LevelCalculator::get()->getLevelFromPoints($this->points);
	}

	public function getLevelPoints(): int {
		return $this->points - LevelCalculator::get()->getRequiredLevelPoints($this->getPetLevel() - 1);
	}
}