<?php

function fizz_buz($number)
{
  $hasil = [];

  for ($i = 1; $i <= $number; $i++) {
    // cek berhasil bagi ada berapa
    // syarat bil prima hanya bisa dibagi 1 dan dirinya sendiri
    $berhasil_bagi = 0;
    for ($j = $i; $j > 0; $j--) {
      if ($i % $j == 0) $berhasil_bagi++;
    }

    if ($berhasil_bagi == 2) {
      $hasil[] = 'FizBuzz';
    } elseif ($i % 2 == 0) {
      $hasil[] = 'Buzz';
    } else if ($i % 2 == 1) {
      $hasil[] = 'Fizz';
    }
  }

  for ($i = 1; $i <= $number; $i++) {
    echo $i . ' => ' . $hasil[$i-1] . '<br/>';
  }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>

<body>
  <form action="" method="post">
    <input type="number" name="number" value="<?php if (isset($_POST['number']) && (int)$_POST['number'] > 0) echo (int)$_POST['number']; ?>">
    <button type="submit">check</button>
  </form>

  <div>
    <?php if (isset($_POST['number']) && (int)$_POST['number'] > 0) fizz_buz((int)$_POST['number']); ?>
  </div>
</body>

</html>