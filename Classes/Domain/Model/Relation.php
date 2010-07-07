<?php

namespace F3\Admin\Domain\Model;

/*                                                                        *
 * This script belongs to the FLOW3 package "Contacts".                   *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License as published by the Free   *
 * Software Foundation, either version 3 of the License, or (at your      *
 * option) any later version.                                             *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General      *
 * Public License for more details.                                       *
 *                                                                        *
 * You should have received a copy of the GNU General Public License      *
 * along with the script.                                                 *
 * If not, see http://www.gnu.org/licenses/gpl.html                       *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/**
 * A Adress
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @scope prototype
 * @entity
 * @autoadmin
 */
class Relation extends \F3\Admin\Domain\Model{
	/**
	 * @var \F3\Admin\Domain\Model\Tag
	 */
	protected $tag;
	
	/**
	 * @var \SplObjectStorage<F3\Admin\Domain\Model\Tag>
	 */
	protected $tags;

    /**
	 * @var \F3\Admin\Domain\Model\Widgets
     * @inline
	 */
	protected $inlineWidget;

	/**
	 * @var \SplObjectStorage<F3\Admin\Domain\Model\Widgets>
     * @inline
	 */
	protected $inlineWidgets;

    /**
	 * @var \F3\Admin\Domain\Model\Info
     * @infotext This Property has no Repository and Belongs to its Parent Object
	 */
	protected $info;

	/**
	 * @var \SplObjectStorage<F3\Admin\Domain\Model\Info>
     * @infotext This Property has no Repository and Belongs to its Parent Object
	 */
	protected $infos;
	
	public function __construct() {
		$this->tags = new \SplObjectStorage();
		$this->inlineWidgets = new \SplObjectStorage();
		$this->infos = new \SplObjectStorage();
	}
}

?>