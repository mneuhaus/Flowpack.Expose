<?php
namespace Flowpack\Expose\Processors;


/*                                                                        *
 * This script belongs to the FLow framework.                            *
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
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Configuration\ConfigurationManager;
use TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 */
class PaginationProcessor extends AbstractProcessor {
	/**
	 * @var \TYPO3\Flow\Configuration\ConfigurationManager
	 * @Flow\Inject
	 */
	protected $configurationManager;

	/**
	 *
	 * @param object $query
	 * @return string Rendered string
	 * @api
	 */
	public function process($query) {
		$configurationPath = 'Flowpack.Expose.pagination';
		$this->query = $query;
		$this->settings = $this->configurationManager->getConfiguration(ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, $configurationPath);
		$this->request = $this->controllerContext->getRequest();

		$this->total = $this->query->count();
		$limits = $this->handleLimits();
		$pagination = $this->handlePagination();

		$content = $this->viewHelperVariableContainer->getView()->renderPartial('Pagination', NULL, array(
			'pagination' => $pagination,
			'limits' => $limits
		));
		$this->addToBlock('bottom', $content);
	}

	public function handleLimits(){

		$limits = array();
		foreach ($this->settings["limits"] as $limit) {
			$limits[$limit] = false;
		}

		if($this->request->hasArgument("limit"))
			$this->limit = $this->request->getArgument("limit");
		else
			$this->limit = $this->settings["defaultLimit"];

		$unset = false;
		foreach ($limits as $key => $value) {
			$limits[$key] = ($this->limit == $key);

			if(!$unset && intval($key) >= intval($this->total)){
				$unset = true;
				continue;
			}
			if($unset)
				unset($limits[$key]);
		}

		if(count($limits) == 1)
			$limits = array();

		$this->query->setLimit($this->limit);

		return $limits;
	}

	public function handlePagination(){
		$currentPage = 1;

		if( $this->request->hasArgument("page") )
			$currentPage = $this->request->getArgument("page");

		$pages = array();
		for($i=0; $i < ($this->total / $this->limit); $i++) {
			$pages[] = $i + 1;
		}

		if($currentPage > count($pages))
			$currentPage = count($pages);

		$offset = ($currentPage - 1) * $this->limit;
		$offset = $offset < 0 ? 0 : $offset;
		$this->query->setOffset($offset);
		$pagination = array("offset" => $offset);

		if(count($pages) > 1){
			$pagination["currentPage"] = $currentPage;

			if($currentPage < count($pages))
				$pagination["nextPage"] = $currentPage + 1;

			if($currentPage > 1)
				$pagination["prevPage"] = $currentPage - 1;

			if(count($pages) > $this->settings["maxPages"]){
				$max = $this->settings["maxPages"];
				$start = $currentPage - ( ($max + ($max % 2) ) / 2);
				$start = $start > 0 ? $start : 0;
				$start = $start > 0 ? $start : 0;
				$start = $start + $max > count($pages) ? count($pages) - $max : $start;
				$pages = array_slice($pages, $start, $max);
			}

			$pagination["pages"] = $pages;
		}
		return $pagination;
	}
}

?>