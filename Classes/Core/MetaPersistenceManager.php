<?php
namespace TYPO3\Expose\Core;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.Expose".               *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * FLOW3's MetaPersistenceManager
 *
 * @FLOW3\Scope("singleton")
 * @api
 */
class MetaPersistenceManager {

	/**
	 * @var \TYPO3\FLOW3\Object\ObjectManagerInterface
	 * @FLOW3\Inject
	 */
	protected $objectManager;

	/**
	 * @var array
	 */
	protected $persistenceManager = array();

	/**
	 * Returns the (internal) identifier for the object, if it is known to the
	 * backend. Otherwise NULL is returned.
	 *
	 * Note: this returns an identifier even if the object has not been
	 * persisted in case of AOP-managed entities. Use isNewObject() if you need
	 * to distinguish those cases.
	 *
	 * @param object $object
	 * @return mixed The identifier for the object if it is known, or NULL
	 */
	public function getIdentifierByObject($object) {
		return $this->getPersistenceManagerByObject($object)->getIdentifierByObject($object);
	}

	/**
	 * Checks if the given object has ever been persisted.
	 *
	 * @param object $object The object to check
	 * @return boolean TRUE if the object is new, FALSE if the object exists in the repository
	 */
	public function isNewObject($object) {
		return $this->getPersistenceManagerByObject($object)->isNewObject($object);
	}

	/**
	 * Returns the object with the (internal) identifier, if it is known to the
	 * backend. Otherwise NULL is returned.
	 *
	 * @param mixed $identifier
	 * @param string $objectType
	 * @param boolean $useLazyLoading Set to TRUE if you want to use lazy loading for this object
	 * @return object The object for the identifier if it is known, or NULL
	 */
	public function getObjectByIdentifier($identifier, $objectType = NULL, $useLazyLoading = FALSE) {
		return $this->getPersistenceManagerByClass($objectType)->getObjectByIdentifier($identifier, $objectType, $useLazyLoading);
	}

	/**
	 * @param string $className
	 * @return \TYPO3\FLOW3\Persistence\PersistenceManagerInterface
	 */
	public function getPersistenceManagerByClass($className) {
			// TODO: Actually turn this into some logic to find the suitable manager
		$persistenceManager = '\TYPO3\FLOW3\Persistence\Doctrine\PersistenceManager';
		if (!isset($this->persistenceManager[$persistenceManager])) {
			$this->persistenceManager[$persistenceManager] = $this->objectManager->get($persistenceManager);
		}

		return $this->persistenceManager[$persistenceManager];
	}

	/**
	 * @param object $object
	 * @return \TYPO3\FLOW3\Persistence\PersistenceManagerInterface
	 */
	public function getPersistenceManagerByObject($object) {
		return $this->getPersistenceManagerByClass(get_class($object));
	}

	/**
	 * Adds an object to the persistence.
	 *
	 * @param object $object The object to add
	 * @return void
	 */
	public function add($object) {
		$this->getPersistenceManagerByObject($object)->add($object);
	}

	/**
	 * Return a query object for the given type.
	 *
	 * @param string $type
	 * @return \TYPO3\FLOW3\Persistence\Doctrine\Query
	 */
	public function createQueryForType($type) {
		return $this->getPersistenceManagerByClass($type)->createQueryForType($type);
	}

	/**
	 * Removes an object to the persistence.
	 *
	 * @param object $object The object to remove
	 * @return void
	 */
	public function remove($object) {
		$this->getPersistenceManagerByObject($object)->remove($object);
	}

	/**
	 * Update an object in the persistence.
	 *
	 * @param object $object The modified object
	 * @return void
	 */
	public function update($object) {
		$this->getPersistenceManagerByObject($object)->update($object);
	}
}

?>