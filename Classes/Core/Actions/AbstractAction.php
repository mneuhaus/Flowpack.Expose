<?php

namespace Foo\ContentManagement\Core\Actions;

/* *
 * This script belongs to the FLOW3 framework.                            *
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

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * abstract base class for the actions
 *
 * @version $Id: AbstractValidator.php 3837 2010-02-22 15:17:24Z robert $
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
abstract class AbstractAction implements ActionInterface {
	/**
	 * @var \Foo\ContentManagement\Actions\ActionManager
	 */
	protected $actionManager;

	/**
	 * @var \Foo\ContentManagement\Core\ConfigurationManager
	 * @FLOW3\Inject
	 */
	protected $configurationManager;

	/**
	 * @var \Foo\ContentManagement\Adapters\ContentManager
	 */
	protected $contentManager;	

	/**
	 * @var \Foo\ContentManagement\Core\Helper
	 * @author Marc Neuhaus <apocalip@gmail.com>
	 * @FLOW3\Inject
	 */
	protected $helper;
	
	/**
	 * @var \Foo\ContentManagement\Core\Adapters\AdapterInterface
	 * @author Marc Neuhaus <mneuhaus@famelo.com>
	 * */
	protected $adapter;
	protected $controller;
	protected $request;

	/**
	 * 
	 * @param \Foo\ContentManagement\Adapters\ContentManager $contentManager
	 * @param \Foo\ContentManagement\Actions\ActionManager   $actionManager
	 */
	public function __construct(\Foo\ContentManagement\Actions\ActionManager $actionManager, \Foo\ContentManagement\Adapters\ContentManager $contentManager) {
		$this->actionManager = $actionManager;
		$this->request = $actionManager->getRequest();
		$this->view = $actionManager->getView();

		$this->contentManager = $contentManager;
		if($this->request->hasArgument("being")){
			$class = \Foo\ContentManagement\Core\API::get("classShortNames", $this->request->getArgument("being"));
			$this->adapter = $contentManager->getAdapterByClass($class);
		}
	}

	public function canHandle($being, $action = null, $id = false) {
		return false;
	}

	public function getPackage() {
		return null;
	}

	public function getController() {
		return null;
	}

	public function getTarget() {
		return "_self";
	}
	
	public function getClass() {
		return "btn";
	}

	public function __toString() {
		$action = \Foo\ContentManagement\Core\Helper::getShortName($this);
		$action = str_replace("Action", "", $action);
		return $action;
	}

	public function getAction() {
		return lcfirst(self::__toString());
	}
	
	public function getShortcut(){
		return false;
	}

	public function override($class, $being){
		return false;
	}
	
	public function getSettings($path = null){
		$paths = array("Foo.ContentManagement.ViewSettings");
		$paths[] = ucfirst($this->getAction());
		if(!is_null($path))
			$paths[] = $path;
 		return $this->configurationManager->getSettings(implode(".", $paths));
	}
}
?>