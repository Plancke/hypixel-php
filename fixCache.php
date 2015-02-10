<?php
/*
 * This fixes the old cache which stored data by playername
 * converts all files to their uuid
 */
function getAllFiles($dir)
{
    $array = array();
    $ffs = scandir($dir);
    foreach ($ffs as $ff) {
        if ($ff != '.' && $ff != '..') {
            if (is_dir($dir . '/' . $ff)) {
                foreach (getAllFiles($dir . '/' . $ff) as $filename) {
                    array_push($array, $filename);
                }
            } else {
                array_push($array, $dir . '/' . $ff);
            }
        }
    }
    return $array;
}

$filenames = getAllFiles($HypixelPHP->getOptions()['cache_folder_player'] . '/name/');
echo '<b>Found Files:</b>' . sizeof($filenames);
echo '<ol>';
$i = 0;
foreach ($filenames as $filename) {
    $info = json_decode(file_get_contents($filename), true);
    if ($info != null) {
        if (array_key_exists("record", $info)) {
            if (array_key_exists('uuid', $info['record'])) {
                $uuidFile = $HypixelPHP->getOptions()['cache_folder_player'] . '/uuid/' . $HypixelPHP->getCacheFileName($info['record']['uuid']);
                $i++;
                if (!file_exists($uuidFile)) {
                    $HypixelPHP->setFileContent($uuidFile, json_encode($info));
                }
            }
        }
    }
}
echo $i;
echo '</ol>';