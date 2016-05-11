<?php
/**
 * Created by PhpStorm.
 * User: Azathoth
 * Date: 11. 5. 2016
 * Time: 23:12
 */

namespace app\model\command;


interface ArraySerializable
{
	public function toArray() : array;

	public static function fromArray(array $data);

}