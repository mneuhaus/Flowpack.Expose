<?php
namespace TYPO3\Expose\ViewHelpers;

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
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 * @api
 */
class AnnotationsViewHelper extends \TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper {

    /**
     * @var \TYPO3\Expose\Reflection\AnnotationService
     * @FLOW3\Inject
     */
    protected $annotationService;

    /**
     *
     * @param object $object
     * @param string $className
     * @param string $as
     * @return string Rendered string
     * @api
     */
    public function render($object = null, $className = null, $as = 'annotations') {
        if (is_null($className) && !is_null($object)) {
            $className = get_class($object);
        }
        $classAnnotations = $this->annotationService->getClassAnnotations($className);
        $this->templateVariableContainer->add($as, $classAnnotations);
        $content = $this->renderChildren();
        $this->templateVariableContainer->remove($as);
        return $content;
    }

}

?>