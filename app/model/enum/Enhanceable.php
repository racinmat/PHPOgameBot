<?php
/**
 * Created by PhpStorm.
 * User: Azathoth
 * Date: 30. 7. 2015
 * Time: 17:14
 */

namespace App\Enum;
use App\Model\ValueObject\Resources;

/**
 * Class Enhanceable
 * @package App\Enum
 */
abstract class Enhanceable extends Enum
{

	public function getSelector() : string
	{
		return $this->getClassSelector() . ' > div:nth-child(1) > a.detail_button';
	}

	public function getBuildButtonSelector() : string
	{
		return '.build-it > span:nth-child(1)';
	}

	abstract protected function getClassSelector() : string;

	abstract public function getMenuLocation() : MenuItem;

	abstract public function getFreeToEnhanceText() : string;

	abstract public function getFreeToEnhanceSelector() : string;

}