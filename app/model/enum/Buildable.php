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
 * Class Buildable
 * @package App\Enum
 */
abstract class Buildable extends Enhanceable
{

	abstract public function getPrice() : Resources;

}