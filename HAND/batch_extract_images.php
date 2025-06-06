<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);

if (!defined('SITE_HOME')) { define('SITE_HOME', realpath(__DIR__.'/../../data')); }
$config    = json_decode(file_get_contents(SITE_HOME.'/config.json'), true);
$originals = '/srv/sites/hand/originals';
$output    = '/srv/sites/hand/split';
if (!is_dir($output)) { mkdir($output); }

// Column numbers from batch metadata
define('FILENAME',   0);
define('PERMIT_NUM', 2);
define('ADDRESS',    3);
define('PERMIT_ID',  4);


foreach (glob("$originals/*.csv") as $b) {
    $batch = substr(basename($b), 0, -4);

    $csv    = fopen("$output/$batch.csv", 'r');
    $files  = "$originals/$batch";
    $images = "$output/$batch";
    echo $batch."\n";

    while ($data = fgetcsv($csv)) {
        $file       = "$originals/$batch/".$data[FILENAME].'.pdf';
        $permit_num = $data[PERMIT_NUM];
        $address    = $data[ADDRESS   ];
        $permit_id  = $data[PERMIT_ID ];

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
