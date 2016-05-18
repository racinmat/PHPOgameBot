<?php
/**
 * Created by PhpStorm.
 * User: Azathoth
 * Date: 19. 5. 2016
 * Time: 1:15
 */

namespace App\Utils;

interface Collection extends \Doctrine\Common\Collections\Collection
{

	public function prepend(array $array);
	
}