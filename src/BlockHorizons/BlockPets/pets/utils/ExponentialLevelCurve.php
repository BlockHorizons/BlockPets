<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\utils;

class ExponentialLevelCurve implements LevelCurve {

	/** @var float */
	private $base;
	/** @var float */
	private $multiplier;
	/** @var float */
	private $exponent;

	public function __construct(float $base, float $multiplier, float $exponent) {
		$this->base = $base;
		$this->multiplier = $multiplier;
		$this->exponent = $exponent;
	}

	public function getRequiredLevelPoints(int $level): int {
		return (int) floor($this->base + ($this->multiplier * ($level ** $this->exponent)));
	}

	public function getLevelFromPoints(int $points): int {
		return (int) ceil((($points - $this->base) / $this->multiplier) ** (1 / $this->exponent));
	}
}