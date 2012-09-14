<?php
namespace TYPO3\Expose\ViewHelpers\Render;

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
abstract class AbstractRenderViewHelper extends \TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper {

    /**
    * TODO: Document this Method! ( initialize )
    */
    public function initialize() {
        $this->view = new \TYPO3\Expose\View\FallbackTemplateView();
        $this->view->setControllerContext($this->controllerContext);
        $this->view->setRenderingContext($this->renderingContext);
    }

    /**
     * If $arguments['settings'] is not set, it is loaded from the TemplateVariableContainer (if it is available there).
     *
     * @param array $arguments
     * @return array
     */
    protected function loadSettingsIntoArguments($arguments) {
        if (!isset($arguments['settings']) && $this->templateVariableContainer->exists('settings')) {
            $arguments['settings'] = $this->templateVariableContainer->get('settings');
        }
        return $arguments;
    }

    /**
     * Renders the content.
     *
     * @param array $objects
     * @return string
     * @api
     */
    public function render($objects = array()) {
        return $this->view->renderContent('List', array('objects' => $objects
        ));
    }

}

?>