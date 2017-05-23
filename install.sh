#!/bin/bash

if [ -d ./vendor/citation-style-language/$1 ]
    then
        cd ./vendor/citation-style-language/$1
        git pull origin master
    else
        git clone --branch=master https://github.com/citation-style-language/$1.git vendor/citation-style-language/$1
fi