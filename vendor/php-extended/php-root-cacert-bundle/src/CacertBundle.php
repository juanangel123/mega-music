<?php

namespace PhpExtended\RootCacert;

/**
 * CacertBundle class file.
 *
 * This class represents the file with all the valid ssl certificates up to
 * date.
 *
 * @author Anastaszor
 */
class CacertBundle
{
	
	/**
	 * Gets the absolute path where the cacert.pem file is stored.
	 * This file may be used to validate certificates where fetching outside
	 * resources like with cURL.
	 *
	 * @return string
	 */
	public static function getFilePath()
	{
		return dirname(__DIR__).DIRECTORY_SEPARATOR.'crt'.DIRECTORY_SEPARATOR.'cacert.pem';
	}
	
}
