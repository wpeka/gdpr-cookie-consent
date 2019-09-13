<?php

namespace Analyst\Core;

abstract class AbstractFactory
{
	/**
	 * Unserialize to NoticeFactory
	 *
	 * @param $raw
	 * @return static
	 */
	protected static function unserialize($raw)
	{
		$instance = @unserialize($raw);

		$isProperObject = is_object($instance) && $instance instanceof static;

		// In case for some reason unserialized object is not
		// NoticeFactory we make sure it is NoticeFactory
		if (!$isProperObject) {
			$instance = new static();
		}

		return $instance;
	}
}
