<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\utils;

interface LevelCurve {

	/**
	 * Returns the required amount of points for the given level to level up.
	 *
	 * @param int $level
	 *
	 * @return int
	 */
	public function getRequiredLevelPoints(int $level): int;

	/**
	 * Returns the level for the given points.
	 *
	 * @param int $points
	 *
	 * @return int
	 */
	public function getLevelFromPoints(int $points): int;
}