<?php
/**
 * Created by PhpStorm.
 * User: Azathoth
 * Date: 30. 7. 2015
 * Time: 17:14
 */

namespace App\Enum;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query;


/**
 * Class Building
 * @package App\Enum
 * @method static OrderType _(string $value)
 */
class OrderType extends Enum
{

	const
		ASC = Criteria::ASC,
		DESC = Criteria::DESC,
		NULL = NULL
	;

	public function isActive() : bool 
	{
		return $this->getValue() !== NULL;
	}
	
}