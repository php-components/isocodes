# Minimal Model Example

In this example we load a country by hand into the ISO-3166-1 model class
and we add the translator. Usually this tasks are done automatically by
the ISO Manager and there is no need to do them by hand.

    <?php
    use ISOCodes\ISO3166_1\Model\ISO3166_1;
    use ISOCodes\I18n\Translator;

    $a = new ISO3166_1();

    $a->_translator = new Translator();
    $a->exchangeArray([
        'alpha_2'       => 'ES',
        'alpha_3'       => 'ESP',
        'name'          => 'Spain',
        'numeric'       => '724',
        'official_name' => 'Kingdom of Spain'
    ]);

    echo $a->getName('es');

Returned text is:

`España`