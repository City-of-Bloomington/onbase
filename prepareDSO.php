<?php
/**
 * @copyright 2021 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
$csv = fopen('./dso.csv',   'r');
$idx = fopen('./index.txt', 'w');

// Column order in the CSV file
define('DOCTYPE',   0);
define('BOX_NUM',   1);
define('CASE_NUM',  2);
define('TITLE',     3);
define('LOCATION',  4);
define('FILE_PATH', 5);

// OnBase ID numbers
$FORMAT_PDF  = 16;
$DOCTYPE_DSA = 139;
$DOCTYPE_DSO = 138;

$case_num_format   = '/^[A-Z]{1,3}-\d{1,3}-\d{1,2}$/';
$permit_num_format = '/^[A-Z]\d{2}-\d{3,4}$/';

$c = 0;
while (($row = fgetcsv($csv, 1024, ',', '"')) !== false) {
    fwrite($idx, "BEGIN:\n");
    fwrite($idx, "DOCTYPE:  $DOCTYPE_DSO\n");
    fwrite($idx, "FORMAT:   $FORMAT_PDF\n");
    fwrite($idx, "FILE:     {$row[FILE_PATH]}\n");
    fwrite($idx, "BOX:      {$row[BOX_NUM]}\n");
    fwrite($idx, "TITLE:    {$row[TITLE]}\n");
    fwrite($idx, "LOCATION: {$row[LOCATION]}\n");

    $c++;
    foreach (explode('|', $row[CASE_NUM]) as $case_num) {
        $case_num = trim($case_num);

        if ($case_num) {
            if (preg_match($case_num_format, $case_num)) {
                fwrite($idx, "CASE: $case_num\n");
            }
            elseif (preg_match($permit_num_format, $case_num)) {
                fwrite($idx, "PERMIT: $case_num\n");
            }
            else {
                echo "Invalid case number $case_num in row $c\n";
                exit();
            }
        }

    }
}
