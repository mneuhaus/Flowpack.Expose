<?php
namespace TYPO3\Expose\Controller;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.Expose".               *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Doctrine\ORM\Mapping as ORM;
use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * Action to create a new Being
 *
 */
class NewController extends \TYPO3\Expose\Core\AbstractExposeController {

	/**
	 * Create a new object
	 *
	 * @param string $type
	 * @return void
	 */
	public function indexAction($type) {
		$objects = array(new $type());
		$this->view->assign('className', $type);
		$this->view->assign('objects', $objects);
		$this->view->assign('callback', 'create');
	}

	/**
	 * @return void
	 */
	public function initializeCreateAction() {
		$this->arguments['objects']->setDataType('Doctrine\Common\Collections\Collection<' . $this->request->getArgument('type') . '>');
		$propertyMappingConfiguration = $this->arguments['objects']->getPropertyMappingConfiguration();
		$propertyMappingConfiguration->allowAllProperties();
		foreach ($this->request->getArgument('objects') as $index => $tmp) {
			$propertyMappingConfiguration
				->forProperty($index)
				->allowAllProperties()
				->setTypeConverterOption('TYPO3\FLOW3\Property\TypeConverter\PersistentObjectConverter', \TYPO3\FLOW3\Property\TypeConverter\PersistentObjectConverter::CONFIGURATION_MODIFICATION_ALLOWED, TRUE);
		}

	}
	/**
	 * @param string $type
	 * @param \Doctrine\Common\Collections\Collection $objects
	 * @return void
	 */
	public function createAction($type, $objects) {
		foreach ($objects as $object) {
			$this->persistenceManager->add($object);
		}
		$this->redirect('index', 'sametypelist', 'TYPO3.Expose', array('type' => $type));
	}

}

?>