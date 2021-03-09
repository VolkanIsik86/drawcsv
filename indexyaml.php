<?php
require_once "vendor/autoload.php";

use Symfony\Component\Yaml\Yaml;

$string = array("url" => "", "format" => "csv");
$string['url'] = "https://raw.githubusercontent.com/VolkanIsik86/drawcsv/master/test.csv";
$base = base64_encode(zlib_encode(urlencode(json_encode($string)), ZLIB_ENCODING_RAW));
$link = "https://app.diagrams.net/?desc=" . $base;
echo $link;
echo "\n";

$filen = fopen("test.csv", "w") or die("unable to open");

$csvheader = file_get_contents("csvheader.csv");
$csvheader .= "\n";

$yamlfile = file_get_contents("process.yaml");
$iso = utf8_encode($yamlfile);

$yaml = Yaml::parse($iso);

$step = $yaml['processtask'];

$symTabel = array();

for ($i = 0; sizeof($step) > $i; $i++) {
    $csvheader .= $step[$i]["stepnr"] . "," . $step[$i]["name"];

    if ($step[$i]["task"] == "process_jumpProcess") {
        jump($i);
    } elseif ($step[$i]["task"] == "process_startProcess") {
        processStart($i);
    } else {
        regularStep($i);
    }

    $csvheader .= "\n";
}
echo $csvheader;

fwrite($filen, $csvheader);
fclose($filen);

shell_exec("git config --global credential.helper 'cache --timeout 28800'");

shell_exec("git commit -a -m 'ny verion'");

echo shell_exec("git push");


function processStart($i)
{
    global $csvheader;
    global $step;
    $color = "#97d077";
    if (isset($step[$i]['disabled'])) {
        if ($step[$i]['disabled'] == 1) {
            $color = "#b6b6b6";
        }
    }

    if ($i != (sizeof($step) - 1)) {
        $csvheader .= ",$color,#000000," . "ellipse," . "," . "\"" . ($i + 2) . "\"" . "," . ",";
    } else {
        $csvheader .= ",$color,#000000," . "ellipse," . "," . "," . ",";
    }

}

function regularStep($i)
{
    global $csvheader;
    global $step;
    $color = "#dae8fc";
    if (isset($step[$i]['disabled'])) {
        if ($step[$i]['disabled'] == 1) {
            $color = "#b6b6b6";
        }
    }
    if ($i != (sizeof($step) - 1))
        $csvheader .= ",$color,#000000," . "rectangle," . "," . "\"" . ($i + 2) . "\"" . "," . ",";
    else
        $csvheader .= ",$color,#000000," . "rectangle," . "," . "," . ",";

}


function jump($i)
{
    global $csvheader;
    global $step;
    $color = "#ffe599";
    if (isset($step[$i]['disabled'])) {
        if ($step[$i]['disabled'] == 1) {
            $color = "#b6b6b6";
        }
    }

    if ($i != (sizeof($step) - 1)) {
        if ($step[$i]['config']['processstepnr'] == -1) {
            if ($step[$i]['config']['branchrulea'] == 1 && $step[$i]['config']['branchoperator'] == "==" && $step[$i]['config']['branchruleb'] == 1) {
                $csvheader .= ",$color,#000000," . "rhombus," . $step[$i]['config']['branchrulea']
                    . $step[$i]['config']['branchoperator']
                    . $step[$i]['config']['branchruleb']
                    . "," . "," . ",";
            } else {
                $csvheader .= ",$color,#000000," . "rhombus," . $step[$i]['config']['branchrulea']
                    . $step[$i]['config']['branchoperator']
                    . $step[$i]['config']['branchruleb']
                    . "," . "," . ","
                    . "\"" . ($i + 2) . "\"";
            }
        } elseif ($step[$i]['config']['branchrulea'] == 1 && $step[$i]['config']['branchoperator'] == "==" && $step[$i]['config']['branchruleb'] == 1) {
            $csvheader .= ",$color,#000000," . "rhombus," . $step[$i]['config']['branchrulea']
                . $step[$i]['config']['branchoperator']
                . $step[$i]['config']['branchruleb']
                . "," . "," . "\""
                . $step[$i]['config']['processstepnr']
                . "\"" . ",";
        } else {
            $csvheader .= ",$color,#000000," . "rhombus," . $step[$i]['config']['branchrulea']
                . $step[$i]['config']['branchoperator']
                . $step[$i]['config']['branchruleb']
                . "," . "," . "\""
                . $step[$i]['config']['processstepnr']
                . "\"" . ","
                . "\"" . ($i + 2) . "\"";
        }
    } else {
        $csvheader .= ",$color,#000000," . "rhombus," . "," . "," . ",";
    }
}
