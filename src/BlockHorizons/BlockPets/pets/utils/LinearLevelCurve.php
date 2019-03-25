<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\utils;

class LinearLevelCurve implements LevelCurve {

	/** @var float */
	private $base;
	/** @var float */
	private $multiplier;

	public function __construct(float $base, float $multiplier) {
		$this->base = $base;
		$this->multiplier = $multiplier;
	}

	public function getRequiredLevelPoints(int $level): int {
		return (int) floor($this->base + ($level * $this->multiplier));
	}

	public function getLevelFromPoints(int $points): int {
		return (int) ceil(($points - $this->base) / $this->multiplier);
	}
}