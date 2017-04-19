# citeproc-php #
[![PHP](https://img.shields.io/badge/PHP-%3E=5.6-green.svg?style=flat)](http://docs.php.net/manual/en/migration53.new-features.php)
[![License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat)](https://opensource.org/licenses/MIT)  
[![Build Status](https://travis-ci.org/seboettg/citeproc-php.svg)](https://travis-ci.org/seboettg/citeproc-php)
[![Coverage Status](https://coveralls.io/repos/github/seboettg/citeproc-php/badge.svg)](https://coveralls.io/github/seboettg/citeproc-php)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/seboettg/citeproc-php/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/seboettg/citeproc-php/?branch=master)

citeproc-php is full-featured CSL processor.

The Citation Style Language (CSL) is an XML-based format to describe the formatting of citations, notes and bibliographies, offering:

* An open format
* Compact and robust styles
* Extensive support for style requirements
* Automatic style localization
* Infrastructure for style distribution and updating
* Thousands of freely available styles (Creative Commons BY-SA licensed)

For additional documentation of CSL visit [http://citationstyles.org](http://citationstyles.org).

## Version 1.x ##

The old version 1.x (established by Ron Jerome) has been moved to [this Repository](https://github.com/seboettg/citeproc-php-old). 

## Version 2.x ##

Version 2.x is a completely new written version of citeproc-php. In this connection, the license of version 2.x has been changed from GPLv3 to MIT.

citeproc-php 2.x is still in development and not stable. Have a look at [tasks of milestone Version 2.0](https://github.com/seboettg/citeproc-php/milestone/1) in order to get an overview about finished and open features.

## Installing citeproc-php ##

The recommended way to install citeproc-php is through
[Composer](http://getcomposer.org).

```bash
# Install Composer
curl -sS https://getcomposer.org/installer | php
```

Next, run the Composer command to install the latest stable version of citeproc-php:

```bash
php composer.phar require seboettg/citeproc-php
```

After installing, you need to require Composer's autoloader:

```php
require 'vendor/autoload.php';
```

You can then later update citeproc-php using composer:

 ```bash
composer.phar update
 ```

## How to use citeproc-php ##

citeproc-php renders bibliographical metadata into citations or bibliographies using a stylesheet which defines the 
citation rules. 


### Get the metadata of your publications ###

Create a project folder:

```bash
mkdir mycslproject
cd mycslproject
```

First, you need json formatted metadata array of publication's metadata. There are a lot of Services that supports CSL exports. For instance [BibSonomy](https://www.bibsonomy.org) Zotero, Mendeley. If you don't use any of these services, you can use the following test data for a first step.

```javascript
[
    {
        "author":[
            {
                "family":"Knuth",
                "given":"Donald"
            }
        ],
        "id":"knuth73",
        "issued":{
            "date-parts":[["1973"]]
        },
        "publisher":"Addison-Wesley",
        "publisher-place":"Boston",
        "title":"The Art Of Computer Programming, vol. 3: Sorting And Searching",
        "type":"book"
    },
    {
        "author":[
            {
                "family":"Knuth",
                "given":"Donald E."
            }
        ],
        
        "edition":"Third",
        "event-place":"Boston",
        "id":"knuth97",
        "ISBN":"9780201896848",
        "issued":{
            "date-parts":[["1997"]]
        },
        "publisher":"Addison-Wesley",
        "publisher-place":"Boston",
        "title":"The Art of Computer Programming, Volume 2: Seminumerical Algorithms",
        "type":"book"
    },
    {
        "author":[
            {
                "family":"Friend",
                "given":"Edward H."
            }
        ],
        "container-title":"J. ACM",
        "DOI":"10.1145/320831.320833",
        "event-date":{
            "date-parts":[["1956","jul"]]
        },
        "event-place":"New York, NY, USA",
        "id":"Friend56",
        "ISSN":"0004-5411",
        "issue":"3",
        "issued":{
            "date-parts":[["1956","jul"]]
        },
        "number":"3",
        "number-of-pages":"34",
        "page":"134-168",
        "page-first":"134",
        "publisher":"ACM",
        "publisher-place":"New York, NY, USA",
        "title":"Sorting on Electronic Computer Systems",
        "type":"article-journal",
        "URL":"http://doi.acm.org/10.1145/320831.320833",
        "volume":"3"
    }
]
```

Copy this into a file in your project root and name the file `metadata.json`.

### Initialize your project ###
Second, initialize your project and get the dependant citeproc-php library using composer:

```bash
composer init
```

Now you can define and download required libraries:
 
```bash
composer require seboettg/citeproc-php
```

If you have trouble using composer you will find further information on [https://getcomposer.org/doc/](https://getcomposer.org/doc/).

### Build a first simple Script ###

```php
<?php



```

## Contribution ##