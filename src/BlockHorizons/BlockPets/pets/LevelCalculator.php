<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets;

class LevelCalculator {

	/**
	 * Returns the required amount of points for the given level to level up.
	 *
	 * @param int $level
	 *
	 * @return int
	 */
	public static function getRequiredLevelPoints(int $level): int {
		return (int) (20 + $level / 1.5 * $level);
	}

	/**
	 * Returns how many levels can the points be split into.
	 *
	 * For example, level 1 requires 20xp and level 2 requires 22xp.
	 * calculateLevelUp(43, 0, $remaining) will return 3 and the
	 * value of $remaining will be set to 1.
	 *
	 * @param int $points
	 * @param int $level
	 *
	 * @return int
	 */
	public static function calculateLevelUp(int $points, int $level, ?int &$remaining = null): int {
		$remaining = $points;
		$levelled = 0;

		while($remaining > ($reqd = self::getRequiredLevelPoints($level + $levelled))) {
			++$levelled;
			$remaining -= $reqd;
		}

		return $levelled;
	}

	
	public static function calculateRemainingLevelPoints(int $points, int $level, ?int &$remaining = null): int {
		$remaining = $points;
		$levelled = 0;

		while($remaining > ($reqd = self::getRequiredLevelPoints($level + $levelled))) {
			++$levelled;
			$remaining -= $reqd;
		}

		return $remaining;
	}
}