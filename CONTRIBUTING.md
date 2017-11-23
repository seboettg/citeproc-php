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
* Make sure that your test case covers relevant code parts
* Implement the code until all tests finishing successfully
* Send a pull request
