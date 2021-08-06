<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\events;

use BlockHorizons\BlockPets\Loader;
use BlockHorizons\BlockPets\pets\BasePet;
use pocketmine\event\Cancellable;

class PetLevelUpEvent extends PetEvent implements Cancellable {

	/** @var int */
	private $from;
	/** @var int */
	private $to;

	public function __construct(Loader $loader, $pet, int $from, int $to) {
		parent::__construct($loader, $pet);
		$this->pet = $pet;
		$this->from = $from;
		$this->to = $to;
	}

	/**
	 * Returns the pet level BEFORE adding any levels.
	 *
	 * @return int
	 */
	public function getFrom(): int {
		return $this->from;
	}

	/**
	 * Returns the pet level AFTER adding the levels.
	 *
	 * @return int
	 */
	public function getTo(): int {
		return $this->to;
	}

	/**
	 * Sets the pet level AFTER adding points to the given parameter.
	 *
	 * @param int $to
	 */
	public function setTo(int $to): void {
		if($to < 1) {
			throw new \LogicException("Pet level cannot be negative.");
		}

		$this->to = $to;
	}
}