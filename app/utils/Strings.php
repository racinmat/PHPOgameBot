<?php

namespace App\Utils;

use App\Enum\Enum;
use App\Model\Entity\Planet;
use App\Model\Queue\Command\ArraySerializable;
use App\Model\Queue\Command\ICommand;
use App\Model\Queue\Command\IEnhanceCommand;
use App\Model\ValueObject\Coordinates;
use App\Model\ValueObject\Flight;
use Carbon\Carbon;
use Nette\DI\Container;
use Nette\Object;
use Ramsey\Uuid\Uuid;

class Strings extends \Nette\Utils\Strings
{

	public static function appendGetParametersToUrl(string $url, array $parameters) : string
	{
		if (static::contains($url, '?')) {
			$paramsString = '&';
		} else {
			$paramsString = '?';
		}
		array_walk($parameters, function (&$value, $key) {$value = static::webalize($key) .'=' . static::webalize($value);});
		$paramsString .= implode('&', $parameters);
		return $url . $paramsString;
	}

	public static function extractParametersFromUrl(string $url) : array 
	{
		$paramsString = static::match($url, '~\?[a-zA-Z0-9=&]+~')[0];
		$paramsString = static::replace($paramsString, '~\?~', '');
		$params = static::split($paramsString, '~&~');
		$associativeParams = [];
		foreach ($params as $param) {
			list($key, $value) = static::split($param, '~=~');
			$associativeParams[$key] = $value;
		}
		return $associativeParams;
	}
}