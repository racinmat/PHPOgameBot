<?php
/**
 * Created by PhpStorm.
 * User: Azathoth
 * Date: 30. 7. 2015
 * Time: 17:14
 */

namespace App\Enum;

/**
 * Class Enhanceable
 * @package App\Enum
 */
abstract class Enhanceable extends Enum
{

	public function getSelector() : string
	{
		return $this->getClassSelector() . ' > div:nth-child(1) > a.slideIn';
	}

	public function getBuildButtonSelector() : string
	{
		return '.build-it > span:nth-child(1)';
	}

	abstract public function getClassSelector() : string;

	abstract public function getMenuLocation() : MenuItem;

	abstract public function getFreeToEnhanceText() : string;

	abstract public function getEnhanceStatusSelector() : string;

	abstract public function getEnhanceCountdownSelector() : string;

}