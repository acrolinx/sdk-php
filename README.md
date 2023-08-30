# SDK-PHP [![CI SDK PHP](https://github.com/acrolinx/sdk-php/actions/workflows/main.yml/badge.svg)](https://github.com/acrolinx/sdk-php/actions/workflows/main.yml)

[![Latest Stable Version](https://poser.pugx.org/acrolinx/sdk/v/stable)](https://packagist.org/packages/acrolinx/sdk)
[![Total Downloads](https://poser.pugx.org/acrolinx/sdk/downloads)](https://packagist.org/packages/acrolinx/sdk)
[![License](https://poser.pugx.org/acrolinx/sdk/license)](https://packagist.org/packages/acrolinx/sdk)
[![composer.lock available](https://poser.pugx.org/acrolinx/sdk/composerlock)](https://packagist.org/packages/acrolinx/sdk)

This library is meant to be used to interact with the Acrolinx Platform API in automated integrations.

## Get Started with Your Integration

### Prerequisites

Please contact [Acrolinx SDK support](https://github.com/acrolinx/acrolinx-coding-guidance/blob/master/topics/sdk-support.md)
for consulting and getting your integration certified.
The tests in this SDK work with a test license on an internal Acrolinx URL.
This license is only meant for demonstration and developing purposes.
Once you finished your integration, you'll have to get a license for your integration from Acrolinx.
  
Acrolinx offers different other SDKs, and examples for developing integrations.

Before you start developing your own integration, you might benefit from looking into:

* [Build With Acrolinx](https://support.acrolinx.com/hc/en-us/categories/10209837818770-Build-With-Acrolinx),
* the [Guidance for the Development of Acrolinx Integrations](https://github.com/acrolinx/acrolinx-coding-guidance),
* the [Acrolinx Platform API](https://github.com/acrolinx/platform-api)
* the [Rendered Version of the Acrolinx Platform API](https://acrolinxapi.docs.apiary.io/#)
* the [Acrolinx SDKs](https://github.com/acrolinx?q=sdk), and
* the [Acrolinx Demo Projects](https://github.com/acrolinx?q=demo).

### Start Developing

`composer require acrolinx/sdk`

Use the [`AcrolinxEndpoint`](api.md) methods to interact with the Acrolinx Platform API.

Our [tests](tests/AcrolinxEndpointTest.php) cover a lot of use cases already. Use them for inspiration.

## Contributing to This SDK

### Prerequisites

* Have PHP 8.0 or above installed
* Have php-curl installed
* Have composer installed [Find a tutorial here.](https://www.hostinger.com/tutorials/how-to-install-composer)

### Start Developing

* run `composer install` to install dependencies
* run `composer update` to update dependencies

### Run Tests

* rename the `.env.conf`file to `.env`
* set `ACROLINX_TEST_SERVER_URL` and `ACROLINX_ACCESS_TOKEN` in this file. ([Get an Access Token](https://support.acrolinx.com/hc/en-us/articles/10210854753042-Get-an-Access-Token))
* set `ACROLINX_SSO_USER` and `ACROLINX_SSO_PASSWORD` (You can request the SSO password from Acrolinx support)
* set `ACROLINX_DEV_SIGNATURE` to unique signature provided for your integration. (Note: Signature and access token are different)
* run `composer test` to run unit tests

## License

Copyright 2019-present Acrolinx GmbH

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at:

[http://www.apache.org/licenses/LICENSE-2.0](http://www.apache.org/licenses/LICENSE-2.0)

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.

For more information visit: [https://www.acrolinx.com](https://www.acrolinx.com)
