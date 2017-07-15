<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\events;

use BlockHorizons\BlockPets\Loader;
use BlockHorizons\BlockPets\pets\BasePet;
use pocketmine\event\Cancellable;

class PetLevelUpEvent extends BlockPetsEvent implements Cancellable {

	public static $handlerList = null;

	private $pet;
	private $from;
	private $to;

	public function __construct(Loader $loader, BasePet $pet, int $from, int $to) {
		parent::__construct($loader);
		$this->pet = $pet;
		$this->from = $from;
		$this->to = $to;
	}

	/**
	 * Returns the pet leveled up in the process.
	 *
	 * @return BasePet
	 */
	public function getPet(): BasePet {
		return $this->pet;
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
	public function setTo(int $to) {
		$this->to = $to;
	}
}