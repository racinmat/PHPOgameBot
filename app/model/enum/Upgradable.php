<?php
/**
 * Created by PhpStorm.
 * User: Azathoth
 * Date: 30. 7. 2015
 * Time: 17:14
 */

namespace App\Enum;
use App\Model\Entity\Planet;
use App\Model\ValueObject\Resources;

/**
 * Class Upgradable
 * @package App\Enum
 */
abstract class Upgradable extends Enhanceable
{

	public function getPriceToNextLevel($currentLevel) : Resources
	{
		return $this->getBasePrice()->multiplyByScalar(pow($this->getNextLevelPriceConstant(), $currentLevel));
	}

	abstract protected function getBasePrice() : Resources;

	abstract protected function getNextLevelPriceConstant() : float;

	abstract public function getCurrentLevel(Planet $planet) : int;

	abstract public function setCurrentLevel(Planet $planet, int $currentLevel);
}