<?php
namespace TYPO3\Expose\ViewHelpers;

/*                                                                        *
 * This script belongs to the TYPO3.Expose package.                       *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 *  of the License, or (at your option) any later version.                *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 *
 * @api
 */
class ResourceViewHelper extends \TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper {
    /**
   	 * @Flow\Inject
   	 * @var \TYPO3\Flow\Configuration\ConfigurationManager
   	 */
   	protected $configurationManager;

    /**
     * @param string $path
     * @return string
     */
    public function render($path = '<TYPO3.Expose:ResourceHandler>') {
		$view = new \TYPO3\TypoScript\View\TypoScriptView();
		$view->setPackageKey('TYPO3.Expose');
		$view->setControllerContext($this->controllerContext);

		$configuration = $this->configurationManager->getConfiguration('Views', 'TYPO3.Expose');
		$view->setTypoScriptPathPatterns($configuration['options']['typoScriptPathPatterns']);

        $view->setTypoScriptPath($path);

		return $view->render();
	}
}

?>