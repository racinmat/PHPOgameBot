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
 * Class MenuItem
 * @package App\Enum
 * @method static MenuItem _(string $value)
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

	public function getSelector() : string
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
		throw new InvalidStateException('Unknown value, no selector found.');
	}

	public function getUrlIdentifier()
	{
		switch ($this->getValue()) {
			case static::OVERVIEW: return 'page=overview';
			case static::RESOURCES: return 'page=resources';
			case static::STATION: return 'page=station';
			case static::RESEARCH: return 'page=research';
			case static::SHIPYARD: return 'page=shipyard';
			case static::DEFENSE: return 'page=defense';
			case static::FLEET: return 'page=fleet1';
			case static::GALAXY: return 'page=galaxy';
		}
		throw new InvalidStateException('Unknown value, no identifier found.');
	}
	
}