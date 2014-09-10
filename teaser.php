<?php

// No direct access to this file

$raw_data = file_get_contents('C:\xampp\htdocs\tmp/wordlist.txt');

$words = explode("\n", $raw_data);

$deduped = array();

foreach ($words as $key => $word)
{
  
  $valid_word = false;

  if (strpos($word, '\''))
  {
    continue;
  }

  $tmp = preg_match('/[bcdefghjkmqvxz]+/', $word, $matches);

  if (empty($matches))
  {
    $valid_word = true;
  }

  if (!array_key_exists($word, $deduped) && $valid_word)
  {
    $deduped[$word] = $key;
  }
  
}

$deduped = array_flip($deduped);


$collection = array();


$a = array_slice($deduped, 0, 5);


depth_picker($a, "", $collection);

var_dump($collection);


$collection = array();

$b = array_slice($deduped, 6,5);

depth_picker($b, "", $collection);

var_dump($collection);die;

function depth_picker($arr, $temp_string, &$collect) {
    if ($temp_string != "") 
        $collect []= $temp_string;

    for ($i=0; $i<sizeof($arr);$i++) {
      
        $arrcopy = $arr;
        $elem = array_splice($arrcopy, $i, 1); // removes and returns the i'th element
        if (sizeof($arrcopy) > 0) {
            depth_picker($arrcopy, $temp_string ." " . $elem[0], $collect);
        } else {
            $collect []= $temp_string. " " . $elem[0];
        }   
    }   
}