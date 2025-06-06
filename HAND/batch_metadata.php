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

$pdo   = getConnection($config['db']['epl']);
$sql   = "select p.PMPERMITID,
                 p.PERMITNUMBER,
                 a.ADDRESSLINE1
          from pmpermit p
          join PMPERMITADDRESS pa on pa.PMPERMITID=p.PMPERMITID and pa.MAIN=1
          join MAILINGADDRESS   a on pa.MAILINGADDRESSID=a.MAILINGADDRESSID
          where p.permitnumber=?";
$query = $pdo->prepare($sql);

// Column numbers from the original metadata
define('ITEMNUM',    0);
define('ITEMNAME',   1);
define('ITEMDATE',   2);
define('DATESTORED', 3);
define('FILEPATH',   4);
define('ADDRESS',    5);
define('FILE_NUM',   6);
define('OPEX_BATCH', 7);
define('SET',        8);
define('BOX',        9);

$DOCPOP = 'https://documents.bloomington.in.gov/AppNetWeb/docpop/docpop.aspx?docid=';

foreach (glob("$originals/*.csv") as $b) {
    $batch = substr(basename($b), 0, -4);

    $files  = "$originals/$batch";
    $incsv  = fopen($b, 'r');
    $outcsv = fopen("$output/$batch.csv", 'w');
    $errors = fopen("$output/$batch-errors.csv", 'w');
    $images = "$output/$batch";

    while ($onbase = fgetcsv($incsv)) {
        $address    = $onbase[ADDRESS];
        $permit_num = is_numeric($onbase[FILE_NUM]) ? 'rentpro_'.$onbase[FILE_NUM] : 'RENT'.$onbase[FILE_NUM];
        echo "$batch/$address $permit_num\n";

        $data = [$onbase[ITEMNAME], $address, $permit_num];
        if ($permit_num) {
            $query->execute([$permit_num]);
            $res = $query->fetchAll(\PDO::FETCH_ASSOC);
            if ($res) {
                array_push($data, $res[0]['ADDRESSLINE1']);
                array_push($data, $res[0]['PMPERMITID']);

                fputcsv($outcsv, $data);
                continue;
            }
        }
        // Add the OnBase URL to this document to the error report
        array_push($data, $DOCPOP.$onbase[ITEMNUM]);
        fputcsv($errors, $data);
    }

    fclose($outcsv);
    fclose($errors);
}

function getConnection(array $config): \PDO
{
    $pdo = new \PDO($config['dsn'], $config['user'], $config['pass'], $config['opts']);
    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    return $pdo;
}
