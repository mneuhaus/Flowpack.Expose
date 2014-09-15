<?php
namespace Flowpack\Expose\OptionsProvider;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Flowpack.Expose".       *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Flowpack\Expose\Core\OptionsProvider\AbstractOptionsProvider;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Reflection\ObjectAccess;

/**
 * This OptionsProvider is used to fetch entities based on the orm relation of a property.
 */
class RelationOptionsProvider extends AbstractOptionsProvider {
	/**
	 * This contains the supported settings, their default values, descriptions and types.
	 *
	 * @var array
	 */
	protected $supportedSettings = array(
		'QueryMethod' => array(
			'default' => 'createQuery',
			'description' => 'Method to call on the Repository to create a query',
			'required' => FALSE
		),
		'EmptyOption' => array(
			'default' => NULL,
			'description' => 'Set this setting to add an emtpy option to the beginning of the options',
			'required' => FALSE
		),
		'LabelPath' => array(
			'default' => NULL,
			'description' => 'Set this setting to fetch the displayed label by a specific property/path',
			'required' => FALSE
		)
	);

	/**
	 * @var \TYPO3\Flow\Persistence\PersistenceManagerInterface
	 * @Flow\Inject
	 */
	protected $persistenceManager;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Reflection\ReflectionService
	 */
	protected $reflectionService;

	/**
	 * @var \TYPO3\Flow\Object\ObjectManager
	 * @Flow\Inject
	 */
	protected $objectManager;

	/**
	*/
	public function getOptions() {
		$classSchema = $this->reflectionService->getClassSchema($this->getRelationClass());
		if ($classSchema->getRepositoryClassName() !== NULL) {
			$repository = $this->objectManager->get($classSchema->getRepositoryClassName());
			$query = call_user_func(array($repository, $this->settings['QueryMethod']));
		} else {
			$query = $this->persistenceManager->createQueryForType($this->getRelationClass());
		}

		$options = $query->execute()->toArray();

		if ($this->settings['LabelPath'] !== NULL) {
			$options = array();
			foreach ($query->execute() as $option) {
				$identifier = $this->persistenceManager->getIdentifierByObject($option);
				$label = ObjectAccess::getPropertyPath($option, $this->settings['LabelPath']);
				$options[$identifier] = $label;
			}
		}

		if ($this->settings['EmptyOption'] !== NULL) {
			$newOptions = array('' => $this->settings['EmptyOption']);
			foreach ($options as $key => $value) {
				$newOptions[$key] = $value;
			}
			$options = $newOptions;
		}

		return $options;
	}

	public function getRelationClass() {
		if ($this->propertySchema->getElementType() !== NULL) {
			return $this->propertySchema->getElementType();
		}
		return $this->propertySchema->getType();
	}
}

?>