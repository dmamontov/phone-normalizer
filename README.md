[![Latest Stable Version](https://poser.pugx.org/dmamontov/phone-normalizer/v/stable.svg)](https://packagist.org/packages/dmamontov/phone-normalizer)
[![License](https://poser.pugx.org/dmamontov/phone-normalizer/license.svg)](https://packagist.org/packages/dmamontov/phone-normalizer)
[![Total Downloads](https://poser.pugx.org/dmamontov/phone-normalizer/downloads.svg)](https://packagist.org/packages/dmamontov/phone-normalizer)

Phone Normalizer
================

Pars, normalizes the phone number and results in a specified format.

## Requirements
* PHP version >=5.3.3

## Installation

1) Install [composer](https://getcomposer.org/download/)

2) Follow in the project folder:
```bash
composer require dmamontov/phone-normalizer ~1.0.0
```

In config `composer.json` your project will be added to the library `dmamontov/phone-normalizer`, who settled in the folder `vendor/`. In the absence of a config file or folder with vendors they will be created.

If before your project is not used `composer`, connect the startup file vendors. To do this, enter the code in the project:
```php
require 'path/to/vendor/autoload.php';
```

## Examples of use

``` php
$n = new PhoneNormalizer;
$n->loadCodes('config/codes.json');
$phone = $n->normalize('XXXXXXXXXXXXXX');
var_dump($phone->format('+#CC#(#c#)###-##-##'));
```

