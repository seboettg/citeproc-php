# citeproc-php

This is an effort to implement a full-featured standalone CSL processor in PHP. This program can be used to render bibliographies using [CSL](http://citationstyles.org/) templates. This sources are a fork of the [implementation of rjerome](https://bitbucket.org/rjerome/citeproc-php).

Some advantages:

* uses Composer
* each class is located in a separate file
* uses namespaces
* uses the autoloader of Composer
* uses PHPUnit for testing

## Installation

### Setup to use citeproc-php as library ###

Use Composer to add citeproc-php to your app:

```
$ composer require academicpuma/citeproc-php
```

### Setup to work on citeproc-php ###

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