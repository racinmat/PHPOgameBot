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
 * @method ArrayCollection filter(\Closure $func)
 */
class ArrayCollection extends \Doctrine\Common\Collections\ArrayCollection
{

	public function prepend(array $array) : ArrayCollection
	{
		$this->elements = array_merge($array, $this->elements);
		return $this;
	}

	public function addBefore($element, $key) : ArrayCollection
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

	public function sort(callable $comparator) : ArrayCollection
	{
		usort($this->elements, $comparator);
		return $this;
	}

}