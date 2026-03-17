<?php
/**
 * @copyright 2026 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);

// Field order from filename
define('ADDRESS', 1);
define('NUMBER',  2);
define('BATCH',   3);

// OnBase ID numbers
$FORMAT_PDF   = 16;
$DOCTYPE_DSA  = 139;
$DOCTYPE_DSO  = 138;
$DOCTYPE_ECP  = 143;
$DOCTYPE_HAND = 147;

$idx = fopen('../../index.txt', 'w');
$dir = '../../2026-03-10';
foreach (glob("$dir/*.pdf") as $f) {
    echo "$f\n";
    $file = basename($f);
    $row  = explode('_', $file);

    $batch   = trim($row[BATCH  ]);
    $number  = trim($row[NUMBER ]);
    $address = trim($row[ADDRESS]);
    $address = str_replace('.', '', $address);

    fwrite($idx, "BEGIN:\n");
    fwrite($idx, "DOCTYPE:  $DOCTYPE_ECP\n");
    fwrite($idx, "FORMAT:   $FORMAT_PDF\n");
    fwrite($idx, "FILE:     2026-03-10\\$file\n");
    fwrite($idx, "DATE:     2026-03-10\n");
    fwrite($idx, "ADDRESS:  $address\n");
    fwrite($idx, "NUMBER:   $number\n");
    fwrite($idx, "BATCH:    $batch\n");
}
