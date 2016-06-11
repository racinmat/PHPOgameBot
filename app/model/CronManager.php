<?php

namespace App\Model;
 
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Nette;
 
class CronManager extends Nette\Object
{

	/** @var string */
	private $file;

	public function __construct(string $file)
	{
		$this->file = $file;
	}

	public function setNextStart(Carbon $datetime)
	{
		file_put_contents($this->file, $datetime->__toString());
	}

	public function addNextStart(Carbon $datetime)
	{
		$nextStart = Carbon::instance(new \DateTime(file_get_contents($this->file)));
		$this->setNextStart($nextStart->min($datetime));
	}

	public function addNextPeriodicRun()
	{
		/** @var Carbon $nextStart */
		$nextStart = Carbon::now()->add($this->getNextRunInterval());
		$nextStart->addMinutes(random_int(0, 4))->addSeconds(random_int(0, 59));
		$this->addNextStart($nextStart);
	}

	private function getNextRunInterval() : CarbonInterval
	{
		$inDay = new CarbonInterval(0, 0, 0, 0, 0, 30);
		$inNight = new CarbonInterval(0, 0, 0, 0, 0, 55);

		$longerIntervalFromHour = 1;
		$longerIntervalToHour = 7;
		if (Carbon::now()->hour > $longerIntervalFromHour && Carbon::now()->hour < $longerIntervalToHour) {
			return $inNight;
		}
		return $inDay;
	}
}
