<?php
/**
 * Created by PhpStorm.
 * User: Azathoth
 * Date: 19. 5. 2016
 * Time: 1:15
 */

namespace App\Utils;

use Closure;

class ChangesAwareCollection implements Collection
{

	/** @var Collection */
	protected $collection;

	/** @var bool */
	protected $changed;

	public function __construct(Collection $collection)
	{
		$this->collection = $collection;
		$this->changed = false;
	}

	public function toArray()
	{
		return $this->collection->toArray();
	}

	public function first()
	{
		return $this->collection->first();
	}

	public function last()
	{
		return $this->collection->last();
	}

	public function key()
	{
		return $this->collection->key();
	}

	public function next()
	{
		return $this->collection->next();
	}

	public function current()
	{
		return $this->collection->current();
	}

	public function remove($key)
	{
		$this->changed = true;
		return $this->collection->remove($key);
	}

	public function removeElement($element)
	{
		$this->changed = true;
		return $this->collection->removeElement($element);
	}

	public function offsetExists($offset)
	{
		return $this->collection->offsetExists($offset);
	}

	public function offsetGet($offset)
	{
		return $this->collection->offsetGet($offset);
	}

	public function offsetSet($offset, $value)
	{
		$this->changed = true;
		$this->collection->offsetSet($offset, $value);
	}

	public function offsetUnset($offset)
	{
		$this->changed = true;
		$this->collection->offsetUnset($offset);
	}

	public function containsKey($key)
	{
		return $this->collection->containsKey($key);
	}

	public function contains($element)
	{
		return $this->collection->contains($element);
	}

	public function exists(Closure $p)
	{
		return $this->collection->exists($p);
	}

	public function indexOf($element)
	{
		return $this->collection->indexOf($element);
	}

	public function get($key)
	{
		return $this->collection->get($key);
	}

	public function getKeys()
	{
		return $this->collection->getKeys();
	}

	public function getValues()
	{
		return $this->collection->getValues();
	}

	public function count()
	{
		return $this->collection->count();
	}

	public function set($key, $value)
	{
		$this->changed = true;
		$this->collection->set($key, $value);
	}

	public function add($value)
	{
		$this->changed = true;
		return $this->collection->add($value);
	}

	public function isEmpty()
	{
		return $this->collection->isEmpty();
	}

	public function getIterator()
	{
		return $this->collection->getIterator();
	}

	public function map(Closure $func)
	{
		return $this->collection->map($func);
	}

	public function filter(Closure $p)
	{
		return $this->collection->filter($p);
	}

	public function forAll(Closure $p)
	{
		return $this->collection->forAll($p);
	}

	public function partition(Closure $p)
	{
		return $this->collection->partition($p);
	}

	public function clear()
	{
		$this->changed = true;
		$this->collection->clear();
	}

	public function slice($offset, $length = null)
	{
		$this->changed = true;
		return $this->collection->slice($offset, $length);
	}

	public function isChanged() : bool
	{
		return $this->changed;
	}

	public function prepend(array $array)
	{
		$this->changed = true;
		$this->collection->prepend($array);
	}
	
}