<?php

namespace PhpExtended\RootCacert;

/**
 * CacertUpdate class file.
 *
 * This class represents the way to update the file by reloading from the
 * source website the new cacert.pem file, and updates the git repository
 * with the newest information.
 *
 * @author Anastaszor
 */
class CacertUpdate
{
	
	/**
	 * The tag value.
	 *
	 * @var string (int-int-int)
	 */
	private $_actual_tag = null;
	
	/**
	 * Executes the update process.
	 */
	public function doUpdate()
	{
		$this->_actual_tag = $this->getActualTagFromGit();
		$this->fetchCacertFromSource();
		$this->updateReadme();
		$this->commitToGit();
		$this->updateTagToGit();
	}
	
	/**
	 * Gets the actual tag version from git command.
	 *
	 * @return string
	 * @throws \RuntimeException
	 */
	protected function getActualTagFromGit()
	{
		echo "Getting actual tag from git...";
		chdir(dirname(__DIR__));
		$res = exec("git describe --tags --abbrev=0");
		$matches = array();
		echo "found ".$res."\n";
		if(!preg_match('#^(\d+)\.(\d+)\.(\d+)#', $res, $matches))
			throw new \RuntimeException('Impossible to parse tag '.$res);
		return $matches[1].'-'.$matches[2].'-'.$matches[3];
	}
	
	/**
	 * Fetches the cacert contents, and puts them into the repository.
	 * This takes care of validating the certificate of curl.haxx.se !
	 *
	 * @throws \RuntimeException
	 */
	protected function fetchCacertFromSource()
	{
		$url = 'https://curl.haxx.se/ca/cacert.pem';
		echo "Getting cacert.pem from ".$url."... ";
		
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch, CURLOPT_ENCODING, "gzip");
		
		// be sure to validate the domain we're fetching data from
		$cacert_path = CacertBundle::getFilePath();
		curl_setopt($ch, CURLOPT_CAINFO, $cacert_path);
		curl_setopt($ch, CURLOPT_CAPATH, $cacert_path);
		
		$contents = curl_exec($ch);
		if($contents === false)
		{
			$code = curl_errno($ch);
			$message = curl_error($ch);
			curl_close($ch);
			throw new \RuntimeException($message, $code);
		}
		
		curl_close($ch);
		
		$count = file_put_contents($cacert_path, $contents);
		if($count === false)
			throw new \RuntimeException('Impossible to update cacert.pem file.');
		
		echo "success.\n";
	}
	
	/**
	 * Updates the last updated date on the readme.
	 *
	 * @throws \RuntimeException
	 */
	protected function updateReadme()
	{
		echo "Updating readme... ";
		$readme = file_get_contents(dirname(__DIR__).'/README.md');
		if($readme === false)
			throw new \RuntimeException('Impossible to read readme.');
		
		$readme = preg_replace('#Last Updated Date : \d{4}-\d{2}-\d{2}#ui', 'Last Updated Date : '.date('Y-m-d'), $readme);
		
		$count = file_put_contents(dirname(__DIR__).'/README.md', $readme);
		if($count === false)
			throw new \RuntimeException('Impossible to write readme.');
		
		echo "success\n";
	}
	
	/**
	 * Adds the commit to git
	 */
	protected function commitToGit()
	{
		echo "Commiting to git... ";
		chdir(dirname(__DIR__));
		exec('git add --all');
		exec('git commit -m "Automatic update at '.date('Y-m-d').'"');
		echo "success\n";
	}
	
	/**
	 * Tags current commit with the newest tag value.
	 *
	 * @throws \RuntimeException
	 */
	protected function updateTagToGit()
	{
		echo "Updating git tag... ";
		chdir(dirname(__DIR__));
		$matches = array();
		if(preg_match('#(\d+)-(\d+)-(\d+)#', $this->_actual_tag, $matches))
		{
			$newtag = $matches[1].'.'.$matches[2].'.'.(((int) $matches[3]) + 1);
		}
		else throw new \RuntimeException('Impossible to parse git tag.');
		
		exec('git tag -a "'.$newtag.'" -m "Automatic update at '.date('Y-m-d').'"');
		echo "success\n";
	}
	
}
