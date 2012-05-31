<?php
namespace Foo\ContentManagement\Adapters;

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

use Doctrine\ORM\Mapping as ORM;
use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * Adapter for the Doctrine engine
 *
 * @version $Id: AbstractValidator.php 3837 2010-02-22 15:17:24Z robert $
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 *
 * @FLOW3\Scope("singleton")
 */
class DoctrineAdapter extends \Foo\ContentManagement\Core\Adapters\AbstractAdapter {
    /**
     * @var \TYPO3\FLOW3\Persistence\PersistenceManagerInterface
     * @author Marc Neuhaus <apocalip@gmail.com>
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
     * @author Marc Neuhaus <apocalip@gmail.com>
     * @FLOW3\Inject
     */
    protected $reflectionService;

    /**
     * apply filters
     *
     * @param string $beings
     * @param string $filters
     * @return void
     * @author Marc Neuhaus
     */
    public function applyFilters($beings, $filters) {
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
     * @author Marc Neuhaus
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

    public function createObject($being, $data) {
        $configuration = $this->annotationService->getClassAnnotations($being);
        $result = $this->transform($data, $being);

        if (is_a($result, $being)) {
            $repository = $this->objectManager->get($this->getRepositoryForModel($being));
            $repository->add($result);
            $this->persistenceManager->persistAll();
        }
        return $result;
    }

    public function updateObject($being, $id, $data) {
        $configuration = $this->annotationService->getClassAnnotations($being);
        $result = $this->transform($data, $being);

        if (is_a($result, $being)) {
            $repository = $this->objectManager->get($this->getRepositoryForModel($being));
            $repository->update($result);
            $this->persistenceManager->persistAll();
        }
        return $result;
    }

    public function deleteObject($being, $id) {
        $object = $this->persistenceManager->getObjectByIdentifier($id, $being);
        if ($object == null) return;
        $repositoryObject = $this->objectManager->get($this->getRepositoryForModel($being));
        $repositoryObject->remove($object);
        $this->persistenceManager->persistAll();
    }

    ## Conversion Functions

    public function transform($data, $target) {
        return $data;

        #$object = $this->getObject($target, isset($data["__identity"]) ? $data["__identity"] : null);
        #unset($data["__identity"]);
        #$value = $this->propertyMapper->convert($data, $target, \Foo\ContentManagement\Core\PropertyMappingConfiguration::getConfiguration());
        
        #foreach ($data as $property => $value) {
        #    if(empty($value))
        #        continue;
        #    \TYPO3\FLOW3\Reflection\ObjectAccess::setProperty($object, $property, $value);
        #}

        #return $object;
    }
}

?>