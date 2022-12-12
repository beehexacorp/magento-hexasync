# HexaSync Magento 2 connector module

Operating your business while having to deal with disconnected systems can be quite time-consuming and costly. Our HexaSync Integration Platform, acting as a middleware, will help your businesses automate your operations seamlessly by connecting perfectly to both your legacy systems and modern SaaS applications.

Connecting data between Magento 2 eCommerce Websites with ERP, CRM, POS, ACCOUNTING, and MARKETPLACES systems takes a lot of time, and money and involves spending numerous resources. Streamline your Magento 2 eCommerce operations with our fully managed middleware platform. Our HexaSync stays between Magento 2 and any other systems that help automate the data-sharing process and reduce the possibility of human error so that your company could focus on effectiveness and revenue. 

Through HexaSync, businesses save a lot of time and resources as business processes run seamlessly and efficiently:
Faster and more accurate customer management process
- Automated inventory management
- Sales process updated and automated in real time
- This is core modules for HexaSync's modules. Which adding Menu, Configuration section.


#### Website: https://www.beehexa.com/

``beehexacorp/module-hexa-sync``

 - [Main Functionalities](#markdown-header-main-functionalities)
 - [Installation](#markdown-header-installation)
 - [Configuration](#markdown-header-configuration)
 - [Specifications](#markdown-header-specifications)
 
## Main Functionalities
 This is core modules for HexaSync's modules. 
 - Which adding Menu.
 - Configuration section.
 - Create an integration account on Magento.
 - Create a connection to HexaSync platform.
 
## Installation
\* = in production please use the `--keep-generated` option

### Type 1: Zip file
 - Unzip the zip file in `app/code/Beehexa`
 - Enable the module by running `php bin/magento module:enable Beehexa_HexaSync`
 - Apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`

### Type 2: Composer
 - Install the module composer by running `composer require beehexacorp/module-hexa-sync`
 - enable the module by running `php bin/magento module:enable Beehexa_HexaSync`
 - apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`

## Configuration
Store -> Configuration -> Beehexa Corp

## Specifications
 - Cronjob
	- beehexa_base_fetching_news
