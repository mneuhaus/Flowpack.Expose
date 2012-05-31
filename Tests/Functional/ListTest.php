<?php
namespace Foo\ContentManagement\Tests\Functional;

/*                                                                        *
 * This script belongs to the FLOW3 package "Fluid".                      *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 *  of the License, or (at your option) any later version.                *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\FLOW3\Http\Request;
use TYPO3\FLOW3\Http\Uri;
use TYPO3\FLOW3\Mvc\ActionRequest;

/**
 * Testcase for Standalone View
 *
 * @group large
 */
class ListTest extends \TYPO3\FLOW3\Tests\FunctionalTestCase {

	/**
	 * @var boolean
	 */
	protected $testableHttpEnabled = TRUE;

	/**
	 * @var boolean
	 */
	static protected $testablePersistenceEnabled = TRUE;

	/**
	 * @var \TYPO3\FLOW3\Http\Client\Browser
	 */
	protected $browser;

	/**
	 * Initializer
	 */
	public function setUp() {
		parent::setUp();

		$route = new \TYPO3\FLOW3\Mvc\Routing\Route();
		$route->setUriPattern('test/fluid/formobjects(/{@action})');
		$route->setDefaults(array(
			'@package' => 'TYPO3.Fluid',
			'@subpackage' => 'Tests\Functional\Form\Fixtures',
			'@controller' => 'Form',
			'@action' => 'index',
			'@format' =>'html'
		));
		$route->setAppendExceedingArguments(TRUE);
		$this->router->addRoute($route);
	}

	/**
	 * @test
	 */
	public function objectIsCreatedCorrectly() {
		$this->browser->request('http://localhost/test/fluid/formobjects');
		$form = $this->browser->getForm();

		$form['post']['name']->setValue('Egon Olsen');
		$form['post']['email']->setValue('test@typo3.org');

		$response = $this->browser->submit($form);
		$this->assertSame('Egon Olsen|test@typo3.org', $response->getContent());
	}
}
?>