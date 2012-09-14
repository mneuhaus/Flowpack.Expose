<?php
namespace TYPO3\Expose\OptionsProvider;

/*                                                                        *
* This script belongs to the TYPO3.Expose package.              *
*                                                                        *
* It is free software; you can redistribute it and/or modify it under    *
* the terms of the GNU Lesser General Public License as published by the *
* Free Software Foundation, either version 3 of the License, or (at your *
* option) any later version.                                             *
*                                                                        *
* This script is distributed in the hope that it will be useful, but     *
* WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
* TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser       *
* General Public License for more details.                               *
*                                                                        *
* You should have received a copy of the GNU Lesser General Public       *
* License along with the script.                                         *
* If not, see http://www.gnu.org/licenses/lgpl.html                      *
*                                                                        *
* The TYPO3 project - inspiring people to share!                         *
*                                                                        */

use Doctrine\ORM\Mapping as ORM;
use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * OptionsProvider for related Beings
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
class RelationOptionsProvider extends \TYPO3\Expose\Core\OptionsProvider\AbstractOptionsProvider {

    /**
     * @var \TYPO3\Expose\Core\MetaPersistenceManager
     * @FLOW3\Inject
     */
    protected $persistenceService;

    /**
    * TODO: Document this Method! ( getOptions )
    */
    public function getOptions() {
        $options = array();
        $objects = $this->persistenceService->createQueryForType($this->annotations->getType())->execute();
        foreach ($objects as $object) {
            $options[$this->persistenceService->getIdentifierByObject($object)] = $this->convert($object);
        }
        return $objects;
    }

    public function convert($value) {
        if (method_exists($value, "__toString"))
            return $value->__toString();
        return get_class($value);
    }

}

?>