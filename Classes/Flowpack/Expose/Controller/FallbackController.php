<?php
namespace Flowpack\Expose\Controller;

use TYPO3\Flow\Annotations as Flow;

class FallbackController extends \TYPO3\Flow\Mvc\Controller\ActionController {

	/**
	 * The default view object to use if none of the resolved views can render
	 * a response for the current request.
	 *
	 * @var string
	 * @api
	 */
	protected $defaultViewObjectName = 'Flowpack\Expose\View\FallbackView';

}