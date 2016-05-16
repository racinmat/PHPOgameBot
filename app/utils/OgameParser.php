<?php
/**
 * Created by PhpStorm.
 * User: Azathoth
 * Date: 15. 5. 2016
 * Time: 13:03
 */

namespace App\Utils;


use App\Model\ValueObject\Coordinates;
use Carbon\CarbonInterval;
use Nette\Utils\Strings;

class OgameParser
{

	public static function parseOgameCoordinates(string $coordinates) : Coordinates
	{
		$params = Strings::match($coordinates, '~\[(?<galaxy>\d{1}):(?<system>\d{3}):(?<planet>\d{1,2})\]~');
		return new Coordinates($params['galaxy'], $params['system'], $params['planet']);
	}

	public static function parseOgameTimeInterval(string $interval) : CarbonInterval
	{
		$params = Strings::match($interval, '~((?<weeks>\d{1,2})t)? ?((?<days>\d{1,2})d)? ?((?<hours>\d{1,2})hod)? ?((?<minutes>\d{1,2})min)? ?((?<seconds>\d{1,2})s)?~');
		return new CarbonInterval(0, 0, $params['weeks'], $params['days'], $params['hours'], $params['minutes'], $params['seconds']);
	}

}