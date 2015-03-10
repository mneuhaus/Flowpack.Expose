<?php
namespace Flowpack\Expose\ViewHelpers;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Flowpack.Expose".       *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
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
class BehaviorViewHelper extends AbstractViewHelper {

	
	/**
	 * NOTE: This property has been introduced via code migration to ensure backwards-compatibility.
	 * @see AbstractViewHelper::isOutputEscapingEnabled()
	 * @var boolean
	 */
	protected $escapeOutput = FALSE;

	/**
	 * @Flow\Inject
	 * @var PersistenceManagerInterface
	 */
	protected $persistenceManager;

	/**
	 *
	 * @param objects $objects
	 * @param array $behaviors
	 * @return string Rendered string
	 * @api
	 */
	public function render($objects, $behaviors = array()) {
		$query = $this->getQuery($objects);

		foreach ($behaviors as $behaviorClassName => $active) {
			if ($active !== TRUE) {
				continue;
			}
			$behavior = new $behaviorClassName();
			$behavior->setRequest($this->controllerContext->getRequest());
			$behavior->setTemplateVariableContainer($this->templateVariableContainer);
			$behavior->setViewHelperVariableContainer($this->viewHelperVariableContainer);
			$behavior->run($query);
		}

		$as = array_search($objects, $this->templateVariableContainer->getAll());

		$this->templateVariableContainer->remove($as);
		$this->templateVariableContainer->add($as, $query->execute());
		$content = $this->renderChildren();
		$this->templateVariableContainer->remove($as);
		$this->templateVariableContainer->add($as, $objects);

		return $content;
	}

	public function getQuery($objects) {
		if ($objects instanceof PersistentCollection) {
			$query = $this->persistenceManager->createQueryForType($objects->getTypeClass()->name);
			$ids = array();
			foreach ($objects as $object) {
				$ids[] = $this->persistenceManager->getIdentifierByObject($object);
			}
			$query->matching($query->in('Persistence_Object_Identifier', $ids));
			return $query;
		}

		return $objects->getQuery();
	}
}

?>