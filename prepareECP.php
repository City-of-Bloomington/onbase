<?php
/**
 * @copyright 2021 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
$csv = fopen('./ecp.csv',   'r');
$idx = fopen('./index.txt', 'w');

// Column order in the CSV file
define('BOX_NUM',   0);
define('TITLE',     1);
define('FILE_PATH', 2);

// OnBase ID numbers
$FORMAT_PDF  = 16;
$DOCTYPE_DSA = 139;
$DOCTYPE_DSO = 138;
$DOCTYPE_ECP = 143;

$c = 0;
while (($row = fgetcsv($csv, 1024, ',', '"')) !== false) {
    fwrite($idx, "BEGIN:\n");
    fwrite($idx, "DOCTYPE:  $DOCTYPE_ECP\n");
    fwrite($idx, "FORMAT:   $FORMAT_PDF\n");
    fwrite($idx, "FILE:     {$row[FILE_PATH]}\n");
    fwrite($idx, "BOX:      {$row[BOX_NUM]}\n");
    fwrite($idx, "TITLE:    {$row[TITLE]}\n");
}
