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
 * @method static PlanetProbingStatus _(string $value)
 */
class PlanetProbingStatus extends Enum
{

	const
		CURRENTLY_PROBING = 'currently probing',
		GOT_ALL_INFORMATION = 'got all information',
		DID_NOT_GET_ALL_INFORMATION = 'did not get all information'
	;

}