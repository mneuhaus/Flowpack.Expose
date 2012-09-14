<?php
namespace TYPO3\Expose\PersistentStorageAdapter;

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
 * PersistentStorageAdapter for the Doctrine engine
 *
 * @FLOW3\Scope("singleton")
 */
class NodePersistenceManager implements \TYPO3\FLOW3\Persistence\PersistenceManagerInterface {

	/**
	 * @param array $settings
	 * @return void
	 */
	public function injectSettings(array $settings) {
	}

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
		if (!$object instanceof \TYPO3\TYPO3CR\Domain\Model\NodeInterface) {
			throw new \Exception('TODO: NodePersistenceManager can only work with node objects');
		}

		return $object->getContextPath();
	}

	/**
	 * Checks if the given object has ever been persisted.
	 *
	 * @param object $object The object to check
	 * @return boolean TRUE if the object is new, FALSE if the object exists in the repository
	 */
	public function isNewObject($object) {
		return FALSE;
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
	public function getObjectByIdentifier($identifier, $objectType = NULL, $useLazyLoading = FALSE) {}

	/**
	 * Adds an object to the persistence.
	 *
	 * @param object $object The object to add
	 * @return void
	 */
	public function add($object) {
	}

	/**
	 * Clears the in-memory state of the persistence.
	 * Managed instances become detached, any fetches will
	 * return data directly from the persistence "backend".
	 *
	 * @return void
	 */
	public function clearState() {
	}

	/**
	 * Converts the given object into an array containing the identity of the domain object.
	 *
	 * @param object $object The object to be converted
	 * @return array The identity array in the format array('__identity' => '...')
	 */
	public function convertObjectToIdentityArray($object) {
		return array('__identity' => $this->getIdentifierByObject($object));
	}

	/**
	 * Recursively iterates through the given array and turns objects
	 * into arrays containing the identity of the domain object.
	 *
	 * @param array $array The array to be iterated over
	 * @return array The modified array without objects
	 * @see convertObjectToIdentityArray()
	 */
	public function convertObjectsToIdentityArrays(array $array) {
	}

	/**
	 * Return a query object for the given type.
	 *
	 * @param string $type
	 * @return \TYPO3\FLOW3\Persistence\QueryInterface
	 */
	public function createQueryForType($type) {
	}

	/**
	 * Initializes the persistence manager, called by FLOW3.
	 *
	 * @return void
	 */
	public function initialize() {
	}

	/**
	 * Commits new objects and changes to objects in the current persistence
	 * session into the backend
	 *
	 * @return void
	 */
	public function persistAll() {
	}

	/**
	 * Registers an object which has been created or cloned during this request.
	 * The given object must contain the FLOW3_Persistence_Identifier property, thus
	 * the PersistenceMagicInterface type hint. A "new" object does not necessarily
	 * have to be known by any repository or be persisted in the end.
	 * Objects registered with this method must be known to the getObjectByIdentifier()
	 * method.
	 *
	 * @param \TYPO3\FLOW3\Persistence\Aspect\PersistenceMagicInterface $object The new object to register
	 * @return void
	 */
	public function registerNewObject(\TYPO3\FLOW3\Persistence\Aspect\PersistenceMagicInterface $object) {
	}

	/**
	 * Removes an object to the persistence.
	 *
	 * @param object $object The object to remove
	 * @return void
	 */
	public function remove($object) {
	}

	/**
	 * Update an object in the persistence.
	 *
	 * @param object $object The modified object
	 * @return void
	 * @throws \TYPO3\FLOW3\Persistence\Exception\UnknownObjectException
	 */
	public function update($object) {
	}
}

?>