<?php
namespace TYPO3\Expose\Tests\Functional\Actions;

/*                                                                        *
 * This script belongs to the TYPO3.Expose package.              *
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
use Symfony\Component\DomCrawler\Crawler;

/**
 * Testcase for Standalone View
 *
 * @group large
 */
class ListControllerTest extends \TYPO3\FLOW3\Tests\FunctionalTestCase {

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
		$route->setUriPattern('test/expose/actions(/{@action})');
		$route->setDefaults(array(
			'@package' => 'TYPO3.Expose',
			'@subpackage' => 'Tests\Functional\Actions\Fixtures',
			'@controller' => 'Actions',
			'@action' => 'index',
			'@format' =>'html'
		));
		$route->setAppendExceedingArguments(TRUE);
		$this->router->addRoute($route);
	}

	public function createDummyPost() {
		$post = new Fixtures\Domain\Model\Post();
		$post->setEmail('foo@bar.org');
		$post->setName('myName');
		$this->persistenceManager->add($post);
		$postIdentifier = $this->persistenceManager->getIdentifierByObject($post);
		$this->persistenceManager->persistAll();
		$this->persistenceManager->clearState();

		return $postIdentifier;
	}

	public function callAction($uriArguments) {
		$class = "TYPO3\Expose\Tests\Functional\Actions\Fixtures\Domain\Model\Post";
		return $this->browser->request('http://localhost/test/expose/actions?' . http_build_query($uriArguments));
	}

	/**
	 * @test
	 */
	public function postListIsRendered() {
		$this->createDummyPost();

		$uriArguments = array(
			'--featureRuntime' => array(
				'being' => 'TYPO3\Expose\Tests\Functional\Actions\Fixtures\Domain\Model\Post',
				'@action' => 'index',
				'@controller' => 'list',
				'@package' => 'typo3.expose'
			)
		);
		$class = "TYPO3\Expose\Tests\Functional\Actions\Fixtures\Domain\Model\Post";
		$this->browser->request('http://localhost/test/expose/actions?' . http_build_query($uriArguments));

		$content = $this->browser->getLastResponse()->getContent();

		$this->assertTrue((boolean) stristr($content, "foo@bar.org"));
	}

}
?>