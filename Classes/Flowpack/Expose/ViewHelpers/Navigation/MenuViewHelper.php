<?php
namespace Flowpack\Expose\ViewHelpers\Navigation;

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
use TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 */
class MenuViewHelper extends AbstractViewHelper {
	/**
	 * @var \TYPO3\Flow\Configuration\ConfigurationManager
	 * @Flow\Inject
	 */
	protected $configurationManager;

	/**
	 * The AccessDecisionVoterManager
	 *
	 * @var \TYPO3\Flow\Security\Authorization\AccessDecisionVoterManager
	 * @Flow\Inject
	 */
	protected $accessDecisionVoterManager;

	/**
	 * The policyService
	 *
	 * @var \TYPO3\Flow\Security\Policy\PolicyService
	 * @Flow\Inject
	 */
	protected $policyService;

	/**
	 * @param string $as
	 * @return string
	 */
	public function render($as = 'items') {
		$menuConfiguration = $this->configurationManager->getConfiguration(\TYPO3\Flow\Configuration\ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, 'Flowpack.Expose.Menu');

		#$items = $this->parseRoutes($routes);
		$items = $this->prepareMenuItems($menuConfiguration);

		$this->templateVariableContainer->add($as, $items);
		$output = $this->renderChildren();
		$this->templateVariableContainer->remove($as);
		return $output;
	}

	public function prepareMenuItems($menuItems) {
		$menuItemDefaults = array(
			'action' => 'index',
			'arguments' => array()
		);

		$items = array();
		foreach ($menuItems as $menuItemName => $menuItemConfiguration) {
			if (isset($menuItemConfiguration['entityClassName'])) {
				$menuItemConfiguration = array_merge(array(
					'controller' => 'Crud',
					'package' => 'Flowpack.Expose',
					'arguments' => array(
						'entityClassName' => $menuItemConfiguration['entityClassName']
					)
				), $menuItemConfiguration);
			}
			$menuItemConfiguration['name'] = $menuItemName;
			if (isset($menuItemConfiguration['subMenu'])) {
				$menuItemConfiguration['subMenu'] = $this->prepareMenuItems($menuItemConfiguration['subMenu']);
			}
			$items[] = array_merge($menuItemDefaults, $menuItemConfiguration);
		}

		return $items;
	}

	public function parseRoutes($routes) {
		$items = array();
		$classTemplate = '{package}\Controller\{controller}Controller';
		foreach ($routes as $route) {
			if (isset($route['navigation'])) {

				$item = array();
				$item['label'] = $route['navigation'];
				$item['uriPattern'] = $route['uriPattern'];
				$searchAndReplace = array();
				foreach ($route['defaults'] as $key => $value) {
					$item[ltrim($key, '@')] = $value;
					if (is_array($value)) {
						continue;
					}
					$searchAndReplace['{' . ltrim($key, '@') . '}'] = str_replace('.', '\\', $value);
				}

				$className = str_replace(array_keys($searchAndReplace), array_values($searchAndReplace), $classTemplate);
				if ($this->policyService->hasPolicyEntryForMethod($className, $route['defaults']['@action'] . 'Action')) {
					try {
						$joinPoint = new \Famelo\Navigation\Aop\VirtualJoinPoint();
						$joinPoint->setClassName($className);
						$joinPoint->setMethodName($route['defaults']['@action'] . 'Action');
						$vote = $this->accessDecisionVoterManager->decideOnJoinPoint($joinPoint);
					} catch (\TYPO3\Flow\Security\Exception\AccessDeniedException $e) {
						continue;
					}
				}

				$items[] = $item;
			}
		}
		return $items;
	}
}

?>