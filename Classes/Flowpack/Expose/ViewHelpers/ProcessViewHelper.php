<?php
namespace Flowpack\Expose\ViewHelpers;

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
use Doctrine\ORM\PersistentCollection;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Persistence\PersistenceManagerInterface;
use TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 */
class ProcessViewHelper extends AbstractViewHelper {

	/**
	 * @Flow\Inject
	 * @var PersistenceManagerInterface
	 */
	protected $persistenceManager;

	/**
	 *
	 * @param objects $objects
	 * @param array $processors
	 * @return string Rendered string
	 * @api
	 */
	public function render($objects, $processors = array()) {
		if ($objects instanceof PersistentCollection) {
			$query = $this->persistenceManager->createQueryForType($objects->getTypeClass()->name);
			$ids = array();
			foreach ($objects as $object) {
				$ids[] = $this->persistenceManager->getIdentifierByObject($object);
			}
			$query->matching($query->in('Persistence_Object_Identifier', $ids));
		} else {
			$query = $objects->getQuery();
		}

		foreach ($processors as $processorClassName => $active) {
			if ($active !== TRUE) {
				continue;
			}
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