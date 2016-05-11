<?php

namespace App\Model;
 
use Carbon\Carbon;
use Nette;
 
class CronManager extends Nette\Object
{

	private $file;

	public function __construct(string $file)
	{
		$this->file = $file;
	}

	public function setNextStart(Carbon $datetime)
	{
		file_put_contents($this->file, $datetime->__toString());
	}
	
}
