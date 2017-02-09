# PHP ISO Codes
[![Build Status](https://travis-ci.org/php-components/isocodes.svg?branch=master)](https://travis-ci.org/php-components/isocodes)

PHP ISO codes based on the open source [iso-codes](https://pkg-isocodes.alioth.debian.org/) project.

This project includes:

- ISO-15924
- ISO-3166-1
- ISO-3166-2
- ISO-3166-3
- ISO-4217
- ISO-639-2
- ISO-639-3
- ISO-639-5

...and the appropiate translations which are managed through the [Translation Project](https://www.translationproject.org/html/welcome.html).

## Installation
The easiest and recommended way to install this library is through [composer](http://getcomposer.org/).

Add the library to your composer requirements

    composer require php-components/isocodes

Then tell composer to download any new requirement

    composer update

## Adapters
Adapters are used in order to load the ISO data. Currently we support the following adapters:

- Json
- Pdo

However there are interfaces in place so you can write your own custom adapter. If you write your own adapter keep in mind it must inject the translator object into the objects.

The `get($code)` will determine which type of code has been supplied as an argument and will search for that type of code. The allowed types are the Alpha-2, Alpha-3, Alpha-4 and Numeric codes (If available in the requested ISO).

Some ISO files, such as ISO-639 have additional codes that match the pattern of Alpha-2, Alpha-3, Alpha-4 or Numeric codes. For those special cases an additional method is added to their adapters.

### JSON Adapter
This adapter makes use of [iso-codes](https://pkg-isocodes.alioth.debian.org/) JSON files to provide the ISO data. The data is stored as an array inside the adapter interfaces.

#### Usage Example:

    use ISOCodes\ISO3166_1\Adapter\Json as ISO3166_1Adapter;
    
    $adapter = new ISO3166_1Adapter();
    // Get country with Alpha-2 code 'es' (Spain)
    $country = $adapter->get('es');
    
    if (null !== $country) {
        // Get the country name in Spanish ('es')
        echo $country->getName('es');
    } else {
        echo 'Country not found!';
    }

This shall return `España`

### PDO Adapter
This adapter makes use of PDO to retrieve the ISO data from a database backend. By default it will use the included SQLite database but you may specify another PDO in the constructor of the adapter. The constuctor is defined as follows:

    public function __construct(PDO $pdo = null)

`$pdo` will be you PDO object or you can leave it as null in order to load the default SQLite database.

#### Usage Example

    use ISOCodes\ISO3166_1\Adapter\Pdo as ISO3166_1Adapter;
    
    $adapter = new ISO3166_1Adapter();
    // Get country with Alpha-2 code 'es' (Spain)
    $country = $adapter->get('es');
    
    if (null !== $country) {
        // Get the country name in Spanish ('es')
        echo $country->getName('es');
    } else {
        echo 'Country not found!';
    }

This shall return `España`
