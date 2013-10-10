<?php
namespace TYPO3\Expose\Tests\Functional;

use Doctrine\Common\Collections\ArrayCollection;
use TYPO3\Expose\Tests\Functional\Fixtures\ClassWithToString;
use TYPO3\Expose\Tests\Functional\Fixtures\ClassWithoutToString;
use TYPO3\Expose\Utility\StringRepresentation;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Expose".               *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/**
 * TypoScript stringFormatter test
 */
class StringFormatterTest extends \TYPO3\Flow\Tests\FunctionalTestCase {

	/**
	 * @return array
	 */
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
	 */
	public function stringRepresentationWorks() {
		$this->assertEquals(
			'foo',
			StringRepresentation::convert('foo')
		);

		$this->assertEquals(
			'42',
			StringRepresentation::convert(42)
		);

		$this->assertEquals(
			'42.34664',
			StringRepresentation::convert(42.34664)
		);

		$this->assertEquals(
			'true',
			StringRepresentation::convert(TRUE)
		);

		$this->assertEquals(
			'false',
			StringRepresentation::convert(FALSE)
		);

		$dateTime = new \DateTime('2010-01-28T15:00:00+02:00');
		$this->assertEquals(
			'foo, bar, ' . $dateTime->format(\DateTime::W3C),
			StringRepresentation::convert(array(
				'foo',
				'bar',
				$dateTime
			))
		);

		$classWithToString = new ClassWithToString();
		$this->assertEquals(
			'ClassWithToString',
			StringRepresentation::convert($classWithToString)
		);

		$classWithoutToString = new ClassWithoutToString();
		$this->assertEquals(
			'<ClassWithoutToString: ' . spl_object_hash($classWithoutToString) . '>',
			StringRepresentation::convert($classWithoutToString)
		);


		$collection = new ArrayCollection();
		$collection->add($classWithToString);
		$collection->add($classWithToString);
		$this->assertEquals(
			'ClassWithToString, ClassWithToString',
			StringRepresentation::convert($collection)
		);

		$resource = fopen('php://memory', 'rb');
		$this->assertEquals(
			'<resource: stream #' . intval($resource) . ' rb>',
			StringRepresentation::convert($resource)
		);

		$resource = stream_context_create();
		$this->assertEquals(
			'<resource: stream-context #' . intval($resource) . '>',
			StringRepresentation::convert($resource)
		);
	}
}
?>