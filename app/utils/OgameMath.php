<?php

namespace App\Utils;

use App\Enum\Enum;
use App\Enum\ProbingStatus;
use App\Model\Entity\Planet;
use App\Model\Queue\Command\ArraySerializable;
use App\Model\Queue\Command\ICommand;
use App\Model\Queue\Command\IEnhanceCommand;
use App\Model\ValueObject\Coordinates;
use App\Model\ValueObject\Flight;
use Carbon\Carbon;
use Nette\DI\Container;
use Nette\Object;
use Ramsey\Uuid\Uuid;

class OgameMath extends Object
{

	public static function calculateEnemyLevel(int $myLevel, int $probes, int $result)
	{
		return ceil($myLevel + gmp_sign($probes - $result) * sqrt(abs($probes - $result)));
	}

	public static function calculateProbesToSend(int $myLevel, int $enemyLevel, int $desiredResult)
	{
		return max(1, $desiredResult - ($myLevel - $enemyLevel) * abs($myLevel - $enemyLevel));
	}

	public static function calculateProbesToGetAllInfo(int $myLevel, int $probes, int $currentResult)
	{
		$desiredResult = ProbingStatus::_(ProbingStatus::GOT_ALL_INFORMATION)->getMinimalResult();
		$enemyLevel = static::calculateEnemyLevel($myLevel, $probes, $currentResult);
		return static::calculateProbesToSend($myLevel, $enemyLevel, $desiredResult);
	}

}