<?php
 
namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities;
use Nette\Object;

/**
 * @ORM\Entity
 */
class QueueItem extends Object
{

	const
		ACTION_BUILD = 'build'
	;

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 * @var integer
	 */
	private $id;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $action;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $data;

	/**
	 * QueueItem constructor.
	 * @param string $action
	 * @param string $data
	 */
	public function __construct($action, $data)
	{
		$this->action = $action;
		$this->data = $data;
	}

	/**
	 * @return string
	 */
	public function getAction()
	{
		return $this->action;
	}

	/**
	 * @return string
	 */
	public function getData()
	{
		return $this->data;
	}


}
