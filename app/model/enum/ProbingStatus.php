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
 * Class Defense
 * @package App\Enum
 * @method static ProbingStatus _(string $value)
 */
class ProbingStatus extends Enum
{

	const
		CURRENTLY_PROBING = 'currently probing',
		GOT_ALL_INFORMATION = 'got all information',
		DID_NOT_GET_ALL_INFORMATION = 'did not get all information'
	;
	
}