<?php
 
namespace App\Components;
 
use App\Model\Queue\Command\IBuildCommand;
use App\Model\Queue\Command\ICommand;
use App\Model\Queue\Command\IUpgradeCommand;
use App\Model\Queue\Command\ProbePlayersCommand;
use App\Model\Queue\Command\ScanGalaxyCommand;
use App\Model\Queue\Command\SendFleetCommand;
use App\Model\Queue\QueueManager;
use Latte\Runtime\CachingIterator;
use Nette;
use Nette\Application\UI;
use Ramsey\Uuid\Uuid;


/**
 * Class DisplayCommand
 * @package App\Components
 * @property Nette\Application\UI\ITemplate|\stdClass $template
 */
class DisplayCommand extends UI\Control
{

	/** @var QueueManager */
	private $queueManager;

	public function __construct(QueueManager $queueManager)
	{
		parent::__construct();
		$this->queueManager = $queueManager;
	}

	public function render(CachingIterator $iterator, ICommand $command)
	{
		$classToTemplate = [
			IUpgradeCommand::class => 'upgrade.latte',
			IBuildCommand::class => 'build.latte',
			ScanGalaxyCommand::class => 'scanGalaxy.latte',
			ProbePlayersCommand::class => 'probePlayers.latte',
			SendFleetCommand::class => 'sendFleet.latte'
		];

		$controlTemplate = '';
		foreach ($classToTemplate as $class => $template) {
			if ($command instanceof $class) {
				$controlTemplate = $template;
				break;
			}
		}

		$this->template->setFile(__DIR__ . "/$controlTemplate");
		$this->template->iterator = $iterator;
		$this->template->command = $command;
		$this->template->render();
	}

	public function handleRemove(string $uuid)
	{
		$this->queueManager->removeCommand(Uuid::fromString($uuid));
		$this->presenter->redirect('default');
	}

	public function handleMoveUp(string $uuid)
	{
		$this->queueManager->moveCommandUp(Uuid::fromString($uuid));
		$this->presenter->redirect('default');
	}

	public function handleMoveDown(string $uuid)
	{
		$this->queueManager->moveCommandDown(Uuid::fromString($uuid));
		$this->presenter->redirect('default');
	}

}
