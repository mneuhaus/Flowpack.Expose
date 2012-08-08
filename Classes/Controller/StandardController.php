<?php
namespace TYPO3\Admin\Controller;

/* *
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
 * Standard controller for the Admin package; main entry point when this package is
 * used inside Phoenix.
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
class StandardController extends \TYPO3\TYPO3\Controller\Module\StandardController {

    /**
     * Index action
     *
     * @return void
     */
    public function indexAction() {
        $featureRuntime = new \TYPO3\Admin\Core\FeatureRuntime($this->request);
        if (isset($this->moduleConfiguration['defaultFeatureClassName'])) {
            $featureRuntime->setDefaultFeatureClassName($this->moduleConfiguration['defaultFeatureClassName']);
        }
        if (isset($this->moduleConfiguration['defaultFeatureArguments'])) {
            $featureRuntime->setDefaultFeatureArguments($this->moduleConfiguration['defaultFeatureArguments']);
        }
        return $featureRuntime->execute();
    }

}

?>