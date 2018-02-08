# citeproc-php #
[![Latest Stable Version](https://poser.pugx.org/seboettg/citeproc-php/v/stable)](https://packagist.org/packages/seboettg/citeproc-php) 
[![Unstable Version](https://poser.pugx.org/seboettg/citeproc-php/v/unstable)](https://packagist.org/packages/seboettg/citeproc-php) 
[![Total Downloads](https://poser.pugx.org/seboettg/citeproc-php/downloads)](https://packagist.org/packages/seboettg/citeproc-php) 
[![License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat)](https://opensource.org/licenses/MIT)
[![Build Status](https://travis-ci.org/seboettg/citeproc-php.svg)](https://travis-ci.org/seboettg/citeproc-php/branches)
[![Coverage Status](https://coveralls.io/repos/github/seboettg/citeproc-php/badge.svg)](https://coveralls.io/github/seboettg/citeproc-php)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/seboettg/citeproc-php/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/seboettg/citeproc-php/)
![PHP](https://img.shields.io/badge/PHP-5.6-green.svg?style=flat)
![PHP](https://img.shields.io/badge/PHP-7.0-green.svg?style=flat)
![PHP](https://img.shields.io/badge/PHP-7.1-green.svg?style=flat)

citeproc-php is a full-featured CSL 1.0.1 processor that renders bibliographic metadata into html formatted citations or bibliographies using CSL stylesheets. citeproc-php renders bibliographies as well as citations (except of [Citation-specific Options](http://docs.citationstyles.org/en/stable/specification.html#citation-specific-options)).

## Citation Style Language CSL ##

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

## Installing citeproc-php ##

The recommended way to install citeproc-php is through
[Composer](http://getcomposer.org).

```bash
$ curl -sS https://getcomposer.org/installer | php
```
Add the following lines to your `composer.json` file in order to add required program libraries as well as CSL styles and locales:

```javascript
{
    "name": "vendor-name/program-name",
    "repositories": [
        {
            "type": "package",
            "package": {
                "name": "citation-style-language/locales",
                "version":"1.0.0",
                "source": {
                    "type": "git",
                    "url": "https://github.com/citation-style-language/locales.git",
                    "reference": "master"
                }
            }
        },
        {
            "type": "package",
            "package": {
                "name": "citation-style-language/styles-distribution",
                "version":"1.0.0",
                "source": {
                    "type": "git",
                    "url": "https://github.com/citation-style-language/styles-distribution.git",
                    "reference": "master"
                }
            }
        }
    ],
    "require": {
        "citation-style-language/locales":"@dev",
        "citation-style-language/styles-distribution":"@dev",
        "seboettg/citeproc-php": "^2"
    }
}
```

Next, run the Composer command to install the latest stable version of citeproc-php and its dependencies:

```bash
$ php composer.phar install --no-dev
```

After installing, you need to require Composer's autoloader:

```php
require 'vendor/autoload.php';
```

You can then later update citeproc-php using composer:

 ```bash
$ composer.phar update --no-dev
 ```

If you have trouble using composer you will find further information on [https://getcomposer.org/doc/](https://getcomposer.org/doc/).

## How to use citeproc-php ##

citeproc-php renders bibliographical metadata into html formatted citations or bibliographies using a stylesheet which defines the 
citation rules. 


### Get the metadata of your publications ###

Create a project folder:

```bash
$ mkdir mycslproject
$ cd mycslproject
```

First, you need json formatted metadata array of publication's metadata. There are a lot of services that supports CSL exports. For instance [BibSonomy](https://www.bibsonomy.org) Zotero, Mendeley. 
If you don't use any of these services, you can use the following test data for a first step.

```javascript
[
    {
        "author": [
            {
                "family": "Doe", 
                "given": "James", 
                "suffix": "III"
            }
        ], 
        "id": "item-1", 
        "issued": {
            "date-parts": [
                [
                    "2001"
                ]
            ]
        }, 
        "title": "My Anonymous Heritage", 
        "type": "book"
    },
    {
        "author": [
            {
                "family": "Anderson", 
                "given": "John"
            }, 
            {
                "family": "Brown", 
                "given": "John"
            }
        ], 
        "id": "ITEM-2", 
        "type": "book",
        "title": "Two authors writing a book"
    }
]
```
Copy this into a file in your project root and name that file `metadata.json`.

### Build a first simple script ###

```php
<?php
include "vendor/autoload.php";
use Seboettg\CiteProc\StyleSheet;
use Seboettg\CiteProc\CiteProc;

$data = file_get_contents("metadata.json");
$style = StyleSheet::loadStyleSheet("din-1505-2");
$citeProc = new CiteProc($style);
echo $citeProc->render(json_decode($data), "bibliography");
```

You can also render citations instead of bibliographies:

```php
echo $citeProc->render(json_decode($data), "citation");
```

### Filter Citations ###

Since version 2.1 you have also the possibility to apply a filter so that just specific citations appear.

```php
<p>This a wise sentence 
<?php echo $citeProc->render($data, "citation", json_decode('[{"id":"item-1"}]')); ?>.</p>
<p>This is the most wise setence 
<?php echo $citeProc->render($data, "citation", json_decode('[{"id":"item-1"},{"id":"ITEM-2"}]')); ?>.</p>
```

### Bibliography-specific styles using CSS ###

Some CSL stylesheets use bibliography-specific style options like hanging indents or alignments. To get an effect of these options you can render separated Cascading Stylesheets using CiteProc. 
You have to insert these styles within the `<head>` tag of your html output page.

```php
<?php
include "vendor/autoload.php";
use Seboettg\CiteProc\StyleSheet;
use Seboettg\CiteProc\CiteProc;

$data = file_get_contents("metadata.json");
$style = StyleSheet::loadStyleSheet("harvard-north-west-university");
$citeProc = new CiteProc($style);
$bibliography = $citeProc->render(json_decode($data), "bibliography");
$cssStyles = $citeProc->renderCssStyles();
?>
<html>
<head>
    <title>CSL Test</title>
    <style type="text/css" rel="stylesheet">
        <?php echo $cssStyles; ?>
    </style>
</head>
<body>
    <h1>Bibliography</h1>
    <?php echo $bibliography; ?>
</body>
</html>
```

Now, you can watch and test the output using PHP's internal web server:

```bash
$ php -S localhost:8080
```

Start your Browser and open the URL `http://localhost:8080`.

Under examples folder you will find another example script.


## Advanced usage of citeproc-php ##

Since version 2.1, citeproc-php comes with additional features that are not a part of the CSL specifications.

You can enrich bibliographies and citations with additional HTML tags to inject links (i.e. to set a link to an author's CV), or to add other html markup.

### Use Lambda Functions to setup citeproc-php in order to render advanced citations or bibliographies ###

```php
<?php
include "vendor/autoload.php";
use Seboettg\CiteProc\StyleSheet;
use Seboettg\CiteProc\CiteProc;

$data = file_get_contents("metadata.json");
$style = StyleSheet::loadStyleSheet("elsevier-vancouver");

// pimp the title
$titleFunction = function($cslItem, $renderedText) {
    return '<a href="https://example.org/publication/' . $cslItem->id . '">' . $renderedText . '</a>';
};

//pimp author names
$authorFunction = function($authorItem, $renderedText) {
    if (isset($authorItem->id)) {
        return '<a href="https://example.org/author/' . $authorItem->id . '">' . $renderedText . '</a>';
    }
    return $renderedText;
};
?>
```

As you can see, `$titleFunction` wraps the title and `$authorFunction` wraps author's name in a link.

Assign these functions to its associated CSL variable (in this case title and author) as follows.

```php
<?php
$additionalMarkup = [
    "title" => $titleFunction,
    "author" => $authorFunction
];

$citeProc = new CiteProc($style, "en-US", $additionalMarkup);
?>
<html>
<head>
    <title>CSL Test</title>
</head>
<body>
    <h1>Bibliography</h1>
    <?php echo $citeProc->render(json_decode($data), "bibliography"); ?>
</body>
</html>
```

You can also use custom Lambda Functions in order to enrich citations with additional HTML markup.

If you want to restrict citeproc-php to use a custom Lambda Function either for bibliographies or citations, or you want to apply different 
functions for both, you can define the array as follows:

```php
<?php 
$additionalMarkup = [
    "bibliography" => [
        "author" => $authorFunction,
        "title" => $titleFunction,
        "csl-entry" => function($cslItem, $renderedText) {
            return '<a id="' . $cslItem->id .'" href="#' . $cslItem->id .'"></a>' . $renderedText;
        }
    ],
    "citation" => [
        "citation-number" => function($cslItem, $renderedText) {
            return '<a href="#' . $cslItem->id .'">'.$renderedText.'</a>';
        }
    ]
];

$citeProc = new CiteProc($style, "en-US", $additionalMarkup);

?>
<p>This ia a wise sentence <?php echo $citeProc->render(json_decode($data), "citation", json_decode('[{"id":"item-1"}]')); ?>.</p>
<h3>Literature</h3>
<?php echo $citeProc->render(json_decode($data), "bibliography");

```
In this example each entry of the bibliography gets an anchor by its `id` and the citation (in elsevier-vancouver style [1]) gets an URL with a fragment by its `id`. Hence, every citation mark gets a link to its entry in the bibliography.
Further examples you will find in the example folder.

### Good to know ###
* A custom Lambda Function must have two parameters (`function ($item, $renderedValue) { ... }`) in their signature and must return a string.
* The 1st parameter of a custom Lambda Function is the item (either a citation item or a name item. Both of type `\stdClass`). The 2nd parameter is the rendered result of the associated item.
* Custom Lambda Functions may be applied on all Standard Variables (according to the [CSL specification](http://docs.citationstyles.org/en/1.0.1/specification.html#standard-variables)).
* Custom Lambda Functions may be applied on all Name Variables (according to the [CSL specification](http://docs.citationstyles.org/en/1.0.1/specification.html#name-variables)). Be aware, just one name item will passed as parameter instead of the full citation item.
* Custom Lambda Function for Number Variables or Date Variables will be ignored.
* ```csl-entry``` is not a valid variable according to the CSL specifications. citeproc-php use ```csl-entry``` to hook in and apply a custom Lambda Function after a whole citation item or bibliography entry is rendered. 

## Contribution ##

citeproc-php is an Open Source project. You can support it by reporting bugs, contributing code or contributing documentation.

### Star the Repo ###
Developing software is a hard job and one has to spend a lot of time. Every open-source developer is looking forward 
about esteem for his work. If you use citeproc-php and if you like it, star it and talk about it in Blogs.

### Reporting a Bug ###
Use the [Issue Tracker](https://github.com/seboettg/citeproc-php/issues) in order to report a bug.

### Contribute Code ###
You are a developer and you like to help developing new features or bug fixes? Fork citeproc-php, setup a workspace and send
a pull request.

I would suggest the following way:

* Fork citeproc-php on Github
* Clone the forked repo
```bash
$ git clone https://github.com/<yourname>/citeproc-php
``` 
* Setup your preferred IDE
* Run the UnitTests within your IDE
* Write a test case for your issue. My tests are based on the original [test-suite](https://github.com/citation-style-language/test-suite). You can build custom (human-readable) test cases following the described [Fixture layout](https://github.com/citation-style-language/test-suite#fixture-layout). 
* Additionally, you have to translate (human-readable) test-cases into json format (machine-readable)
```bash
$ cd <project-root>/tests/fixtures/basic-tests
$ ./processor.py -g
```
* create a test function within an already existing test class or create a new test class:
```php
<?php 
namespace Seboettg\CiteProc;
use PHPUnit\Framework\TestCase;

class MyNewClassTest extends TestCase
{
    use TestSuiteTestCaseTrait;
    // ...
    public function testMyBrandNewFunction() 
    {
        //my brand new function is the file name (without file extension)
        $this->_testRenderTestSuite("myBrandNewFunction");
    }
    // ...
}
```
* Implement or adapt your code as long as all tests finishing successfully
* Make sure that your test case covers relevant code parts
* Send a pull request

## Testing ##

You can also run test cases without IDE:

```bash
$ composer test
```
