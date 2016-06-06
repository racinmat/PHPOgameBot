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
use Nette\Utils\Strings;


class DoctrineMonologAdapter implements SQLLogger
{

	const
		SELECT = 'SELECT',
		INSERT = 'INSERT',
		UPDATE = 'UPDATE',
		DELETE = 'DELETE',
		MERGE = 'MERGE',
		CALL = 'CALL',
		EXPLAIN_PLAN = 'EXPLAIN PLAN',
		LOCK_TABLE = 'LOCK TABLE'
	;

	/** @var  Logger */
	private $logger;

	/** @var string[] */
	private $types;

	public function __construct(Logger $logger, array $types)
	{
		$this->logger = $logger;
		$this->types = $types;
	}


	public function startQuery($sql, array $params = null, array $types = null)
	{
		$toBeLogged = false;
		foreach ($this->types as $type) {
			if (Strings::contains($sql, $type)) {
				$toBeLogged = true;
				break;
			}
		}

		if ( ! $toBeLogged) {
			return;
		}

		$string = "SQL: $sql";
		if ($params) {
			$paramsString = implode(', ', $params);
			$string .= ", params: $paramsString";
		}
		if ($types) {
			$typesString = implode(', ', $types);
			$string .= ", types: $typesString";
		}
		$this->logger->addDebug($string);
	}

	public function stopQuery()
	{

	}
}