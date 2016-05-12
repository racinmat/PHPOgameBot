<?php
/**
 * Created by PhpStorm.
 * User: Azathoth
 * Date: 11. 5. 2016
 * Time: 23:12
 */

namespace App\Model\Queue\Command;


interface ArraySerializable
{
	public function toArray() : array;

	public static function fromArray(array $data);

}