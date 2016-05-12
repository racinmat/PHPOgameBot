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
 * Class Upgradable
 * @package App\Enum
 */
abstract class Upgradable extends Enhanceable
{

	abstract public function getPriceToNextLevel($currentLevel) : Resources;
	
}