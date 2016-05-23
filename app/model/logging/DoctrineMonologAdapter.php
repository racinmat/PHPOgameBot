<?php
/**
 * Created by PhpStorm.
 * User: Azathoth
 * Date: 23. 5. 2016
 * Time: 13:39
 */

namespace App\Model\Logging;


use Doctrine\DBAL\Logging\SQLLogger;
use Monolog\Logger;

class DoctrineMonologAdapter implements SQLLogger
{

	/** @var  Logger */
	private $logger;

	public function __construct(Logger $logger)
	{
		$this->logger = $logger;
	}


	public function startQuery($sql, array $params = null, array $types = null)
	{
		$paramsString = implode(', ', $params);
		$typesString = implode(', ', $types);
		$this->logger->addDebug("SQL: $sql, params: $paramsString, types: $typesString");
	}

	public function stopQuery()
	{

	}
}