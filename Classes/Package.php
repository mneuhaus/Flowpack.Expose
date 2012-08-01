<?php
namespace Foo\ContentManagement;

use \TYPO3\FLOW3\Package\Package as BasePackage;
use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * Package base class of the Admin package.
 *
 * @FLOW3\Scope("singleton")
 */
class Package extends BasePackage {
	/**
	 * Invokes custom PHP code directly after the package manager has been initialized.
	 *
	 * @param \TYPO3\FLOW3\Core\Bootstrap $bootstrap The current bootstrap
	 * @return void
	 */
	public function boot(\TYPO3\FLOW3\Core\Bootstrap $bootstrap) {
		$dispatcher = $bootstrap->getSignalSlotDispatcher();
		#$dispatcher->connect('TYPO3\FLOW3\Monitor\FileMonitor', 'filesHaveChanged', 'Foo\ContentManagement\Core\CacheManager', 'flushCachesByChangedFiles');
	}
}
?>