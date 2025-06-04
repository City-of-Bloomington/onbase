<?php
/**
 * @copyright 2021 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
$csv = fopen('./dsa.csv',   'r');
$idx = fopen('./index.txt', 'w');

// Column order in the CSV file
define('DOCTYPE',   0);
define('BOX_NUM',   1);
define('YEAR',      2);
define('CASE_NUM',  3);
define('TITLE',     4);
define('LOCATION',  5);
define('FILE_PATH', 6);

// OnBase ID numbers
$FORMAT_PDF  = 16;
$DOCTYPE_DSA = 139;
$DOCTYPE_DSO = 138;

$valid_format = '/^[A-Z\/]+-\d{1,3}-\d{1,2}$/';

$c = 0;
while (($row = fgetcsv($csv, 1024, ',', '"')) !== false) {
    fwrite($idx, "BEGIN:\n");
    fwrite($idx, "DOCTYPE:  $DOCTYPE_DSA\n");
    fwrite($idx, "FORMAT:   $FORMAT_PDF\n");
    fwrite($idx, "FILE:     {$row[FILE_PATH]}\n");
    fwrite($idx, "BOX:      {$row[BOX_NUM]}\n");
    fwrite($idx, "YEAR:     {$row[YEAR]}\n");
    fwrite($idx, "TITLE:    {$row[TITLE]}\n");
    fwrite($idx, "LOCATION: {$row[LOCATION]}\n");

    $c++;
    foreach (explode('|', $row[CASE_NUM]) as $case_num) {
        $case_num = trim($case_num);

        if ($case_num && !preg_match($valid_format, $case_num)) {
            echo "Invalid case number $case_num in row $c\n";
            exit();
        }
        fwrite($idx, "CASE: $case_num\n");
    }
}
