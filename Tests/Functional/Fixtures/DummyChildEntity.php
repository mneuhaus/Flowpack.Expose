<?php
namespace Flowpack\Expose\Tests\Functional\Fixtures;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Flowpack.Expose".       *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Doctrine\ORM\Mapping as ORM;
use TYPO3\Flow\Annotations as Flow;

/**
 * @Flow\Entity
 */
class DummyChildEntity {
	/**
     * @var \Flowpack\Expose\Tests\Functional\Fixtures\DummyEntity
     * @ORM\ManyToOne(inversedBy="dummyChildEntity")
     */
    protected $dummyEntity;

    /**
     * @var string
     */
    protected $name;
}
?>