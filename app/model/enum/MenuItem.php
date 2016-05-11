<?php
/**
 * Created by PhpStorm.
 * User: Azathoth
 * Date: 30. 7. 2015
 * Time: 17:14
 */

namespace App\Enum;

use Nette\Localization\ITranslator;

/**
 * Class MenuItem
 * @package App\Enum
 * @method static _(string $value) @return MenuItem
 */
class MenuItem extends Enum
{

	const
		OVERVIEW = 'overview',
		RESOURCES = 'resources',
		STATION = 'station',
		RESEARCH = 'research',
		SHIPYARD = 'shipyard',
		DEFENSE = 'defense',
		FLEET = 'fleet',
		GALAXY = 'galaxy'
	;

	public function getSelector()
	{
		switch ($this->getValue()) {
			case static::OVERVIEW: return '#menuTable > li:nth-child(1) > a > span';
			case static::RESOURCES: return '#menuTable > li:nth-child(2) > a > span';
			case static::STATION: return '#menuTable > li:nth-child(3) > a > span';
			case static::RESEARCH: return '#menuTable > li:nth-child(5) > a > span';
			case static::SHIPYARD: return '#menuTable > li:nth-child(6) > a > span';
			case static::DEFENSE: return '#menuTable > li:nth-child(7) > a > span';
			case static::FLEET: return '#menuTable > li:nth-child(8) > a > span';
			case static::GALAXY: return '#menuTable > li:nth-child(9) > a > span';
		}
	}
}