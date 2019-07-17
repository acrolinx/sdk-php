# SDK-PHP

This library is meant to be used to interact with the Acrolinx Platform API in embedded integrations.
It does NOT offer an interface to work with the Acrolinx Sidebar.

Use [AcrolinxEndpoint](api.md) methods to interact with the Acrolinx Platform API.

## Prerequisites

Please contact [Acrolinx SDK support](https://github.com/acrolinx/acrolinx-coding-guidance/blob/master/topics/sdk-support.md)
for consulting and getting your integration certified.
The tests in this SDK work with a test license on an internal Acrolinx URL.
This license is only meant for demonstration and developing purposes.
Once you finished your integration, you'll have to get a license for your integration from Acrolinx.
  
Acrolinx offers different other SDKs, and examples for developing integrations.

Before you start developing your own integration, you might benefit from looking into:

* [Getting Started with Custom Integrations](https://docs.acrolinx.com/customintegrations),
* the [Guidance for the Development of Acrolinx Integrations](https://github.com/acrolinx/acrolinx-coding-guidance),
* the [Acrolinx Platform API](https://github.com/acrolinx/platform-api)
* the [Rendered Version of Acrolinx Platform API](https://acrolinxapi.docs.apiary.io/#)
* the [Acrolinx SDKs](https://github.com/acrolinx?q=sdk), and
* the [Acrolinx Demo Projects](https://github.com/acrolinx?q=demo).

## Prerequisites to Develop This Project

* Have php7 installed
* Have php-curl installed
* Have composer installed [Find a tutorial here.](https://www.hostinger.com/tutorials/how-to-install-composer)


## Start Developing

* run `composer install` to install dependencies
* run `composer update` to update dependencies

## Run Tests
* rename the **.env.conf** file to **.env**
* set **ACROLINX_TEST_SERVER_URL** and **ACROLINX_ACCESS_TOKEN** in this file
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
