<?php
namespace Flowpack\Expose\Core\QueryBehaviors;

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
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Mvc\ActionRequest;
use TYPO3\Fluid\Core\ViewHelper\TemplateVariableContainer;
use TYPO3\Fluid\Core\ViewHelper\ViewHelperVariableContainer;

/**
 */
abstract class AbstractQueryBehavior implements QueryBehaviorInterface {

	/**
	 * Defines whether or not this behaviour is applied on transient properties
	 * @var boolean
	 */
	public static $appliedOnTransientProperties = false;

	/**
	 * Controller Context to use
	 * @var ActionRequest
	 * @api
	 */
	protected $request;

	/**
	 * Current variable container reference.
	 * @var TemplateVariableContainer
	 * @api
	 */
	protected $templateVariableContainer;

	/**
	 * ViewHelper Variable Container
	 * @var ViewHelperVariableContainer
	 * @api
	 */
	protected $viewHelperVariableContainer;

	/**
	 * @param ActionRequest $request
	 */
	public function setRequest($request) {
		$this->request = $request;
	}

	/**
	 * @param TemplateVariableContainer $templateVariableContainer
	 */
	public function setTemplateVariableContainer($templateVariableContainer) {
		$this->templateVariableContainer = $templateVariableContainer;
	}

	/**
	 * @param ViewHelperVariableContainer $viewHelperVariableContainer
	 */
	public function setViewHelperVariableContainer($viewHelperVariableContainer) {
		$this->viewHelperVariableContainer = $viewHelperVariableContainer;
	}

	public function addToBlock($name, $content) {
		$block = array();
		if ($this->viewHelperVariableContainer->exists('Flowpack\Expose\ViewHelpers\BlockViewHelper', $name)) {
			$block = $this->viewHelperVariableContainer->get('Flowpack\Expose\ViewHelpers\BlockViewHelper', $name);
		}
		$block[] = $content;
		$this->viewHelperVariableContainer->addOrUpdate('Flowpack\Expose\ViewHelpers\BlockViewHelper', $name, $block);
	}

	public function addWrapper($name, $callback) {
		$wrap = array();
		if ($this->viewHelperVariableContainer->exists('Flowpack\Expose\ViewHelpers\WrapViewHelper', $name)) {
			$wrap = $this->viewHelperVariableContainer->get('Flowpack\Expose\ViewHelpers\WrapViewHelper', $name);
		}
		$wrap[] = $callback;
		$this->viewHelperVariableContainer->addOrUpdate('Flowpack\Expose\ViewHelpers\WrapViewHelper', $name, $wrap);
	}
}

?>