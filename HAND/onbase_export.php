<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);


if (!defined('SITE_HOME')) { define('SITE_HOME', realpath(__DIR__.'/../../data')); }
define('HAND', 109);
define('MOUNT', SITE_HOME.'/cob-obapp1');

$config    = json_decode(file_get_contents(SITE_HOME.'/config.json'), true);
$export    = SITE_HOME.'/export';
$pdo       = getConnection($config['db']['onbase']);

$sql = "select distinct cast(i.itemdate as date) as batch_date
        from hsi.itemdata i
        where i.itemtypegroupnum=?";
$qq  = $pdo->prepare($sql);
$qq->execute([HAND]);
$batches = $qq->fetchAll(\PDO::FETCH_COLUMN);

$sql = "select i.itemnum,
               i.itemname,
               cast(i.itemdate   as date) as itemdate,
               cast(i.datestored as date) as datestored,
               p.filepath,
               a.keyvaluechar as address,
               f.keyvaluechar as file_num,
               o.keyvaluechar as opex_batch_num,
               s.keyvaluechar as set_id,
               b.keyvaluechar as box_id
        from hsi.itemdata     i
        join hsi.itemdatapage p on i.itemnum=p.itemnum
        join hsi.diskgroup    g on p.diskgroupnum=g.diskgroupnum
        left join hsi.keyitem237   a on i.itemnum=a.itemnum
        left join hsi.keyitem242   f on i.itemnum=f.itemnum
        left join hsi.keyitem243   o on i.itemnum=o.itemnum
        left join hsi.keyitem244   s on i.itemnum=s.itemnum
        left join hsi.keyitem245   b on i.itemnum=b.itemnum
        where i.itemtypegroupnum=?
          and cast(i.itemdate as date)=?";
$qq  = $pdo->prepare($sql);
foreach ($batches as $batch) {
    if ( !is_dir("$export/$batch")) { mkdir("$export/$batch", 0775, true); }
    $csv = fopen("$export/$batch.csv", 'w');
    $qq->execute([HAND, $batch]);
    foreach ($qq->fetchAll(\PDO::FETCH_ASSOC) as $row) {
        foreach ($row as $k=>$v) { $row[$k] = $v ? trim($v) : ''; }
        $row['filepath'] = str_replace('\\', '/', $row['filepath']);
        fputcsv($csv, $row);

        $src  = MOUNT."/HAND$row[filepath]";
        $dest = "$export/$batch/$row[itemname].pdf";
        echo "$src\n";
        copy($src, $dest);
    }
}


function getConnection(array $config): \PDO
{
    $pdo = new \PDO($config['dsn'], $config['user'], $config['pass'], $config['opts']);
    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    return $pdo;
}
