<?php
namespace Foo\ContentManagement\Finishers;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.Form".                 *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 *  of the License, or (at your option) any later version.                *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * This finisher redirects to another Controller.
 */
class PersistenceFinisher extends \TYPO3\Form\Finishers\RedirectFinisher {
	/**
	 * @var \Foo\ContentManagement\Core\Helper
	 * @author Marc Neuhaus <apocalip@gmail.com>
	 * @FLOW3\Inject
	 */
	protected $helper;

	protected $defaultOptions = array(
		'package' => NULL,
		'controller' => NULL,
		'action' => '',
		'arguments' => array(),
		'delay' => 0,
		'statusCode' => 303,
	);

	public function executeInternal() {
		$formRuntime = $this->finisherContext->getFormRuntime();
		$request = $formRuntime->getRequest();//->getMainRequest();

		$class = $this->parseOption('class');
		$adapterClass = $this->helper->getAdapterByBeing($class);
		$adapter = new $adapterClass();
		$adapter->init();

		if($request->hasArgument("id")){
			$adapter->createObject($class, $formRuntime->getFormState()->getFormValues());
		}else{
			$adapter->createObject($class, $request->getArgument("id"), $formRuntime->getFormState()->getFormValues());
		}
	}

	/**
	 * @param array $options configuration options in the format array('@action' => 'foo', '@controller' => 'bar', '@package' => 'baz')
	 * @return void
	 * @api
	 */
	public function setOptions(array $options) {
		$this->options = $options;
	}
}
?>