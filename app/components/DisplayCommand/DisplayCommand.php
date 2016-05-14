<?php
 
namespace App\Components;
 
use App\Model\Queue\Command\IBuildCommand;
use App\Model\Queue\Command\ICommand;
use App\Model\Queue\Command\IUpgradeCommand;
use Nette;
use Nette\Application\UI;
use Tracy\Debugger;

class DisplayCommand extends UI\Control
{

	public function __construct()
	{
		parent::__construct();
	}

	public function render(int $index, ICommand $command)
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
		$this->template->index = $index;
		$this->template->command = $command;
		$this->template->render();
	}
	
}