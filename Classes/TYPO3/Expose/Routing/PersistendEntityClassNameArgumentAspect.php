<?php
namespace TYPO3\Expose\Routing;

use TYPO3\Flow\Annotations as Flow;

/**
 * @Flow\Aspect
 */
class PersistendEntityClassNameArgumentAspect {
    /**
     * @Flow\Inject
     * @var \TYPO3\Flow\Object\ObjectManager
     */
    protected $objectManager;

    /**
     * @param \TYPO3\Flow\AOP\JoinPointInterface $joinPoint
     * @Flow\Before("method(TYPO3\Flow\Mvc\Routing\UriBuilder->uriFor())")
     * @return void
     */
    public function addEntityClassName(\TYPO3\Flow\AOP\JoinPointInterface $joinPoint) {
        if ($this->isExposeController($joinPoint) === FALSE) {
            return;
        }

        $request = $joinPoint->getProxy()->getRequest();
        $controllerArguments =$joinPoint->getMethodArgument('controllerArguments');
        if ($request->hasArgument('entityClassName') && isset($controllerArguments['entityClassName']) === FALSE) {
            $controllerArguments['entityClassName'] = $request->getArgument('entityClassName');
            $joinPoint->setMethodArgument('controllerArguments', $controllerArguments);
        }
    }

    public function isExposeController($joinPoint) {
        $request = $joinPoint->getProxy()->getRequest();

        $controllerName = $joinPoint->getMethodArgument('controllerName');
        if ($controllerName === NULL) {
            $controllerName = $request->getControllerName();
        }
        $packageKey = $joinPoint->getMethodArgument('packageKey');
        if ($packageKey === NULL) {
            $packageKey = $request->getControllerPackageKey();
        }

        $subPackageKey = $joinPoint->getMethodArgument('subPackageKey');
        if ($subPackageKey === NULL) {
            $subPackageKey = $request->getControllerSubPackageKey();
        }

        $possibleObjectName = '\@package\@subpackage\Controller\@controllerController';
        $possibleObjectName = str_replace('@package', str_replace('.', '\\', $packageKey), $possibleObjectName);
        $possibleObjectName = str_replace('@subpackage', $subPackageKey, $possibleObjectName);
        $possibleObjectName = str_replace('@controller', $controllerName, $possibleObjectName);
        $possibleObjectName = str_replace('\\\\', '\\', $possibleObjectName);

        $controllerObjectName = $this->objectManager->getCaseSensitiveObjectName($possibleObjectName);

        if ($controllerObjectName == '\TYPO3\Expose\Controller\CrudController') {
            return TRUE;
        }
        return is_subclass_of($controllerObjectName, '\TYPO3\Expose\Controller\CrudController');
    }

}