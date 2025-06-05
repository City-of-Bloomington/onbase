<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);

if (!defined('SITE_HOME')) { define('SITE_HOME', realpath(__DIR__.'/../../data')); }
$config    = json_decode(file_get_contents(SITE_HOME.'/config.json'), true);
$originals = realpath(SITE_HOME.'/../');
$output    = SITE_HOME.'/HAND';
if (!is_dir($output)) { mkdir($output); }

$batches   = ['2024-08-05', '2023-06-30'];
foreach ($batches as $batch) {
    $csv    = fopen("$output/$batch.csv", 'r');
    $files  = "$originals/$batch";
    $images = "$output/$batch";
    echo $batch."\n";

    while ($data = fgetcsv($csv)) {
        $file       = "$originals/$batch/$data[0]";
        $permit_num = $data[1];
        $address    = $data[3];
        $permit_id  = $data[4];


        if (!is_dir("$images/$permit_num")) { mkdir("$images/$permit_num", 0775, true); }
        echo "$images/$permit_num\n";

        $manifest = fopen("$images/$permit_num/$permit_num.csv", 'w');
        exec("pdfimages -png \"$file\" $images/$permit_num/$permit_num");
        foreach (glob("$images/$permit_num/*.png") as $i) {
            fputcsv($manifest, [basename($file), basename($i)]);
        }
        fclose($manifest);
    }
}
