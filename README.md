# sdk-php
PHP SDK for working with Acrolinx (without Sidebar) 

## Prerequisites

* Have php7 installed
* Have php-curl installed
* Have composer installed [Find a tutorial here.](https://www.hostinger.com/tutorials/how-to-install-composer)

## Start developing

* run `composer install` to install dependencies
* run `composer update` to update dependencies

## Run tests
* rename the **.env.conf** file to **.env**
* set **ACROLINX_TEST_SERVER_URL** and **ACROLINX_ACCESS_TOKEN** in this file
* run `composer test` to run unit tests
* DON'T check your personal **.env** file into git
