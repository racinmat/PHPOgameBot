<?php
/**
 * Created by PhpStorm.
 * User: Azathoth
 * Date: 30. 7. 2015
 * Time: 17:14
 */

namespace App\Enum;
use Nette\InvalidStateException;


/**
 * Class Defense
 * @package App\Enum
 * @method static ProbingStatus _(string $value)
 */
class ProbingStatus extends Enum
{

	const
		CURRENTLY_PROBING = 'currently probing',
		GOT_ALL_INFORMATION = 'got all information',
		MISSING_RESEARCH = 'missing research',
		MISSING_BUILDINGS = 'missing buildings',
		MISSING_DEFENSE = 'missing defense',
		MISSING_FLEET = 'missing fleet'
	;

	public function getMinimalResult() : int
	{
		switch ($this->getValue()) {
			case static::MISSING_FLEET: return 1;
			case static::MISSING_DEFENSE: return 2;
			case static::MISSING_BUILDINGS: return 3;
			case static::MISSING_RESEARCH: return 5;
			case static::GOT_ALL_INFORMATION: return 7;
		}
		throw new InvalidStateException('Report not read.');
	}

	public function missingAnyInformation() : bool
	{
		switch ($this->getValue()) {
			case static::GOT_ALL_INFORMATION: return false;
			case static::CURRENTLY_PROBING: return false;
			default: return true;
		}
	}

	public function getMaximalResult() : int
	{
		switch ($this->getValue()) {
			case static::MISSING_FLEET: return 1;
			case static::MISSING_DEFENSE: return 2;
			case static::MISSING_BUILDINGS: return 4;
			case static::MISSING_RESEARCH: return 6;
			case static::GOT_ALL_INFORMATION: return 7;
		}
		throw new InvalidStateException('Report not read.');
	}

	public function min(ProbingStatus $another) : ProbingStatus
	{
		if ($this->getMinimalResult() <= $another->getMinimalResult()) {
			return $this;
		} else {
			return $another;
		}
	}
}