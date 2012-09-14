<?php
namespace TYPO3\Expose\Tests\Functional;

/*                                                                        *
 * This script belongs to the TYPO3.Expose package.              *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 *  of the License, or (at your option) any later version.                *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/**
 * TypoScript stringFormatter test
 */
class StringFormatterTest extends \TYPO3\FLOW3\Tests\FunctionalTestCase {

	public function dataSourceForFormatter() {
		return array(
			'string' => array(
				'source' => 'Some string value',
				'expected' => 'Some string value'
			),
			'integer' => array(
				'source' => 42,
				'expected' => '42'
			),
			'float is converted to float with max two decimal positions' => array(
				'source' => 42.34664,
				'expected' => '42.35'
			),
			'boolean TRUE' => array(
				'source' => TRUE,
				'expected' => 'true'
			),
			'boolean FALSE' => array(
				'source' => FALSE,
				'expected' => 'false'
			),
			'collection array' => array(
				'source' => array(TRUE, 'My String', 42.456),
				'expected' => 'true, My String, 42.46'
			),
			'DateTime' => array(
				'source' => new \DateTime('2010-01-28T15:00:00+02:00'),
				'expected' => '2010-01-28 15:00'
			)
		);
	}

	/**
	 * @test
	 * @dataProvider dataSourceForFormatter
	 */
	public function formatterWorks($source, $expected) {
		$view = $this->getView();

		$view->assign('value', $source);
		$view->setTypoScriptPath('/test<TYPO3.Expose:StringFormatter>');
		$this->assertEquals($expected, $view->render());
	}

	protected function getView() {
		$view = new \TYPO3\TypoScript\View\TypoScriptView();
		$view->setPackageKey('TYPO3.Expose');
		$view->disableFallbackView();

		$mockControllerContext = $this->getMockBuilder('TYPO3\FLOW3\Mvc\Controller\ControllerContext')->disableOriginalConstructor()->getMock();
		$view->setControllerContext($mockControllerContext);

		return $view;
	}
}
?>