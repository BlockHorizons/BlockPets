<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\events;

use BlockHorizons\BlockPets\Loader;
use BlockHorizons\BlockPets\pets\BasePet;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;

class PetLevelUpEvent extends PetEvent implements Cancellable {

	use CancellableTrait;

	public function __construct(Loader $loader, BasePet $pet, private int $from, private int $to) {
		parent::__construct($loader, $pet);
	}

	/**
	 * Returns the pet level BEFORE adding any levels.
	 */
	public function getFrom(): int {
		return $this->from;
	}

	/**
	 * Returns the pet level AFTER adding the levels.
	 */
	public function getTo(): int {
		return $this->to;
	}

	/**
	 * Sets the pet level AFTER adding points to the given parameter.
	 */
	public function setTo(int $to): void {
		if($to < 1) {
			throw new \LogicException("Pet level cannot be negative.");
		}

		$this->to = $to;
	}
}