<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use Codeception\Module\WebDriver;

class Acceptance extends \Codeception\Module
{

	public function seeElementExists($selector, $attributes = [])
	{
		/** @var WebDriver $webDriver */
		$webDriver =  $this->getModule('WebDriver')->webDriver;
		$els = $webDriver->matchVisible($selector);
		$els = $webDriver->filterByAttributes($els, $attributes);
		return count($els) > 0;
	}
}
