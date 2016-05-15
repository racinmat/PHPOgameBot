<?php
 
namespace App\Components;
 
use App\Model\Queue\Command\IBuildCommand;
use App\Model\Queue\Command\ICommand;
use App\Model\Queue\Command\IUpgradeCommand;
use App\Model\Queue\QueueManager;
use Latte\Runtime\CachingIterator;
use Nette;
use Nette\Application\UI;
use Ramsey\Uuid\Uuid;
use Tracy\Debugger;

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
			IBuildCommand::class => 'build.latte'
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
		$this->queueManager->removeFromQueue(Uuid::fromString($uuid));
	}

	public function handleMoveUp(string $uuid)
	{
		$this->queueManager->moveCommandUp(Uuid::fromString($uuid));
	}

	public function handleMoveDown(string $uuid)
	{
		$this->queueManager->moveCommandDown(Uuid::fromString($uuid));
	}

}
