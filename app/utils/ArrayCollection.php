<?php
/**
 * Created by PhpStorm.
 * User: Azathoth
 * Date: 19. 5. 2016
 * Time: 1:15
 */

namespace App\Utils;

class ArrayCollection extends \Doctrine\Common\Collections\ArrayCollection implements Collection
{

	public function prepend(array $array)
	{
		$this->elements = array_merge($array, $this->elements);
	}
}