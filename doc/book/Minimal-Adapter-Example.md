# Minimal Adapter Example

In this example we search for a country by the alpha-2 country code and 
echo the name of the country in spanish if it has been found.

    use ISOCodes\ISO3166_1\Adapter\Json as ISO3166_1Adapter;
    
    require_once(__DIR__ . '/vendor/autoload.php');
    
    $a = new ISO3166_1Adapter();
    $c = $a->get('es');
    
    if (null !== $c) {
        echo $c->getName('es_ES');
    } else {
        echo 'Country not found!';
    }

Returned text is:

`España`