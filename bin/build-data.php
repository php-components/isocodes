<?php

if (!defined('__DIR__')) {
    define('__DIR__', dirname(__FILE__));
}

require(__DIR__ . '/include/php-mo.php');

if (!file_exists(__DIR__ . '/iso-codes/.git')) {
    exec('git clone https://anonscm.debian.org/git/pkg-isocodes/iso-codes.git');
} else {
    chdir(__DIR__ . '/iso-codes');
//    exec('git pull https://anonscm.debian.org/git/pkg-isocodes/iso-codes.git master');
    chdir(__DIR__);
}

// Copy language files
$isos = array(
    '15924',
    '3166-1',
    '3166-2',
    '3166-3',
    '4217',
    '639-2',
    '639-3',
    '639-5',
);

// copy data files
if (!file_exists(dirname(__DIR__) . '/data')) {
    mkdir(dirname(__DIR__) . '/data');
}

foreach ($isos as $iso) {
    $src = __DIR__ . '/iso-codes/data/iso_' . $iso . '.json';
    if (file_exists($src)) {
        copy($src, dirname(__DIR__) . '/data/iso_' . $iso . '.json');
    }
}

// make sure language directory exists
if (!file_exists(dirname(__DIR__) . '/language')) {
    mkdir(dirname(__DIR__) . '/language');
}

foreach ($isos as $iso) {
    if (!file_exists(dirname(__DIR__) . '/language/iso_' . $iso)) {
        mkdir(dirname(__DIR__) . '/language/iso_' . $iso);
    }
}

// Copy language files
foreach ($isos as $iso) {
    $src_path = __DIR__ . '/iso-codes/iso_' . $iso;
    if ($handle = opendir($src_path)) {
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
                $src       = $src_path . '/' . $entry;
                $dst       = dirname(__DIR__) . '/language/iso_' . $iso . '/' . $entry;
                
                $extension = pathinfo($src, PATHINFO_EXTENSION);
                
                if (strcasecmp($extension, 'po') === 0) {
                    // Copy *.po file
                    copy($src, $dst);
                    
                    // compile *.po file
                    phpmo_convert($dst);
                } elseif ( (strcasecmp($extension, 'pot') === 0) || (strcasecmp($extension, 'mo')) ) {
                    // Copy *.pot and *.mo file
                    copy($src, $dst);
                }
            }
        }
        
        closedir($handle);
    }
}
