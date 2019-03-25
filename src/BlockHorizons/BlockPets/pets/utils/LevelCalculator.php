<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\utils;

final class LevelCalculator {

	/** @var LevelCurve */
	private static $curve;

	public static function get(): LevelCurve {
		return self::$curve;
	}

	public static function set(LevelCurve $curve): void {
		self::$curve = $curve;
	}

	public static function getLevelPoints(int $level): int {
		$curve = self::get();
		return 1 + $curve->getRequiredLevelPoints($level) - $curve->getRequiredLevelPoints($level - 1);
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

		$curve = self::get();

		while($remaining > ($reqd = $curve->getRequiredLevelPoints($level + $levelled))) {
			++$levelled;
			$remaining -= $reqd;
		}

		return $levelled;
	}
}