<?php
namespace Foo\ContentManagement\PersistentStorageAdapter;

/*                                                                        *
 * This script belongs to the Foo.ContentManagement package.              *
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

use Doctrine\ORM\Mapping as ORM;
use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * PersistentStorageAdapter for the Doctrine engine
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 *
 * @FLOW3\Scope("singleton")
 */
class DoctrinePersistentStorageAdapter extends \Foo\ContentManagement\Core\PersistentStorageAdapter\AbstractPersistentStorageAdapter {
    /**
     * @var \TYPO3\FLOW3\Persistence\PersistenceManagerInterface
         * @FLOW3\Inject
     */
    protected $persistenceManager;

    /**
     * @FLOW3\Inject
     * @var \TYPO3\FLOW3\Validation\ValidatorResolver
     */
    protected $validatorResolver;

    /**
     * @var \TYPO3\FLOW3\Reflection\ReflectionService
     * @api
         * @FLOW3\Inject
     */
    protected $reflectionService;

    /**
     * apply filters
     *
     * @param string $filters
     * @return void
     */
    public function applyFilters($filters) {
        foreach ($filters as $property => $value) {
            $this->query->matching($this->query->contains($property, $value));
        }
    }

    public function applyLimit($limit) {
        $this->query->setLimit($limit);
    }

    public function applyOffset($offset) {
        $this->query->setOffset($offset);
    }

    public function applyOrderings($property, $direction = null) {
        if (is_null($direction)) {
            $direction = \TYPO3\FLOW3\Persistence\QueryInterface::ORDER_ASCENDING;
        }

        $this->query->setOrderings(array(
            $property => $direction
        ));
    }

    public function getName($being) {
        return ucfirst($being);
    }

    public function initQuery($being) {
        $repository = str_replace("Domain\\Model", "Domain\\Repository", $being) . "Repository";
        $repository = $this->getRepositoryForModel($being);
        if (\class_exists($repository)) {
            $this->repository = $this->objectManager->get($repository);
            $this->query = $this->repository->createQuery();
        }
        return $this;
    }

    public function getClasses(){
        return $this->reflectionService->getClassNamesByAnnotation("TYPO3\FLOW3\Annotations\Entity");
    }

    public function getGroups() {
        $this->init();
        $groups = array();
        $classes = $this->annotationService->getClassesAnnotatedWith(array("Active"));

        foreach ($classes as $class => $packageName) {
            $annotations = $this->annotationService->getClassAnnotations($class);
            $repository = $this->getRepositoryForModel($class);

            if (class_exists($repository)) {
                $group = $packageName;
                $name = $this->getShortName($class);

                if ($annotations->has("group"))
                    $group = (string) $annotations->get("group");

                #if ($annotations->get("label"))
                #    $name = strval(current($annotations->get("label")));

                $groups[$group][] = array("being" => $class, "name" => $name);
            }
        }

        return $groups;
    }

    public function getObject($being, $id = null) {
        if (class_exists($being)) {
            if ($id == null) {
                return $this->objectManager->get($being);
            } else {
                return $this->persistenceManager->getObjectByIdentifier($id, $being);
            }
        }
        return null;
    }

    public function getObjects($class) {
        $annotations = $this->annotationService->getClassAnnotations($class);
        $objects = array();

        if (!isset($this->query) || !is_subclass_of($class, $this->repository->getEntityClassName()))
            $this->initQuery($class);

#        if (isset($configuration["class"]["admin\annotations\orderby"])) {
#            $this->query->setOrderings(array(
#                current($configuration["class"]["admin\annotations\orderby"]) => 'ASC'
#            ));
#        }
    
        if(isset($this->query) && is_object($this->query))
            $objects = $this->query->execute();

        return $objects;
    }

    public function getId($object) {
        if (is_object($object)) {
            return $this->persistenceManager->getIdentifierByObject($object);
        }
        return null;
    }

    public function getQuery() {
        return $this->query;
    }

    public function executeQuery() {
        return $this->query->execute();
    }

    public function getRepositoryForModel($model) {
        $annotations = $this->annotationService->getClassAnnotations($model);
        $classSchema = $this->reflectionService->getClassSchema($model);

        $repository = $classSchema->getRepositoryClassName();

        return $repository;
    }

    public function getTotal($being) {
        return $this->query->count();
    }

    /**
     * returns the shortname representation of the class
     *
     * @package default
         */
    public function getShortName($class){
        if(is_object($class))
            $class = get_class($class);

        $parts = explode("\\", $class);
        return array_pop($parts);
    }

    public function isNewObject($object) {
        return $this->persistenceManager->isNewObject($object);
    }

    public function createObject($being, $object) {
        $configuration = $this->annotationService->getClassAnnotations($being);
        
        if (is_a($object, $being)) {
            $repository = $this->objectManager->get($this->getRepositoryForModel($being));
            $repository->add($object);
            $this->persistenceManager->persistAll();
        }
        return $object;
    }

    public function updateObject($being, $object) {
        $configuration = $this->annotationService->getClassAnnotations($being);
        
        if (is_a($object, $being)) {
            $repository = $this->objectManager->get($this->getRepositoryForModel($being));
            $repository->update($object);
            $this->persistenceManager->persistAll();
        }
        return $object;
    }

    public function deleteObject($being, $id) {
        $object = $this->persistenceManager->getObjectByIdentifier($id, $being);
        if ($object == null) return;
        $repositoryObject = $this->objectManager->get($this->getRepositoryForModel($being));
        $repositoryObject->remove($object);
        $this->persistenceManager->persistAll();
    }
}

?>