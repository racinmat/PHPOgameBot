<?php
 
namespace App\Model\ValueObject;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities;
use Nette\Object;
use Tracy\Debugger;

class CoordinatesDifference extends Coordinates
{

	public static $minGalaxy = 0;
	public static $minSystem = 0;
	public static $minPlanet = 0;

	public static $maxGalaxy = 5;
	public static $maxSystem = 498;
	public static $maxPlanet = 14;

	public function __construct(int $galaxy, int $system, int $planet)
	{
		parent::__construct($galaxy, $system, $planet);
	}

	public static function fromArray(array $data) : Coordinates
	{
		return new CoordinatesDifference($data['galaxy'], $data['system'], $data['planet']);
	}


}
