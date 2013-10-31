<?php
namespace TYPO3\Expose\Utility;

/*                                                                        *
 * This script belongs to the TYPO3 Flow framework.                       *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * @Flow\Scope("singleton")
 */
class StringRepresentation {
	public static function convert($mixed) {
		switch (true) {
			case is_string($mixed):
			case is_int($mixed):
			case is_float($mixed):
			case is_double($mixed):
				return strval($mixed);

			case is_bool($mixed):
				return $mixed === TRUE ? 'true' : 'false';

			case is_resource($mixed):
				$type = get_resource_type($mixed);
				if ('stream' === $type) {
					$metaData = stream_get_meta_data($mixed);
					$info = ' ' . $metaData['mode'];
				} else {
					$info = '';
				}
				return sprintf('<resource: %s #%d%s>', $type, $mixed, $info);

			case $mixed instanceof \IteratorAggregate:
				$mixed = iterator_to_array($mixed);

			case is_array($mixed):
				foreach ($mixed as $key => $value) {
					$mixed[$key] = self::convert($value);
				}
				return implode(', ', $mixed);

			case $mixed instanceof \DateTime:
				return $mixed->format(\DateTime::W3C);

			case $mixed instanceof \TYPO3\Flow\Security\Account:
				return $mixed->getAccountIdentifier();

			case is_object($mixed) && method_exists($mixed, '__toString'):
				return $mixed->__toString();

			case is_object($mixed):
				$className = get_class($mixed);
				$parts = explode('\\', $className);
				return sprintf('<%s: %s>', end($parts), spl_object_hash($mixed));

			default:
				return 'Unknown type of variable';
				break;
		}
	}
}
?>