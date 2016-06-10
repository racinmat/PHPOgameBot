<?php

require_once __DIR__ . '/vendor/autoload.php';

/** @var \Nette\DI\Container $container */
$container = require_once __DIR__ . '/app/bootstrap.php';
//$hours = 1 + 24/60 + 38/3600;
//$minutes = ($hours - (int) $hours) * 60;
//$seconds = ($minutes - (int) $minutes) * 60;
//$hours = (int) $hours;
//$minutes = (int) $minutes;
//$seconds = (int) $seconds;
//echo "{$hours}:$minutes:$seconds";
//
//$interval = "26min";
//$params = \Nette\Utils\Strings::match($interval, '~((?<weeks>\d{1,2})t)? ?((?<days>\d{1,2})d)? ?((?<hours>\d{1,2})hod)? ?((?<minutes>\d{1,2})min)? ?((?<seconds>\d{1,2})s)?~');
//var_dump($params);
//echo $params['seconds'] ?? 0;

class MyIterator extends ArrayIterator {

	public function addBeforeCurrent($element)
	{
		echo "adding: $element" . PHP_EOL;
		$this->offsetSet($this->key(), $element);
		$this->seek($this->key() - 1);
	}

	public function current()
	{
		echo 'getting current: ' . parent::current() . PHP_EOL;
		return parent::current(); // TODO: Change the autogenerated stub
	}

}

class Queue implements IteratorAggregate {

	private $commands;

	/** @var ArrayIterator */
	private $iterator;

	public function __construct($commands)
	{
		$this->commands = $commands;
	}

	public function addBeforeCurrent($element)
	{
		echo "adding: $element" . PHP_EOL;
		array_splice($this->commands, $this->iterator->key(), 0, [$element - 0.5]);
	}

	public function getIterator()
	{
		$this->iterator = new ArrayIterator($this->commands);
		return $this->iterator;
	}
}

//$array = [1,2,3,4,5,6,7,8,9,10];
//$iterator = new Queue($array);
//foreach ($iterator as $index => $item) {
//	if ($item % 2 == 0) {
//		array_splice( $array, $index, 0, [$item - 0.5] );
//		$iterator->addBeforeCurrent($item - 0.5);
////		var_dump($array);
////		$iterator;
//	}
//	echo $item . PHP_EOL;
//}

//$array = [1,2,3,4,5,6,7,8,9,10];
//while (count($array) > 0) {
//	$item = $array[0];
//	if ($item % 2 == 0) {
//		array_splice( $array, 0, 0, [$item - 0.5] );
//		$item = $array[0];
//	}
//	echo $item . PHP_EOL;
//	array_shift($array);
//}//	echo $item . PHP_EOL;


//var_dump(array_merge([1,2], [3], [4,5]));
//var_dump((new \App\Utils\ArrayCollection([1,2,4,5]))->addBefore(3, 2)->toArray());

//var_dump(\Nette\Utils\Strings::replace('C:\xampp\htdocs\bookStore\app\modules\frontModule\presenters', '~(\\\|\\/)modules(\\\|\\/)[a-z]+Module~'));
//var_dump(\Nette\Utils\Strings::replace('C:\xampp\htdocs\bookStore\app\modules\frontModule\presenters', '~(\\\|\\/)~'));
//var_dump(\Nette\Utils\Strings::replace('C:\xampp\htdocs\bookStore\app\modules\frontModule\presenters', '~(\\\|\\/)modules(\\\|\\/)~'));

///** @var \App\Model\Queue\QueueConsumer $consumer */
//$consumer = $container->getByType(\App\Model\Queue\QueueConsumer::class);
//$consumer->processQueue();

class A {
	public static function create()
	{
		return new static;
	}
}
class B extends A {}
//
//var_dump(B::create());

//$time = '20.05.2016 21:54:26';
//$carbon = \Carbon\Carbon::instance(new DateTime($time));
//echo $carbon;

//$ratio = new \App\Model\ValueObject\Resources(3, 2, 1);
//$sum = 0;
//$ratio->forAll(function($e) use (&$sum) {$sum += $e; return true;});
//echo $sum;

///** @var \App\Model\DatabaseManager $databaseManager */
//$databaseManager = $container->getByType(\App\Model\DatabaseManager::class);
///** @var \App\Model\ResourcesCalculator $resourcesCalculator */
//$resourcesCalculator = $container->getByType(\App\Model\ResourcesCalculator::class);
//$planets = $databaseManager->getAllMyPlanets();
//foreach ($planets as $i => $planet) {
//	var_dump($resourcesCalculator->getProductionPerHour($planet));
//}
//var_dump($resourcesCalculator->getProductionPerHour($planet));
//$planet = $databaseManager->getPlanet(new \App\Model\ValueObject\Coordinates(1, 357, 6));

/** @var \Kdyby\Monolog\Logger $logger */
$logger = $container->getByType(\Kdyby\Monolog\Logger::class);
//$logger->addDebug('debug message');
//$logger->addAlert('alert message');

//function nthFleet(int $nth, string $type, bool $returning = null) : string
//{
//	$returnSelector = '';
//	if ($returning !== null) {
//		$returning = $returning ? 'true' : 'false';
//		$returnSelector = "[data-return-flight => $returning]";
//	}
//	return "#eventContent > tbody > tr:nth-of-type($nth)$returnSelector > td$type";
//}
//
//function getNthFleetArrivalTime(int $nth, string $type, bool $returning = null) : string
//{
//	return nthFleet($nth, $type, $returning) . ".countDown";
//}
//
//echo getNthFleetArrivalTime(1, \App\Model\PageObject\FleetInfo::TYPE_ENEMY);
//$fleet = \App\Model\ValueObject\Fleet::fromArray([\App\Enum\Ships::BATTLESHIP => 5, \App\Enum\Ships::DESTROYER => 0]);
//$fleet->addShips(\App\Enum\Ships::_(\App\Enum\Ships::ESPIONAGE_PROBE), 2);
//$fleet->addShips(\App\Enum\Ships::_(\App\Enum\Ships::DEATHSTAR), 0);
//var_dump($fleet->getNonZeroShips());
//var_dump($_SERVER);
//var_dump($_ENV);
//var_dump(strpos("/game/index.php?page=fleet2","/game/index.php?page=fleet3") !== false);


function add(int $a, int $b) : int {
	return $a + $b;
}

//var_dump(add(5,6));
//var_dump(add(5000000,6000000));
//var_dump(add(500000000,600000000));
//var_dump(add(5000000000,6000000000));
//var_dump(add(100000000, 1));
//
$arr = ['one' => 1, 'three' => 'ahoj'];
var_dump($arr['two'] ?? 0);
var_dump($arr['one'] ? 1 : 0);
var_dump($arr['three'] ? 3 : -1);
var_dump($arr['two'] ?: 0);
var_dump(false ?? 0);
var_dump(false ?: 0);
var_dump(null ?? 0);

//$comparator = \App\Utils\Functions::compareCarbonDateTimes();
//var_dump($comparator(\Carbon\Carbon::now(), \Carbon\Carbon::minValue()));
//var_dump(\App\Utils\Functions::compareCarbonDateTimes()(\Carbon\Carbon::now(), \Carbon\Carbon::minValue()));

//$resources = new \App\Utils\ArrayCollection();
//$resources->add(new \App\Model\ValueObject\Resources(10, 20, 30));
//$resources->add(new \App\Model\ValueObject\Resources(100, 200, 300));
//$resources->add(new \App\Model\ValueObject\Resources(4000, 2000, 3000));
//$resources->add(new \App\Model\ValueObject\Resources(1000, 2000, 3000));
//$resources->add(new \App\Model\ValueObject\Resources(0, 0, 0));
//$resources->add(new \App\Model\ValueObject\Resources(100, 200, 100));
//$big = $resources->filter(function (\App\Model\ValueObject\Resources $resources) {return $resources->getTotal() > 500;});
//$bigger = $resources->filter(function (\App\Model\ValueObject\Resources $resources) {return $resources->getTotal() > 5000;});
//$totals = $bigger->map(function (\App\Model\ValueObject\Resources $resources) {return $resources->getTotal();});
//$sorted = $totals->sort(function ($a, $b) {return $a < $b ? -1 : 1;});
//var_dump($big);
//var_dump($bigger);
//var_dump($totals);
//var_dump($sorted);
//var_dump(\Nette\Utils\Json::encode($sorted->toArray()));
//var_dump($sorted->first());

//$didNotGetAllInfo1 = \App\Enum\ProbingStatus::_(\App\Enum\ProbingStatus::DID_NOT_GET_ALL_INFORMATION);
//$didNotGetAllInfo2 = \App\Enum\ProbingStatus::_(\App\Enum\ProbingStatus::DID_NOT_GET_ALL_INFORMATION);
//var_dump($didNotGetAllInfo1 === $didNotGetAllInfo2);
//var_dump($didNotGetAllInfo1 == $didNotGetAllInfo2);
//var_dump(gmp_sign(-2));
//var_dump(gmp_sign(20));
//$orderBy = \App\Enum\OrderPlanetsBy::_(\App\Enum\OrderPlanetsBy::NULL);
//var_dump($orderBy->isActive());
//$interval = new \Carbon\CarbonInterval(0, 0, 0, 0, 0, 1, 0);
//$time = \Carbon\Carbon::minValue()->add($interval);
//var_dump($time);
//$arr = [0,1,2,3,4,5,6];
//var_dump(array_slice($arr, 0, 3));
//var_dump(array_slice($arr, 0, 30));
