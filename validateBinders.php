<?php
/**
 * @copyright 2020 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);

define('FIELD_FILE',   0);
define('FIELD_BOARD',  1);
define('FIELD_YEAR',   2);
define('FIELD_TITLE',  3);
define('FIELD_NUMBER', 4);

$file   = fopen('./CommissionArchives.csv', 'r');
$c      = 0;
while (($data = fgetcsv($file)) !== false) {
    $c++;
    if ($c == 1) { continue; }
    if (!is_file('./binders/'.$data[FIELD_FILE])) {
        echo "File missing: {$data[FIELD_FILE]}\n";
    }
}
