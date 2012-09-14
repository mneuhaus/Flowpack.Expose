<?php
namespace TYPO3\Expose\ViewHelpers;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.Expose".               *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Doctrine\ORM\Mapping as ORM;
use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * TODO: (SK) get rid of this view helper. Duplicates lots of FLOW3 code.
 * (MN) Agreed in general, only difference here is the usage of those
 * Fallbacks for Templates again. IMHO this is one feature we
 * should try to keep, because it makes overriting Template
 * for certain things a breeze
 *
 * @api
 */
class RenderViewHelper extends \TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * @var \TYPO3\FLOW3\Cache\CacheManager
	 * @FLOW3\Inject
	 */
	protected $cacheManager;

	/**
	 * @var \TYPO3\FLOW3\Configuration\ConfigurationManager
	 * @FLOW3\Inject
	 */
	protected $configurationManager;

	/**
	 * @var \TYPO3\FLOW3\Object\ObjectManagerInterface
	 * @FLOW3\Inject
	 */
	protected $objectManager;

	/**
	 * @var \TYPO3\Fluid\Core\Parser\TemplateParser
	 * @FLOW3\Inject
	 */
	protected $templateParser;

	/**
	 * @var array
	 */
	protected $variables;

	/**
	 * returns a template Path by checking configured fallbacks
	 *
	 * @param string $patterns
	 * @param string $replacements
	 * @return $path String
	 */
	public function getPathByPatternFallbacks($patterns, $replacements) {
		if (is_string($patterns)) {
			$paths = explode('.', $patterns);
			$patterns = $this->configurationManager->getConfiguration(\TYPO3\FLOW3\Configuration\ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, 'TYPO3.Expose');
			$patterns = $patterns['Fallbacks'];
			foreach ($paths as $path) {
				$patterns = $patterns[$path];
			}
		}
		$triedPatterns = array();
		foreach ($patterns as $pattern) {
			$pattern = str_replace(array_keys($replacements), array_values($replacements), $pattern);
			$triedPatterns[] = $pattern;
			if (file_exists($pattern)) {
				return $pattern;
			}
		}
		throw new \RuntimeException('Could not find any matching path. Tried: ' . implode(', ', $triedPatterns), 1347870855);
	}

	/**
	 * Build the rendering context
	 *
	 * @param \TYPO3\Fluid\Core\ViewHelper\TemplateVariableContainer $variableContainer
	 * @return \TYPO3\Fluid\Core\Rendering\RenderingContext
	 */
	protected function buildRenderingContext(\TYPO3\Fluid\Core\ViewHelper\TemplateVariableContainer $variableContainer = NULL) {
		if ($variableContainer === NULL) {
			$variableContainer = $this->objectManager->get('TYPO3\\Fluid\\Core\\ViewHelper\\TemplateVariableContainer', $this->variables);
		}
		$renderingContext = $this->objectManager->get('TYPO3\\Fluid\\Core\\Rendering\\RenderingContext');
		$renderingContext->injectTemplateVariableContainer($variableContainer);
		if ($this->controllerContext !== NULL) {
			$renderingContext->setControllerContext($this->controllerContext);
		}
		$viewHelperVariableContainer = $this->objectManager->get('TYPO3\\Fluid\\Core\\ViewHelper\\ViewHelperVariableContainer');
		$viewHelperVariableContainer->setView($this->viewHelperVariableContainer->getView());
		$renderingContext->injectViewHelperVariableContainer($viewHelperVariableContainer);

		return $renderingContext;
	}

	/**
	 * @param string $templatePathAndFilename
	 * @return \TYPO3\Fluid\Core\Parser\ParsedTemplateInterface
	 * @throws \TYPO3\Fluid\View\Exception\InvalidTemplateResourceException
	 */
	protected function parseTemplate($templatePathAndFilename) {
		$templateSource = \TYPO3\FLOW3\Utility\Files::getFileContents($templatePathAndFilename, FILE_TEXT);
		if ($templateSource === FALSE) {
			throw new \TYPO3\Fluid\View\Exception\InvalidTemplateResourceException('"' . $templatePathAndFilename . '" is not a valid template resource URI.', 1257246929);
		}

		return $this->templateParser->parse($templateSource);
	}

	/**
	 * @param object $value
	 * @param string $partial
	 * @param string $fallbacks
	 * @param array $variables
	 * @param string $section
	 * @param mixed $optional
	 * @param string $variant
	 * @return string Rendered string
	 * @api
	 */
	public function render($value = '', $partial = '', $fallbacks = '', array $variables = array(), $section = NULL, $optional = FALSE, $variant = 'Default') {
		if ($value !== '') {
			return $value;
		}
		if ($partial !== '' && !is_null($partial)) {
			if ($fallbacks !== '') {
				$replacements = array('@partial' => $partial,
					'@package' => 'TYPO3.Expose',
					'@action' => $partial,
					'@variant' => $variant
				);
				$cache = $this->cacheManager->getCache('Expose_TemplateCache');
				$identifier = str_replace('\\', '_', implode('-', $replacements));
				$identifier = str_replace('.', '_', $identifier);
				$identifier = str_replace('/', '_', $identifier);
				$identifier = str_replace(' ', '_', $identifier);
				if (!$cache->has($identifier)) {
					$template = $this->getPathByPatternFallbacks($fallbacks, $replacements);
					$cache->set($identifier, $template);
				} else {
					$template = $cache->get($identifier);
				}
				if (empty($variables) && FALSE) {
					$this->view = $this->viewHelperVariableContainer->getView();
					$this->view->setTemplatePathAndFilename($template);
					if (!empty($template)) {
						return $this->view->render($partial);
					}
				} else {
					$partial = $this->parseTemplate($template);
					$variableContainer = $this->objectManager->get('TYPO3\\Fluid\\Core\\ViewHelper\\TemplateVariableContainer', $variables);
					$renderingContext = $this->buildRenderingContext($variableContainer);

					return $partial->render($renderingContext);
				}
			}
		}
		if ($section !== NULL) {
			$output = $this->viewHelperVariableContainer->getView()->renderSection($section, $variables, $optional);
			if (strlen($output) < 1) {
				$output = $this->renderChildren();
			}

			return $output;
		}
	}
}

?>