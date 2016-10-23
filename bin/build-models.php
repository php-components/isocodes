<?php
require_once __DIR__ . '/include/git.php';
require_once __DIR__ . '/include/JsonSchema.php';
require_once __DIR__ . '/include/InterfaceBuilder.php';

$schemas = array();

foreach ($isos as $iso) {
    $schemas[$iso] = new JsonSchema(__DIR__ . '/iso-codes/data/schema-' . $iso . '.json', $iso);
}

foreach ($schemas as $iso => $schema) {
    $interfaceBuilder = new InterfaceBuilder();
    
    $normalizedISO = preg_replace('/\-/', '_', $schema->getIso());
    $namespace     = "ISOCodes\\ISO" . $normalizedISO . "\\Model";
    $className     = "ISO" . $normalizedISO;
    
    $interfaceBuilder->loadSchema($schema, $namespace, $className );
    
    // interface
    $data = $interfaceBuilder->build(true);
    file_put_contents(dirname(__DIR__) . '/src/ISO' . $normalizedISO . '/Model/' . $className . "Interface.php"  , $data);
    
    // class
    $data = $interfaceBuilder->build(false);
    file_put_contents(dirname(__DIR__) . '/src/ISO' . $normalizedISO . '/Model/' . $className . ".php"  , $data);
}

