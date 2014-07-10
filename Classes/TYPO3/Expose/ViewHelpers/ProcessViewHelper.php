<?php
namespace TYPO3\Expose\ViewHelpers;

/*                                                                        *
 * This script belongs to the Flow framework.                             *
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
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 */
class ProcessViewHelper extends AbstractViewHelper {
	/**
	 *
	 * @param objects $objects
	 * @param array $processors
	 * @return string Rendered string
	 * @api
	 */
	public function render($objects, $processors = array()) {
		$query = $objects->getQuery();
		foreach ($processors as $processorClassName) {
			$processor = new $processorClassName();
			$processor->setRenderingContext($this->renderingContext);
			$processor->process($query);
		}

		$as = array_search($objects, $this->templateVariableContainer->getAll());

		$this->templateVariableContainer->remove($as);
		$this->templateVariableContainer->add($as, $query->execute());
		$content = $this->renderChildren();
		$this->templateVariableContainer->remove($as);
		$this->templateVariableContainer->add($as, $objects);

		return $content;
	}
}

?>