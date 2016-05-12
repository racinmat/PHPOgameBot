<?php

namespace App\Model\Game;
 
use App\Enum\Building;
use App\Enum\MenuItem;
use App\Enum\Upgradable;
use App\Model\Entity\Planet;
use app\model\queue\ICommandProcessor;
use App\Model\ResourcesCalculator;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Nette\Object;
use Nette\Utils\Strings;

class UpgradeManager extends Object implements ICommandProcessor
{

	/** @var \AcceptanceTester */
	protected $I;

	/** @var ResourcesCalculator */
	protected $resourcesCalculator;

	/** @var PlanetManager */
	protected $planetManager;

	public function __construct(\AcceptanceTester $I, PlanetManager $planetManager, ResourcesCalculator $resourcesCalculator)
	{
		$this->I = $I;
		$this->planetManager = $planetManager;
		$this->resourcesCalculator = $resourcesCalculator;
	}

	/**
	 * @param Upgradable $upgradable
	 * @return bool returns true when building was built, otherwise returns false
	 */
	public function upgrade(Upgradable $upgradable) : bool
	{
		//možná refreshnout všechna data hned po zalogování. Refreshovat vše včetně levelů budov, výzkumů apod.
		$this->planetManager->refreshResourceData();
		$planet = $this->planetManager->getMyHomePlanet();
		if (!$this->canBuild($planet, $upgradable)) {
			return false;
		}
		$this->openMenu($upgradable);
		$I = $this->I;
		$I->click($upgradable->getBuildButtonSelector());
		$I->wait(1);
		return true;
	}

	protected function openMenu(Upgradable $upgradable)
	{
		$I = $this->I;
		$I->click($upgradable->getMenuLocation()->getSelector());
		$I->click($upgradable->getSelector());
		$I->wait(1);
	}

	protected function canBuild(Planet $planet, Upgradable $upgradable) : bool
	{
		return $this->resourcesCalculator->isEnoughResourcesForUpgrade($planet, $upgradable) && ! $this->currentlyUpgrading($upgradable);
	}

	public function getTimeToUpgradeAvailable(Planet $planet, Upgradable $upgradable) : Carbon
	{
		$datetime1 = $this->resourcesCalculator->getTimeToEnoughResourcesForUpgrade($planet, $upgradable);
		$datetime2 = $this->getTimeToFinishUpgrade($upgradable);
		return $datetime1->max($datetime2);
	}

	protected function parseOgameTimeInterval(string $interval) : CarbonInterval
	{
		$params = Strings::match($interval, '~((?<weeks>\d{1,2})t)? ?((?<days>\d{1,2})d)? ?((?<hours>\d{1,2})hod)? ?((?<minutes>\d{1,2})min)? ?((?<seconds>\d{1,2})s)?~');
		return new CarbonInterval(0, 0, $params['weeks'], $params['days'], $params['hours'], $params['minutes'], $params['seconds']);
	}

	protected function currentlyUpgrading(Upgradable $upgradable) : bool
	{
		return ! $this->I->seeExists($upgradable->getFreeToEnhanceText(), $upgradable->getEnhanceStatusSelector());
	}

	protected function getTimeToFinishUpgrade(Upgradable $upgradable) : Carbon
	{
		$I = $this->I;
		$I->click(MenuItem::_(MenuItem::OVERVIEW));
		$I->wait(1);
		if ($I->seeElementExists("{$upgradable->getEnhanceStatusSelector()} #Countdown")) {
			$interval = $I->grabTextFrom("{$upgradable->getEnhanceStatusSelector()} #Countdown");
			return Carbon::now()->add($this->parseOgameTimeInterval($interval));
		}
		return Carbon::now();
	}

}
