<?php
/**
 * Created by PhpStorm.
 * User: Azathoth
 * Date: 19. 5. 2016
 * Time: 1:15
 */

namespace App\Utils;

/**
 * Class ArrayCollection
 * @package App\Utils
 * @method ArrayCollection map(\Closure $func)
 */
class ArrayCollection extends \Doctrine\Common\Collections\ArrayCollection implements Collection
{

	public function prepend(array $array) : Collection
	{
		$this->elements = array_merge($array, $this->elements);
		return $this;
	}

	public function addBefore($element, $key) : Collection
	{
		$before = $this->slice(0, $key);
		$after = $this->slice($key, $this->count() - $key);
		$this->elements = array_merge($before, [$element], $after);
		return $this;
	}

	public function merge(ArrayCollection $another) : ArrayCollection
	{
		$this->elements = array_merge($this->elements, $another->toArray());
		return $this;
	}
}