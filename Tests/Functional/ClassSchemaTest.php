<?php
namespace Flowpack\Expose\Tests\Functional;

use Flowpack\Expose\Reflection\ClassSchema;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Flowpack.Expose".       *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/**
 */
class ClassSchemaTest extends \TYPO3\Flow\Tests\FunctionalTestCase {
	/**
	 * @test
	 */
	public function basicClassSchemaTest() {
		$schema = new ClassSchema('\Flowpack\Expose\Tests\Functional\Fixtures\DummyEntity');

		$this->assertEquals(
			$schema->getPropertyNames(),
			array("dummyChildEntities", "dummyChildEntity", "name")
		);

		$this->assertEquals(
			$schema->getListProperties(),
			array("name")
		);

		$this->assertEquals(
			$schema->getSearchProperties(),
			array("name")
		);

		$this->assertEquals(
			$schema->getFilterProperties(),
			array("dummyChildEntity")
		);

		$this->assertEquals(
			$schema->getDefaultSortBy(),
			NULL
		);

		$this->assertEquals(
			$schema->getDefaultOrder(),
			NULL
		);

		$this->assertEquals(
			$schema->getListProcessors(),
			array(
				'\Flowpack\Expose\Processors\SearchProcessor' => TRUE,
				'\Flowpack\Expose\Processors\FilterProcessor' => TRUE,
				'\Flowpack\Expose\Processors\PaginationProcessor' => TRUE,
				'\Flowpack\Expose\Processors\SortProcessor' => TRUE
			)
		);
	}

	/**
	 * @test
	 */
	public function getPropertyTest() {
		$schema = new ClassSchema('\Flowpack\Expose\Tests\Functional\Fixtures\DummyEntity');

		$property = $schema->getProperty('name');
		$this->assertEquals($property->getName(), 'name');
		$this->assertEquals($property->getLabel(), 'Name');
		$this->assertEquals($property->getPosition(), '300');
		$this->assertEquals($property->getInfotext(), '');
		$this->assertEquals($property->getType(), 'string');
		$this->assertEquals($property->getElementType(), null);
		$this->assertEquals($property->getControl(), 'Textfield');
		$this->assertEquals($property->getClassName(), '\Flowpack\Expose\Tests\Functional\Fixtures\DummyEntity');
	}

	/**
	 * @test
	 */
	public function getPropertyOfChildEntityTest() {
		$schema = new ClassSchema('\Flowpack\Expose\Tests\Functional\Fixtures\DummyEntity');

		$property = $schema->getProperty('dummyChildEntity.name');
		$this->assertEquals($property->getClassName(), '\Flowpack\Expose\Tests\Functional\Fixtures\DummyChildEntity');
	}
}
?>