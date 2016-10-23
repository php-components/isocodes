<?php
if (!file_exists(dirname(__DIR__) . '/iso-codes/.git')) {
    chdir(dirname(__DIR__));
    exec('git clone https://anonscm.debian.org/git/pkg-isocodes/iso-codes.git');
}

if (!file_exists(dirname(__DIR__) . '/iso-codes/.git')) {
    echo "\n\n";
    echo "ERROR: Unable to fin ISO codes repository on your disk.\n";
    echo "       Do you have git installed on your system path?\n\n";
    die();
}

// ISOs contained in the repository
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