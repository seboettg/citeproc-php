# citeproc-php #
[![Latest Stable Version](https://poser.pugx.org/academicpuma/citeproc-php/v/stable)](https://packagist.org/packages/academicpuma/citeproc-php) 
[![Total Downloads](https://poser.pugx.org/academicpuma/citeproc-php/downloads)](https://packagist.org/packages/academicpuma/citeproc-php) 
[![Latest Unstable Version](https://poser.pugx.org/academicpuma/citeproc-php/v/unstable)](https://packagist.org/packages/academicpuma/citeproc-php) 
[![License](https://poser.pugx.org/academicpuma/citeproc-php/license)](https://packagist.org/packages/academicpuma/citeproc-php) 
[![PHP](https://img.shields.io/badge/PHP-%3E=5.3-green.svg?style=flat)](http://docs.php.net/manual/en/migration53.new-features.php)

**Description**

This is an effort to implement a standalone CSL processor in PHP. This program can be used to render bibliographies using [CSL](http://citationstyles.org/) Stylesheets. This repository is a fork of the [implementation of rjerome](https://bitbucket.org/rjerome/citeproc-php) (apparently no longer maintained).

Some advantages:

* uses Composer
* each class is located in a separate file
* uses namespaces
* uses the autoloader of Composer
* uses PHPUnit for testing

## Installing citeproc-php using Composer ##

Use Composer to add citeproc-php to your app:

```
$ composer require academicpuma/citeproc-php
```


## How to use citeproc-php ##

```
<?php
include 'vendor/autoload.php';
use \AcademicPuma\CiteProc\CiteProc;

$bibliographyStyleName = 'apa';
$lang = "en-US";

$csl = CiteProc::loadStyleSheet($bibliographyStyleName); // xml code of your csl stylesheet

$citeProc = new CiteProc($csl, $lang);

// $data is a JSON encoded string
echo $citeProc->render(json_decode($data));
?>
```

## Setup a workspace ##

```
$ cd /path/to/your/php/workspace
$ hg clone https://seboettg@bitbucket.org/seboettg/citeproc-php
$ composer install
```

You can setup your apache to see the test results on a webpage. Therefor you can configure your apache with an custom virtual host:

```
<VirtualHost *:80>
        ServerAdmin webmaster@localhost
        ServerName citeproc.local
        DocumentRoot /path/to/your/php/workspace/citeproc-php/tests
        ErrorLog "/var/log/apache2/citeproc.error.log"
        CustomLog "/var/log/apache2/citeproc.access.log" common

        <Directory /path/to/your/php/workspace/citeproc-php/tests>
                AllowOverride All
                Order allow,deny
                Allow from all
        </Directory>
</VirtualHost>
```
Replace '/path/to/your/php/workspace/' with your path and replace 'citeproc.local' with your favourite host name.

If you want to use your own host address don't forget to append them to ``/etc/hosts``:

```
127.0.0.1       citeproc.local localhost
```

Restart your apache

```
$ sudo apachectl restart
```

or 

```
$ sudo /etc/init.d/apache2 restart
```

Open your browser and enter your chosen host address, as soon as apache has finished.


## Known packages using citeproc-php ##

* [lib-php-kar](https://packagist.org/packages/unikent/lib-php-kar) (GitHub)
* [BibSonomy restclient-php](http://bitbucket.org/bibsonomy/restclient-php) (BitBucket)
