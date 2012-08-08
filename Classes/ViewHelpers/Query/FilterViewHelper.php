<?php
namespace TYPO3\Admin\ViewHelpers\Query;

/*                                                                        *
 * This script belongs to the TYPO3.Admin package.              *
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
class FilterViewHelper extends \TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper {

    /**
     * @var \TYPO3\Admin\Reflection\AnnotationService
     * @FLOW3\Inject
     */
    protected $annotationService;

    /**
     * @var \TYPO3\FLOW3\Configuration\ConfigurationManager
     * @FLOW3\Inject
     */
    protected $configurationManager;

    /**
    * TODO: Document this Method! ( getFilter )
    */
    public function getFilter($selected = array()) {
        $filters = array();
        return $filters;
        foreach ($this->objects as $object) {

        }
        return $filters;
    }

    /**
    * TODO: Document this Method! ( handleFilters )
    */
    public function handleFilters() {
        if ($this->request->hasArgument('filters')) {
            $filters = $this->request->getArgument('filters');
            foreach ($filters as $key => $value) {
                if (!empty($value)) {
                    $this->query->matching($this->query->equals($key, $value));
                }
            }
            return $this->getFilter($filters);
        } else {
            return $this->getFilter();
        }
    }

    /**
     *
     * @param mixed $objects
     * @param string $as
     * @param string $filtersAs
     * @return string Rendered string
     * @api
     */
    public function render($objects = null, $as = 'filteredObjects', $filtersAs = 'filters') {
        $this->objects = $objects;
        $this->query = $objects->getQuery();
        $this->request = $this->controllerContext->getRequest();
        $filters = $this->handleFilters();
        $this->templateVariableContainer->add($filtersAs, $filters);
        $this->templateVariableContainer->add($as, $this->query->execute());
        $content = $this->renderChildren();
        $this->templateVariableContainer->remove($filtersAs);
        $this->templateVariableContainer->remove($as);
        return $content;
    }

}

?>