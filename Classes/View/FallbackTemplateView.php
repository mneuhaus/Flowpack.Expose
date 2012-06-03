<?php
namespace Foo\ContentManagement\View;

/*                                                                        *
 * This script belongs to the FLOW3 package "Fluid".                      *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 *  of the License, or (at your option) any later version.                *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * TODO: (SK) why cannot we use the standard template view? What does this view do in addition?
 * 		 (MN) This View iterates over a wide variety of Fallback options to make it easy to override
 *    		  an template for a Package, Model, Action, etc just by convention.
 *        	  Here are for Example the Fallbacks for the Views:
 *           	- resource://@package/Private/Templates/@being/@action/@variant.html
 *           	- resource://@package/Private/Templates/Admin/@action/@variant.html
 *           	- resource://@package/Private/Templates/@being/@action.html
 *           	- resource://@package/Private/Templates/Admin/@action.html
 *           	- resource://Foo.ContentManagement/Private/Templates/Standard/@action/@variant.html
 *           	- resource://Foo.ContentManagement/Private/Templates/Standard/@action.html
 *
 * The main template view. Should be used as view if you want Fluid Templating
 *
 * @api
 */
class FallbackTemplateView extends \TYPO3\Fluid\View\TemplateView {
	/**
	 * @var \Foo\ContentManagement\Core\ActionManager
	 * @FLOW3\Inject
	 */
	protected $actionManager;

	/**
	 * @var \Foo\ContentManagement\Core\CacheManager
	 * @FLOW3\Inject
	 */
	protected $cacheManager;

	/**
	 * @var \Foo\ContentManagement\Adapters\ContentManager
	 * @FLOW3\Inject
	 */
	protected $contentManager;

	/**
	 * Resolve the template path and filename for the given action. If $actionName
	 * is NULL, looks into the current request.
	 *
	 * @param string $actionName Name of the action. If NULL, will be taken from request.
	 * @return string Full path to template
	 * @throws \TYPO3\Fluid\View\Exception\InvalidTemplateResourceException
	 */
	protected function getTemplatePathAndFilename($actionName = NULL) {
		if(is_null($actionName))
			if(isset($this->actionName))
				$actionName = $this->actionName;
			else
				$actionName = "index";
		else
			$this->actionName = $actionName;

		$replacements = array(
			"@action" => ucfirst($actionName),
			"@variant" => "Default",
			"@package" => "Foo.ContentManagement",
		);

		if($this->controllerContext->getRequest()->hasArgument("being")){
			$being = $this->controllerContext->getRequest()->getArgument("being");
			if(class_exists($being, false) && false){
#				$replacements["@package"] = $this->helper->getPackageByClassName($being) ? $this->helper->getPackageByClassName($being) : "Admin";

				$replacements["@being"] = $this->contentManager->getShortName($being);

				// TODO: Reimplement Variants
				#$being = $this->helper->getBeing($being);
				#$replacements["@variant"] = $being->variant->getVariant($actionName);
			}
		}

		if($this->controllerContext->getRequest()->hasArgument("variant")){
			$replacements["@variant"] = $this->request->getArgument("variant");
		}

		$cache = $this->cacheManager->getCache('Admin_TemplateCache');
		$identifier = str_replace(".", "_", implode("-",$replacements));
		$noTemplate = false;
		if(!$cache->has($identifier)){
			//try{
				$template = $this->getPathByPatternFallbacks("Views", $replacements);
			//}catch (\Exception $e){
			//	$noTemplate = true;
			//	var_dump($e);
			//}
			//if(!$noTemplate)
				$cache->set($identifier,$template);
		}else{
			$template = $cache->get($identifier);
		}

		return $template;
	}

	/**
	 * returns a template Path by checking configured fallbacks
	 *
	 * @param string $patterns
	 * @param string $replacements
	 * @return $path String
		 */
	public function getPathByPatternFallbacks($patterns, $replacements){
		if(is_string($patterns)){
			$paths = explode(".",$patterns);
			$patterns = $this->contentManager->getSettings();
			$patterns = $patterns["Fallbacks"];
			foreach ($paths as $path) {
				$patterns = $patterns[$path];
			}
		}

		foreach($patterns as $pattern){
			$pattern = str_replace(array_keys($replacements),array_values($replacements),$pattern);
			$tried[] = $pattern;
			if(file_exists($pattern)){
				return $pattern;
			}
		}

		throw new \Exception('Could not find any Matching Path. Tried: '.implode(", ", $tried).'');
	}

	public function renderContent($contentName, $variables = array(), $variant = "Default", $sectionName = NULL) {
		$parsedTemplate = $this->templateParser->parse($this->getContentSource($contentName, $variant));

		$variableContainer = $this->objectManager->get('TYPO3\Fluid\Core\ViewHelper\TemplateVariableContainer', $variables);
		$renderingContext = clone $this->baseRenderingContext;
		$renderingContext->injectTemplateVariableContainer($variableContainer);

		$this->startRendering(self::RENDERING_PARTIAL, $parsedTemplate, $renderingContext);
		if ($sectionName !== NULL) {
			$output = $this->renderSection($sectionName, $variables);
		} else {
			$output = $parsedTemplate->render($renderingContext);
		}
		$this->stopRendering();

		return $output;
	}

	/**
	 * Figures out which content to use.
	 *
	 * @param string $partialName The name of the partial
	 * @return string contents of the partial template
	 * @throws \TYPO3\Fluid\View\Exception\InvalidTemplateResourceException
	 */
	protected function getContentSource($contentName, $variant = "Default") {
		$replacements = array(
			"@content" => ucfirst($contentName),
			"@variant" => "Default",
			"@package" => "Foo.ContentManagement",
			"@variant" => $variant
		);
		$contentPathAndFilename = $this->getPathByPatternFallbacks("Contents", $replacements);
		$contentSource = \TYPO3\FLOW3\Utility\Files::getFileContents($contentPathAndFilename, FILE_TEXT);
		if ($contentSource === FALSE) {
			throw new \TYPO3\Fluid\View\Exception\InvalidTemplateResourceException('"' . $contentPathAndFilename . '" is not a valid template resource URI.', 1257246929);
		}
		return $contentSource;
	}

	public function setTemplateByAction($actionName) {
		$this->setTemplatePathAndFilename($this->getTemplatePathAndFilename($actionName));
	}

	/**
	 * Resolves the layout root to be used inside other paths.
	 *
	 * @return string Path to layout root directory
	 */
	protected function getLayoutRootPath() {
		if ($this->layoutRootPath !== NULL) {
			return $this->layoutRootPath;
		} else {
			return str_replace('@packageResourcesPath', 'resource://Foo.ContentManagement', $this->layoutRootPathPattern);
		}
	}
}

?>