<?php
/**
 * Created by PhpStorm.
 * User: Azathoth
 * Date: 11. 6. 2015
 * Time: 9:50
 * Copyright © 2014, Matěj Račinský. Všechna práva vyhrazena.
 */

namespace App\Enum;


use Nette\Object;


/**
 * Class Enum
 * @package App\Utils
 * @author: Matěj Račinský 
 */
abstract class Enum extends Object {

	/**
	 * @var Enum[][]
	 */
	protected static $instances = [];

	protected $value;

	private function __construct($value)
	{
		$this->value = $value;
	}

	protected static function getConstants()
	{
		$reflection = static::getReflection();
		$array = $reflection->constants;
		asort($array);
		return $array;
	}

	/**
	 * @param $value
	 * @return static
	 */
	public static function newInstance($value)
	{
		$instance = static::getInstanceIfExists($value);
		if ($instance !== false) {
			return $instance;
		}

		if (in_array($value, static::getConstants())) {
			foreach (static::getEnumValues() as $enumValue) {
				if ($enumValue == $value) {
					$value = $enumValue;
					break;
				}
			}
			$instance = new static($value);
			static::addInstance($instance);
			return $instance;
		} else {
			throw new \InvalidArgumentException('Wrong value of enum.');
		}
	}

	/**
	 * short alias for newInstance factory function
	 * @param $value
	 * @return Enum
	 */
	public static function _($value)
	{
		return static::newInstance($value);
	}

	/**
	 * Returns already created instance of enum value or returns false
	 * @param $value
	 * @return static|bool
	 */
	private static function getInstanceIfExists($value)
	{
		if (!static::getInstances()) {
			return FALSE;
		}
		foreach (static::getInstances() as $instance) {
			if ($instance->value === $value) {
				return $instance;
			}
		}
		return FALSE;
	}

	/**
	 * @return mixed
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * @return static[]
	 */
	public static function getEnums()
	{
		foreach (static::getEnumValues() as $value) {
			static::newInstance($value);
		}
		return static::getInstances();
	}

	private static function getInstances()
	{
		if (isset(static::$instances[static::class])) {
			asort(static::$instances[static::class]);
			return static::$instances[static::class];
		} else {
			return NULL;
		}
	}

	private static function addInstance(Enum $instance)
	{
		static::$instances[static::class][] = $instance;
	}

	/**
	 * @return string[]
	 */
	public static function getEnumValues() : array 
	{
		return static::getConstants();
	}

	public static function getSelectBoxValues() : array
	{
		return array_combine(static::getEnumValues(), static::getEnumValues());
	}

	public function __toString() : string
	{
		return $this->getValue();
	}
	
}