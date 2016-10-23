<?php
require_once __DIR__ . '/include/git.php';
require_once __DIR__ . '/include/JsonSchema.php';
require_once __DIR__ . '/include/InterfaceBuilder.php';

$schemas = array();

function _mysql_escape_string($string)
{
    //if (function_exists('mysql_escape_string')) {
    //    $escaped =  @mysql_escape_string($string);
    //} else {
    $escaped = addcslashes($string, "\n\r'\;\\");
    //}   
    return $escaped;
}

foreach ($isos as $iso) {
    $schemas[$iso] = new JsonSchema(__DIR__ . '/iso-codes/data/schema-' . $iso . '.json', $iso);
}

$out  = "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
$out .= "SET time_zone = \"+00:00\";\n\n";
$out .= "USE isocodes;\n\n";
// Create tables
foreach ($schemas as $iso => $schema) {
    $out .= "CREATE TABLE `iso_" . preg_replace('/\-/', '_', $schema->getIso()) . "` (\n";
    
    $fields = array();
    
    foreach ($schema->getProperties() as $name => $value) {
        $field = "    `" . $name . "` VARCHAR(255)";
        if ($schema->isRequired($name)) {
            $field .= " NOT NULL";
        } else {
            $field .= " DEFAULT NULL";
        }
    
        if (isset($value['description']) && !empty($value['description'])) {
            $field .= " COMMENT '" . _mysql_escape_string($value['description']) . "'";
        }
    
        $fields[] = $field;
    }
    
    $out .= implode(",\n", $fields) . "\n";
    
    $out .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8;\n\n";
    
    // Now the insert statement
    $insertStatement = "INSERT INTO `iso_" . preg_replace('/\-/', '_', $schema->getIso()) . "` (";
    
    $fields = array_keys($schema->getProperties());
    $insertStatement .= "`" . implode("`, `", $fields) . "`";
    
    $insertStatement .= ") VALUES\n";
    
    $out .= $insertStatement;
    
    // Load the data JSON file
    $data = json_decode(file_get_contents(__DIR__ . '/iso-codes/data/iso_' . $iso . '.json'), true);
    $data = $data[$iso];
    
    $allValues = array();
    
    foreach($data as $row) {
        $values = array();
        foreach($fields as $field) {
            if (isset($row[$field])) {
                if (empty($row[$field])) {
                    if ($schema->isRequired($field)) {
                        $values[] = "''";
                    } else {
                        $values[] = "NULL";
                    }
                } else {
                    $values[] = "'" . _mysql_escape_string($row[$field]) . "'";
                }
            } else {
                $values[] = "NULL";
            }
        }
        
        $allValues[] = "(" . implode(", ", $values) . ")";
    }
    
    // WAIT! I must split long inserts
    /*
    $maxRows = 100;
    if (count($allValues) > $maxRows) {
        while (count($allValues) > $maxRows) {
            $smallArray = array();
            for ($i = 0; $i < $maxRows; $i++) {
                $smallArray[] = array_shift($allValues);
            }
            
            $out .= implode(",\n", $smallArray) . ";\n\n";
            
            $out .= $insertStatement;
        }
    }
    */
    $out .= implode(",\n", $allValues) . ";\n\n";
    
    
}
    
file_put_contents(dirname(__DIR__) . '/mysql-data.sql', $out);