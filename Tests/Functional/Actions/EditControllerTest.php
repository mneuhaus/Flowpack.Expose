<?php
namespace Foo\ContentManagement\Tests\Functional\Actions;

/*                                                                        *
 * This script belongs to the Foo.ContentManagement package.              *
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
class EditControllerTest extends \TYPO3\FLOW3\Tests\FunctionalTestCase {

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
		$route->setUriPattern('test/contentmanagement/actions(/{@action})');
		$route->setDefaults(array(
			'@package' => 'Foo.ContentManagement',
			'@subpackage' => 'Tests\Functional\Actions\Fixtures',
			'@controller' => 'Actions',
			'@action' => 'index',
			'@format' =>'html'
		));
		$route->setAppendExceedingArguments(TRUE);
		$this->router->addRoute($route);
	}

	public function callAction($uriArguments) {
		$class = "Foo\ContentManagement\Tests\Functional\Actions\Fixtures\Domain\Model\Post";
		return $this->browser->request('http://localhost/test/contentmanagement/actions?' . http_build_query($uriArguments));
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


	/**
	 * @test
	 */
	public function editPostFormIsRendered() {
		return;
		$identifier = $this->createDummyPost();

		$response = $this->callAction(array(
			'--actionRuntime' => array(
				'being' => 'Foo\ContentManagement\Tests\Functional\Actions\Fixtures\Domain\Model\Post',
				'id' => $identifier,
				'@action' => 'index',
				'@controller' => 'edit',
				'@package' => 'foo.contentmanagement'
			)
		));

		$form = $this->browser->getForm();
		$this->assertTrue($form->has("contentForm[item][name]"));
		$this->assertEquals("myName", $form->get("contentForm[item][name]")->getValue());

		$this->assertTrue($form->has("contentForm[item][email]"));
		$this->assertEquals("foo@bar.org", $form->get("contentForm[item][email]")->getValue());
	}

	/**
	 * @test
	 */
	public function editedPostIsSavedFromEditController() {
		$identifier = $this->createDummyPost();

		$response = $this->callAction(array(
			'--actionRuntime' => array(
				'being' => 'Foo\ContentManagement\Tests\Functional\Actions\Fixtures\Domain\Model\Post',
				'id' => $identifier,
				'@action' => 'index',
				'@controller' => 'edit',
				'@package' => 'foo.contentmanagement'
			)
		));

		$content = $response->getContent();

		$form = $this->browser->getForm();
		$form["--contentForm[item][name]"] = "Tony Tester";
		$form["--contentForm[item][email]"] = "tony@tester.com";
		$response = $this->browser->submit($form);

		// $response = $this->callAction(array(
		// 	'--actionRuntime' => array(
		// 		'being' => 'Foo\ContentManagement\Tests\Functional\Actions\Fixtures\Domain\Model\Post',
		// 		'@action' => 'index',
		// 		'@controller' => 'list',
		// 		'@package' => 'foo.contentmanagement'
		// 	)
		// ));
		
		$content = $response->getContent();
		#var_dump($content);
		#$this->assertTrue((boolean) stristr($content, "foo@bar.org"));
	}
}
?>