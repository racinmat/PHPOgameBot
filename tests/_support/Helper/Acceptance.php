<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use Codeception\Module\WebDriver;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverElement;

class Acceptance extends \Codeception\Module
{

	public function seeElementExists($selector, $attributes = [])
	{
		/** @var WebDriver $webDriver */
		$webDriver = $this->getModule('WebDriver');
		$els = $webDriver->matchVisible($selector);
		$els = $webDriver->filterByAttributes($els, $attributes);
		return count($els) > 0;
	}

	/**
	 * @param $selector
	 * @return int
	 * @throws \Codeception\Exception\ModuleException
	 */
	public function getNumberOfElements($selector)
	{
		/** @var WebDriver $webDriver */
		$webDriver = $this->getModule('WebDriver');
		return count($webDriver->matchVisible($selector));
	}

	public function seeExists($text, $selector = null)
	{
		/** @var WebDriver $webDriver */
		$webDriver = $this->getModule('WebDriver');
		if (!$selector) {
			//vykucháno z PHPUnit, jádro assertPageContains
			return stripos(htmlspecialchars_decode($webDriver->getVisibleText()), $text) !== false;
		}
		$nodes = $webDriver->matchVisible($selector);
		//vykucháno z PHPUnit, jádro assertNodesContain
		if (!count($nodes)) {
			return false;
		}
		if ($text === '') {
			return true;
		}

		foreach ($nodes as $node) {
			/** @var $node \WebDriverElement  * */
			if (!$node->isDisplayed()) {
				continue;
			}
			if (stripos(htmlspecialchars_decode($node->getText()), $text) !== false) {
				return true;
			}
		}
		return false;

	}

	public function seeInCurrentUrlExists($uri)
	{
		/** @var WebDriver $webDriver */
		$webDriver = $this->getModule('WebDriver');
		$currentUri = $webDriver->_getCurrentUri();
		return strpos($currentUri, $uri) !== false;
	}

	public function closeBrowser()
	{
		/** @var WebDriver $webDriverModule */
		$webDriverModule =  $this->getModule('WebDriver');
		/** @var RemoteWebDriver $webDriver */
		$webDriver = $webDriverModule->webDriver;
		$webDriver->close();
	}

	public function logFailedAction(string $logDirectory, string $filename)
	{
		/** @var WebDriver $webDriverModule */
		$webDriverModule =  $this->getModule('WebDriver');
		$webDriverModule->_saveScreenshot("$logDirectory/$filename.png");
		$webDriverModule->_savePageSource("$logDirectory/$filename.html");

	}
}
