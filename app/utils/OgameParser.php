<?php
/**
 * Created by PhpStorm.
 * User: Azathoth
 * Date: 15. 5. 2016
 * Time: 13:03
 */

namespace App\Utils;


use App\Model\ValueObject\Coordinates;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Nette\Utils\Strings;

class OgameParser
{

	public static function parseOgameCoordinates(string $coordinates) : Coordinates
	{
		$params = Strings::match($coordinates, '~\[(?<galaxy>\d+):(?<system>\d+):(?<planet>\d+)\]~');
		return new Coordinates($params['galaxy'], $params['system'], $params['planet']);
	}

	public static function parseOgameTimeInterval(string $interval) : CarbonInterval
	{
		$params = Strings::match($interval, '~((?<weeks>\d{1,2})t)? ?((?<days>\d{1,2})d)? ?((?<hours>\d{1,2})hod)? ?((?<minutes>\d{1,2})min)? ?((?<seconds>\d{1,2})s)?~');
		return new CarbonInterval(0, 0, $params['weeks'] ?? 0, $params['days'] ?? 0, $params['hours'] ?? 0, $params['minutes'] ?? 0, $params['seconds'] ?? 0);
	}

	/**
	 * @param string $string
	 * @return int[]
	 */
	public static function parseSlash(string $string) : array
	{
		$params = Strings::match($string, '~(?<first>\d+)/(?<second>\d+)~');
		return [(int) $params['first'], (int) $params['second']];
	}

	public static function getNearestTime(array $timeIntervals) : Carbon
	{
		if (count($timeIntervals) === 0) {
			return Carbon::now();
		}

		$minimalTime = Carbon::now()->addYears(666);    //just some big date in the future
		foreach ($timeIntervals as $timeInterval) {
			$minimalTime = $minimalTime->min(Carbon::now()->add(OgameParser::parseOgameTimeInterval($timeInterval)));
		}
		return $minimalTime;
	}

	public static function parseResources(string $resource) : string
	{
		return Strings::replace($resource, '~\.~');
	}

	/**
	 * @param string $temperatures
	 * @return int[]
	 */
	public static function parseTemperature(string $temperatures) : array
	{
		$params = Strings::match($temperatures, '~(?<from>-?\d+)°C až (?<to>-?\d+)°C~');
		return [(int) $params['from'], (int) $params['to']];
	}

}