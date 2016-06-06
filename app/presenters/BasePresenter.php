<?php

namespace App\Presenters;

use Nette;
use App\Model;


/**
 * Base presenter for all application presenters.
 * @property Nette\Application\UI\ITemplate|\stdClass $template
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{

}
