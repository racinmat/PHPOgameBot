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

	abstract protected function getClassSelector() : string;

	abstract public function getMenuLocation() : MenuItem;

	public function getBuildButtonSelector() : string
	{
		return '.build-it > span:nth-child(1)';
	}

}