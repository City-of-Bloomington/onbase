<?php
/**
 * @copyright 2021 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
$csv = fopen('./dsa.csv', 'r');
$idx = fopen('./dsa.txt', 'w');

define('DOCTYPE',   0);
define('BOX_NUM',   1);
define('YEAR',      2);
define('CASE_NUM',  3);
define('TITLE',     4);
define('LOCATION',  5);
define('FILE_PATH', 6);

$regex = '/^[A-Z]{1,3}-\d{1,3}-\d{1,2}$/';
// $string = 'P-32-68';

// if (preg_match($regex, $string)) {
//     echo "valid\n";
// }
// else {
//     echo "invalid\n";
// }

$c = 0;
while (($row = fgetcsv($csv, 1024, ',', '"')) !== false) {
    $c++;
    foreach (explode('|', $row[CASE_NUM]) as $case_num) {
        $case_num = trim($case_num);

        if ($case_num && !preg_match($regex, $case_num)) {
            echo "Invalid case number $case_num in row $c\n";
            exit();
        }
    }
}
