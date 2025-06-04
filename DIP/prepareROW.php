<?php
/**
 * @copyright 2021 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
$csv = fopen('./row.csv',   'r');
$idx = fopen('./index.txt', 'w');

// Column order in the CSV file
define('BOX_NUM',   0);
define('DOCID',     1);
define('TITLE',     2);
define('PARCEL',    3);
define('FILE_PATH', 4);

// OnBase ID numbers
$FORMAT_PDF  = 16;
$DOCTYPE_DSA = 139;
$DOCTYPE_DSO = 138;
$DOCTYPE_ECP = 143;
$DOCTYPE_ROW = 144;

$c = 0;
while (($row = fgetcsv($csv, 1024, ',', '"')) !== false) {
    $doc_id = substr($row[DOCID], 7);
    
    fwrite($idx, "BEGIN:\n");
    fwrite($idx, "DOCTYPE:  $DOCTYPE_ROW\n");
    fwrite($idx, "FORMAT:   $FORMAT_PDF\n");
    fwrite($idx, "FILE:     {$row[FILE_PATH]}\n");
    fwrite($idx, "BOX:      {$row[BOX_NUM]}\n");
    fwrite($idx, "TITLE:    {$row[TITLE]}\n");
    fwrite($idx, "DOCID:    $doc_id\n");
    fwrite($idx, "PARCEL:   {$row[PARCEL]}\n");
}
