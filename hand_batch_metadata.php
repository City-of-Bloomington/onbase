<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);

if (!defined('SITE_HOME')) { define('SITE_HOME', realpath(__DIR__.'/../data')); }
$config = json_decode(file_get_contents(SITE_HOME.'/config.json'), true);
$pdo    = getConnection($config['db']['epl']);

$sql    = "select p.PMPERMITID,
                  p.PERMITNUMBER,
                  a.ADDRESSLINE1
           from pmpermit p
           join PMPERMITADDRESS pa on pa.PMPERMITID=p.PMPERMITID and pa.MAIN=1
           join MAILINGADDRESS   a on pa.MAILINGADDRESSID=a.MAILINGADDRESSID
           where p.permitnumber=?";
$query  = $pdo->prepare($sql);

$batches= ['2024-08-05', '2023-06-30'];
foreach ($batches as $batch) {
    $files = "../$batch";
    $csv   = fopen(SITE_HOME."/$batch.csv", 'w');

    $images = SITE_HOME."/$batch";
    if (!is_dir($images)) { mkdir($images); }

    foreach (glob("$files/*.pdf") as $f) {
        $d          = explode('_', basename($f));
        $address    = str_replace('.', '', $d[1]);
        $permit_num = is_numeric($d[2]) ? "rentpro_$d[2]" : "RENT$d[2]";
        echo "$batch/$address $permit_num\n";

        $data = [$f, $permit_num, $address];

        if ($permit_num) {
            $query->execute([$permit_num]);
            $res = $query->fetchAll(\PDO::FETCH_ASSOC);
            if ($res) {
                array_push($data, $res[0]['ADDRESSLINE1']);
                array_push($data, $res[0]['PMPERMITID']);
                if (!is_dir("$images/$permit_num")) { mkdir("$images/$permit_num"); }

                $manifest = fopen("$images/$permit_num/$permit_num.csv", 'w');
                exec("pdfimages -png \"$f\" $images/$permit_num/$permit_num");
                foreach (glob("$images/$permit_num/*.png") as $i) {
                    fputcsv($manifest, [basename($f), basename($i)]);
                }
                fclose($manifest);
            }
        }
        fputcsv($csv, $data);
    }
    fclose($csv);
}

function getConnection(array $config): \PDO
{
    $pdo = new \PDO($config['dsn'], $config['user'], $config['pass'], $config['opts']);
    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    return $pdo;
}
