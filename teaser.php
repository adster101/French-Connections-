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

var_dump($deduped);