<?php

function fizz_buz($arr) {
  $hasil = [];

  foreach ($arr as $number) {
    $berhasil_bagi = 0;
    for ($i = $number; $i > 0 ; $i--) {
      // berhasil dibagi
      $tmp = $number % $i == 0;
      if ($tmp) $berhasil_bagi++;
    }

    echo $berhasil_bagi . '<br/>';

    if ($berhasil_bagi == 2) {
      $hasil[] = 'FizBuzz';
    } elseif ($number % 2 == 0) {
      $hasil[] = 'Buzz';
    } else if ($number % 2 == 1) {
      $hasil[] = 'Fizz';
    }
  }

  foreach ($hasil as $i => $num) {
    echo $arr[$i] . ': ' . $num . '<br/>';
  }
}

fizz_buz([1,2,3,4,5,6,7,8,9,10]);

