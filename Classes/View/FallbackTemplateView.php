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
 * The main template view. Should be used as view if you want Fluid Templating
 *
 * @api
 */
class FallbackTemplateView extends \TYPO3\Fluid\View\TemplateView {
	/**
	 * @var \Foo\ContentManagement\Core\CacheManager
	 * @FLOW3\Inject
	 */
	protected $cacheManager;

	/**
	 * @var \Foo\ContentManagement\Core\ConfigurationManager
	 * @FLOW3\Inject
	 */
	protected $configurationManager;

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
		$replacements = array(
			"@action" => ucfirst($actionName),
			"@variant" => "Default",
			"@package" => "Foo.ContentManagement",
		);
		
		if($this->controllerContext->getRequest()->hasArgument("being")){
			$being = $this->controllerContext->getRequest()->getArgument("being");
			if(class_exists($being, false)){
				$replacements["@package"] = $this->helper->getPackageByClassName($being) ? $this->helper->getPackageByClassName($being) : "Admin";
		
				$replacements["@being"] = \Foo\ContentManagement\Core\Helper::getShortName($being);
				
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
			try{
				$template = $this->getPathByPatternFallbacks("Views", $replacements);
			}catch (\Exception $e){
				$noTemplate = true;
			}
			if(!$noTemplate)
				$cache->set($identifier,$template);
		}else{
			$template = $cache->get($identifier);
		}
		
		if(!$noTemplate){
			$this->view->setTemplatePathAndFilename($template);
		}
	}

	/**
	 * returns a template Path by checking configured fallbacks
	 *
	 * @param string $patterns 
	 * @param string $replacements 
	 * @return $path String
	 * @author Marc Neuhaus
	 */
	public function getPathByPatternFallbacks($patterns, $replacements){
		if(is_string($patterns)){
			$paths = explode(".",$patterns);
			$patterns = $this->configurationManager->getSettings();
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
		$this->baseRenderingContext->setControllerContext($this->controllerContext);
		$this->templateParser->setConfiguration($this->buildParserConfiguration());

#		$templateIdentifier = $this->getTemplateIdentifier($contentName);
#		if ($this->templateCompiler->has($templateIdentifier)) {
#			$parsedTemplate = $this->templateCompiler->get($templateIdentifier);
#		} else {
			$parsedTemplate = $this->templateParser->parse($this->getContentSource($contentName, $variant));
#			if ($parsedTemplate->isCompilable()) {
#				$this->templateCompiler->store($templateIdentifier, $parsedTemplate);
#			}
#		}

		if ($parsedTemplate->hasLayout()) {
			$layoutName = $parsedTemplate->getLayoutName($this->baseRenderingContext);
			$layoutIdentifier = $this->getLayoutIdentifier($layoutName);
			if ($this->templateCompiler->has($layoutIdentifier)) {
				$parsedLayout = $this->templateCompiler->get($layoutIdentifier);
			} else {
				$parsedLayout = $this->templateParser->parse($this->getLayoutSource($layoutName));
				if ($parsedLayout->isCompilable()) {
					$this->templateCompiler->store($layoutIdentifier, $parsedLayout);
				}
			}
			$this->startRendering(self::RENDERING_LAYOUT, $parsedTemplate, $this->baseRenderingContext);
			$output = $parsedLayout->render($this->baseRenderingContext);
			$this->stopRendering();
		} else {
			$this->startRendering(self::RENDERING_TEMPLATE, $parsedTemplate, $this->baseRenderingContext);
			$output = $parsedTemplate->render($this->baseRenderingContext);
			$this->stopRendering();
		}

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
}

?>