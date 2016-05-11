<?php

namespace App\Model;
 
use Nette;
 
class BuildingsManager extends Nette\Object
{

	/** @var \AcceptanceTester */
	private $I;

	public function __construct(\AcceptanceTester $I)
	{
		$this->I = $I;
	}

	public function buildMetalMine()
	{
		$I = $this->I;
		$I->click('#menuTable > li:nth-child(2) > a');
		$I->click('.supply1 > div:nth-child(1) > a:nth-child(1)');
//		$I->click('.build-it > span:nth-child(1)');
	}

	public function buildCrystalMine()
	{
		$I = $this->I;
		$I->click('#menuTable > li:nth-child(2) > a');
		$I->click('.supply2 > div:nth-child(1) > a:nth-child(1)');
		$I->click('.build-it > span:nth-child(1)');
	}

	public function buildDeuteriumMine()
	{
		$I = $this->I;
		$I->click('#menuTable > li:nth-child(2) > a');
		$I->click('.supply3 > div:nth-child(1) > a:nth-child(1)');
//		$I->click('.build-it > span:nth-child(1)');
	}

	public function buildSolarPowerPlant()
	{
		$I = $this->I;
		$I->click('#menuTable > li:nth-child(2) > a');
		$I->click('.supply4 > div:nth-child(1) > a:nth-child(1)');
//		$I->click('.build-it > span:nth-child(1)');
	}
}