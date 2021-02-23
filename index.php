<?php

$string = array("url"=>"","format"=>"csv");
$string['url']= "https://raw.githubusercontent.com/VolkanIsik86/drawcsv/master/test.csv";
$base = base64_encode(zlib_encode(urlencode(json_encode($string)),ZLIB_ENCODING_RAW));
$link = "https://app.diagrams.net/?desc=".$base;
echo $link;
echo "\n";

$filen=fopen("test.csv","w") or die("unable to open");

$csvheader= file_get_contents("csvheader.csv");
$csvheader.="\n";
$file = file_get_contents("process.xml");

$xml = simplexml_load_string($file);

$step = $xml->processTask;




$symTabel = array();

for($i=0 ; sizeof($step) > $i; $i++){
    $csvheader.= $step[$i]["stepnr"] .",".$step[$i]["name"] ;

    if ($step[$i]["task"]=="process_jumpProcess"){
        jump($i);
    }

    elseif ($step[$i]["task"]=="process_startProcess"){
        if($i!=(sizeof($step)-1))
        $csvheader.=",#d6b656,#000000," . "ellipse," . "," . "\"". ($i+2) . "\"" ."," .",";
        else
            $csvheader.=",#d6b656,#000000," . "ellipse," . "," ."," . ",";
    }else{
        if($i!=(sizeof($step)-1))
        $csvheader.=",#d5e8d4,#000000," . "rectangle," . "," . "\"". ($i+2) . "\"" . "," . ",";
        else
            $csvheader.=",#d5e8d4,#000000," . "rectangle," . "," ."," . ",";
    }

    $csvheader.="\n";
}
echo $csvheader;

fwrite($filen,$csvheader);
fclose($filen);

shell_exec("git commit -a -m 'ny verion'");

echo shell_exec("git push");

//echo($step[2]->config->branchrulea);

//print_r($xml);

 function jump($i){
     global $csvheader;
     global $step;
     if($i!=(sizeof($step)-1)) {
         if ($step[$i]->config->processstepnr == -1) {
             $csvheader .= ",#b85450,#000000," . "rhombus," . $step[$i]->config->branchrulea
                 . $step[$i]->config->branchoperator
                 . $step[$i]->config->branchruleb
                 . "," . "," . ","
                 ;
         }elseif ($step[$i]->config->branchrulea==1 && $step[$i]->config->branchoperator=="==" && $step[$i]->config->branchruleb==1){
             $csvheader .= ",#b85450,#000000," . "rhombus," . $step[$i]->config->branchrulea
                 . $step[$i]->config->branchoperator
                 . $step[$i]->config->branchruleb
                 . "," . "," . "\""
                 . $step[$i]->config->processstepnr
                 . "\"" . ","
                 ;
         }
         else {
             $csvheader .= ",#b85450,#000000," . "rhombus," . $step[$i]->config->branchrulea
                 . $step[$i]->config->branchoperator
                 . $step[$i]->config->branchruleb
                 . "," . "," . "\""
                 . $step[$i]->config->processstepnr
                 . "\"" . ","
                 . "\"" . ($i + 2) . "\"";
         }
     }else {
         $csvheader .= ",#b85450,#000000," . "rhombus," . "," . "," . ",";
     }

 }
