<?php
require_once __DIR__ . '/include/git.php';
require_once __DIR__ . '/include/JsonSchema.php';

$schemas = array();

$dbh = new PDO('sqlite:' . dirname(__DIR__) . '/data/isocodes.sqlite');

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

// Create tables
foreach ($schemas as $iso => $schema) {
    $createTable = "CREATE TABLE IF NOT EXISTS [iso_" . preg_replace('/\-/', '_', $schema->getIso()) . "] (\n";
    
    $fields = array();
    
    foreach ($schema->getProperties() as $name => $value) {
        switch($iso)
        {
            case '15924':
                if (strcasecmp($name, 'numeric') === 0 ) {
                    $field = "    [" . $name . "] CHARACTER(3)";
                } elseif (strcasecmp($name, 'alpha_4') === 0 ) {
                    $field = "    [" . $name . "] CHARACTER(4)";
                } else {
                    $field = "    [" . $name . "] VARCHAR(255)";
                }
                break;
            case '3166-1':
                if (strcasecmp($name, 'numeric') === 0 ) {
                    $field = "    [" . $name . "] CHARACTER(3)";
                } elseif (strcasecmp($name, 'alpha_2') === 0 ) {
                    $field = "    [" . $name . "] CHARACTER(2)";
                } elseif (strcasecmp($name, 'alpha_3') === 0 ) {
                    $field = "    [" . $name . "] CHARACTER(3)";
                } else {
                    $field = "    [" . $name . "] VARCHAR(255)";
                }
                break;
            case '3166-2':
                if (strcasecmp($name, 'code') === 0 ) {
                    $field = "    [" . $name . "] CHARACTER(6)";
                } elseif (strcasecmp($name, 'parent') === 0 ) {
                    $field = "    [" . $name . "] CHARACTER(66)";
                } elseif (strcasecmp($name, 'type') === 0 ) {
                    $field = "    [" . $name . "] CHARACTER(64)";
                } else {
                    $field = "    [" . $name . "] VARCHAR(255)";
                }
                break;
            case '3166-3':
                if (strcasecmp($name, 'numeric') === 0 ) {
                    $field = "    [" . $name . "] CHARACTER(3)";
                } elseif (strcasecmp($name, 'alpha_3') === 0 ) {
                    $field = "    [" . $name . "] CHARACTER(3)";
                } elseif (strcasecmp($name, 'alpha_4') === 0 ) {
                    $field = "    [" . $name . "] CHARACTER(4)";
                } elseif (strcasecmp($name, 'withdrawal_date') === 0 ) {
                    $field = "    [" . $name . "] DATE";
                } else {
                    $field = "    [" . $name . "] VARCHAR(255)";
                }
                break;
            case '4217':
                if (strcasecmp($name, 'numeric') === 0 ) {
                    $field = "    [" . $name . "] CHARACTER(3)";
                } elseif (strcasecmp($name, 'alpha_3') === 0 ) {
                    $field = "    [" . $name . "] CHARACTER(3)";
                } else {
                    $field = "    [" . $name . "] VARCHAR(255)";
                }
                break;
            case '639-2':
                if (strcasecmp($name, 'alpha_2') === 0 ) {
                    $field = "    [" . $name . "] CHARACTER(2)";
                } elseif (strcasecmp($name, 'alpha_3') === 0 ) {
                    $field = "    [" . $name . "] CHARACTER(3)";
                } elseif (strcasecmp($name, 'bibliographic') === 0 ) {
                    $field = "    [" . $name . "] CHARACTER(3)";
                } else {
                    $field = "    [" . $name . "] VARCHAR(255)";
                }
                break;
            case '639-3':
                if (strcasecmp($name, 'alpha_2') === 0 ) {
                    $field = "    [" . $name . "] CHARACTER(2)";
                } elseif (strcasecmp($name, 'alpha_3') === 0 ) {
                    $field = "    [" . $name . "] CHARACTER(3)";
                } elseif (strcasecmp($name, 'bibliographic') === 0 ) {
                    $field = "    [" . $name . "] CHARACTER(3)";
                } elseif (strcasecmp($name, 'type') === 0 ) {
                    $field = "    [" . $name . "] CHARACTER(1)";
                } elseif (strcasecmp($name, 'scope') === 0 ) {
                    $field = "    [" . $name . "] CHARACTER(1)";
                } else {
                    $field = "    [" . $name . "] VARCHAR(255)";
                }
                break;
            case '639-5':
                if (strcasecmp($name, 'alpha_3') === 0 ) {
                    $field = "    [" . $name . "] CHARACTER(3)";
                } else {
                    $field = "    [" . $name . "] VARCHAR(255)";
                }
                break;
            default:
                if (strcasecmp($name, 'numeric') === 0 ) {
                    $field = "    [" . $name . "] CHARACTER(3)";
                } elseif (strcasecmp($name, 'alpha_2') === 0 ) {
                    $field = "    [" . $name . "] CHARACTER(2)";
                } elseif (strcasecmp($name, 'alpha_3') === 0 ) {
                    $field = "    [" . $name . "] CHARACTER(3)";
                } elseif (strcasecmp($name, 'alpha_4') === 0 ) {
                    $field = "    [" . $name . "] CHARACTER(4)";
                } else {
                    $field = "    [" . $name . "] VARCHAR(255)";
                }
        }
        
        if ($schema->isRequired($name)) {
            $field .= " NOT NULL";
        } else {
            $field .= " NULL";
        }
    
        $fields[] = $field;
    }
    
    $createTable .= implode(",\n", $fields) . "\n";
    
    $createTable .= ")";
    
    $dbh->exec($createTable);
    
    switch($iso)
    {
        case '15924':
            $dbh->exec('CREATE UNIQUE INDEX UIX_ISO_15924_ALPHA_4 on iso_15924 (alpha_4);');
            $dbh->exec('CREATE UNIQUE INDEX UIX_ISO_15924_NUMERIC on iso_15924 (numeric);');
            break;
        case '3166-1':
            $dbh->exec('CREATE UNIQUE INDEX UIX_ISO_3166_1_ALPHA_2 on iso_3166_1 (alpha_2);');
            $dbh->exec('CREATE UNIQUE INDEX UIX_ISO_3166_1_ALPHA_3 on iso_3166_1 (alpha_3);');
            $dbh->exec('CREATE UNIQUE INDEX UIX_ISO_3166_1_NUMERIC on iso_3166_1 (numeric);');
            break;
        case '3166-2':
            $dbh->exec('CREATE UNIQUE INDEX UIX_ISO_3166_2_CODE on iso_3166_2 (code);');
            $dbh->exec('CREATE INDEX IX_ISO_3166_2_TYPE on iso_3166_2 (type);');
            $dbh->exec('CREATE INDEX IX_ISO_3166_2_PARENT on iso_3166_2 (parent);');
            break;
        case '3166-3':
            $dbh->exec('CREATE UNIQUE INDEX UIX_ISO_3166_3_ALPHA_3 on iso_3166_3 (alpha_3);');
            $dbh->exec('CREATE UNIQUE INDEX UIX_ISO_3166_3_ALPHA_4 on iso_3166_3 (alpha_4);');
            $dbh->exec('CREATE INDEX IX_ISO_3166_3_NUMERIC on iso_3166_3 (numeric);');
            break;
        case '4217':
            $dbh->exec('CREATE UNIQUE INDEX UIX_ISO_4217_ALPHA_3 on iso_4217 (alpha_3);');
            $dbh->exec('CREATE UNIQUE INDEX UIX_ISO_4217_NUMERIC on iso_4217 (numeric);');
            break;
        case '639-2':
            $dbh->exec('CREATE INDEX IX_ISO_639_2_ALPHA_2 on iso_639_2 (alpha_2);');
            $dbh->exec('CREATE INDEX IX_ISO_639_2_BIBLIOGRAPHIC on iso_639_2 (bibliographic);');
            $dbh->exec('CREATE UNIQUE INDEX UIX_ISO_639_2_ALPHA_3 on iso_639_2 (alpha_3);');
            $dbh->exec('CREATE UNIQUE INDEX UIX_ISO_639_2_NUMERIC on iso_639_2 (numeric);');
            break;
        case '639-3':
            $dbh->exec('CREATE INDEX IX_ISO_639_3_ALPHA_2 on iso_639_3 (alpha_2);');
            $dbh->exec('CREATE INDEX IX_ISO_639_3_BIBLIOGRAPHIC on iso_639_3 (bibliographic);');
            $dbh->exec('CREATE INDEX IX_ISO_639_3_TYPE on iso_639_3 (type);');
            $dbh->exec('CREATE INDEX IX_ISO_639_3_SCOPE on iso_639_3 (scope);');
            $dbh->exec('CREATE UNIQUE INDEX UIX_ISO_639_3_ALPHA_3 on iso_639_3 (alpha_3);');
            break;
        case '639-5':
            $dbh->exec('CREATE UNIQUE INDEX UIX_ISO_639_5_ALPHA_3 on iso_639_5 (alpha_3);');
            break;
    }
    
    
    // Now the insert statement
    $insertStatement = "INSERT INTO [iso_" . preg_replace('/\-/', '_', $schema->getIso()) . "] (";
    
    $fields = array_keys($schema->getProperties());
    $insertStatement .= "" . implode(", ", $fields) . "";
    
    $insertStatement .= ") VALUES ";
    
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
        
        //$allValues[] = "(" . implode(", ", $values) . ")";
        $dbh->exec($insertStatement . '(' . implode(", ", $values) . ');');
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
    $insertStatement .= implode(",\n", $allValues);
    
    //echo $insertStatement;
    
   
}