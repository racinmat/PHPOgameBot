<?php
/**
 * Created by PhpStorm.
 * User: Azathoth
 * Date: 30. 7. 2015
 * Time: 17:14
 */

namespace App\Enum;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Expression;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\Query;


/**
 * Class Building
 * @package App\Enum
 * @method static OrderPlanetsBy _(string $value)
 */
class OrderPlanetsBy extends Enum
{
	//values of this enum are used in DQL
	const
		LAST_VISIT = 'lastVisited',
		NULL = NULL
	;

	public function isActive() : bool
	{
		return $this->getValue() !== NULL;
	}

}