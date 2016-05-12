<?php

namespace App\Model\Game;
 
use App\Enum\Buildable;
use App\Enum\Building;
use App\Enum\Defense;
use App\Model\Entity\Planet;
use App\Model\Queue\Command\IBuildCommand;
use App\Model\Queue\Command\ICommand;
use app\model\queue\ICommandProcessor;
use App\Model\ResourcesCalculator;
use Carbon\Carbon;
use Nette;

class BuildManager extends Nette\Object implements ICommandProcessor
{

	/** @var \AcceptanceTester */
	protected $I;

	/** @var PlanetManager */
	protected $planetManager;
	
	/** @var ResourcesCalculator */
	protected $resourcesCalculator;

	public function __construct(\AcceptanceTester $I, PlanetManager $planetManager, ResourcesCalculator $resourcesCalculator)
	{
		$this->I = $I;
		$this->planetManager = $planetManager;
		$this->resourcesCalculator = $resourcesCalculator;
	}
	
	/**
	 * @param Buildable $buildable
	 * @param int $amount
	 * @return bool returns true when building was built, otherwise returns false
	 */
	public function build(Buildable $buildable, int $amount) : bool
	{
		//možná refreshnout všechna data hned po zalogování
		//todo: dodělat kontrolu, že se nestaví nějaké lodě/obrana
		$this->planetManager->refreshResourceData();
		$planet = $this->planetManager->getMyHomePlanet();
		if (!$this->resourcesCalculator->isEnoughResourcesForBuild($planet, $buildable, $amount)) {
			return false;
		}
		$this->openMenu($buildable);
		$I = $this->I;
		$I->fillField('#number', $amount);
		$I->wait(1);
		$I->click($buildable->getBuildButtonSelector());
		$I->wait(1);
		return true;
	}

	protected function openMenu(Buildable $buildable)
	{
		$I = $this->I;
		$I->click($buildable->getMenuLocation()->getSelector());
		$I->click($buildable->getSelector());
		$I->wait(1);
	}

	public function canProcessCommand(ICommand $command) : bool
	{
		return $command instanceof IBuildCommand;
	}

	public function processCommand(ICommand $command) : bool
	{
		/** @var IBuildCommand $command */
		$this->build($command->getBuildable(), $command->getAmount());
	}
	
	public function getTimeToProcessingAvailable(Planet $planet, ICommand $command) : Carbon
	{
		// TODO: Implement getTimeToProcessingAvailable() method.
	}
}
