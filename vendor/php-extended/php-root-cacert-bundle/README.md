# php-root-cacert-bundle
A library to safely add the CA root certificates into your programmes ssl validations.

This library was made to answer a common error :
`SSL certificate problem: unable to get local issuer certificate`
without disabling the certificate when calling the resolving library (like cURL)
and without changing the server configuration in ini files.

This library uses the [https://curl.haxx.se/ca/cacert.pem](https://curl.haxx.se/ca/cacert.pem)
source file to update its contents. This library is updated once a week, every sunday.


## Last Updated Date : 2018-06-30


## Installation

The installation of this library is made via composer.
Download `composer.phar` from [their website](https://getcomposer.org/download/).
Then add to your composer.json :

```json
	"require": {
		...
		"php-extended/php-root-cacert-bundle": "^1",
		...
	}
```
Then run `php composer.phar update` to install this library.
The autoloading of all classes of this library is made through composer's autoloader.


## Basic Usage

You may use this library the following way:

```php

use PhpExtended\RootCacert\CacertBundle;

$ch = curl_init();

// set some curl options

$cacert_path = CacertBundle::getFilePath();

curl_setopt($ch, CURLOPT_CAINFO, $cacert_path);
curl_setopt($ch, CURLOPT_CAPATH, $cacert_path);

curl_exec($ch);

curl_close($ch);

```


## License

MIT (See [license file](LICENSE)).
