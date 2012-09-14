<?php
namespace TYPO3\Expose\Core;

/*                                                                        *
 * This script belongs to the TYPO3.Expose package.              		  *
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
     * @api
     * @todo improve try/catch block
     */
    public function getIdentifierByObject($object) {
        return $this->getPersitenceManagerByObject($object)->getIdentifierByObject($object);
    }

    /**
     * Checks if the given object has ever been persisted.
     *
     * @param object $object The object to check
     * @return boolean TRUE if the object is new, FALSE if the object exists in the repository
     * @api
     */
    public function isNewObject($object) {
        return $this->getPersitenceManagerByObject($object)->isNewObject($object);
    }

    /**
     * Returns the object with the (internal) identifier, if it is known to the
     * backend. Otherwise NULL is returned.
     *
     * @param mixed $identifier
     * @param string $objectType
     * @param boolean $useLazyLoading Set to TRUE if you want to use lazy loading for this object
     * @return object The object for the identifier if it is known, or NULL
     * @throws \RuntimeException
     * @api
     */
    public function getObjectByIdentifier($identifier, $objectType = NULL, $useLazyLoading = FALSE) {
        return $this->getPersitenceManagerByClass($objectType)->getObjectByIdentifier($identifier, $objectType, $useLazyLoading);
    }

    /**
    * TODO: Document this Method! ( getPersitenceManagerByClass )
    */
    public function getPersitenceManagerByClass($class) {
        // TODO: Actually turn this into some logic to find the suitable manager
        $persistenceManager = '\\TYPO3\\FLOW3\\Persistence\\Doctrine\\PersistenceManager';
        if (!isset($this->persistenceManager[$persistenceManager])) {
            $this->persistenceManager[$persistenceManager] = $this->objectManager->get($persistenceManager);
        }
        return $this->persistenceManager[$persistenceManager];
    }

    /**
    * TODO: Document this Method! ( getPersitenceManagerByObject )
    */
    public function getPersitenceManagerByObject($object) {
        return $this->getPersitenceManagerByClass(get_class($object));
    }

    /**
     * Adds an object to the persistence.
     *
     * @param object $object The object to add
     * @return void
     * @throws \TYPO3\FLOW3\Persistence\Exception\KnownObjectException if the given $object is not new
     * @throws \TYPO3\FLOW3\Persistence\Exception if another error occurs
     * @api
     */
    public function add($object) {
        return $this->getPersitenceManagerByObject($object)->add($object);
    }

    /**
     * Return a query object for the given type.
     *
     * @param string $type
     * @return \TYPO3\FLOW3\Persistence\Doctrine\Query
     */
    public function createQueryForType($type) {
        return $this->getPersitenceManagerByClass($type)->createQueryForType($type);
    }

    /**
     * Removes an object to the persistence.
     *
     * @param object $object The object to remove
     * @return void
     * @api
     */
    public function remove($object) {
        return $this->getPersitenceManagerByObject($object)->remove($object);
    }

    /**
     * Update an object in the persistence.
     *
     * @param object $object The modified object
     * @return void
     * @throws \TYPO3\FLOW3\Persistence\Exception\UnknownObjectException if the given $object is new
     * @throws \TYPO3\FLOW3\Persistence\Exception if another error occurs
     * @api
     */
    public function update($object) {
        return $this->getPersitenceManagerByObject($object)->update($object);
    }

}

?>