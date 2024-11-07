<!DOCTYPE html>
<html lang="en">
<head>
    <?php
   include 'seguridad_login.php'
    ?>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analisis Clinico</title>
    <style>
        body{
            padding: 0;
            margin: 0;
            overflow: hidden;
        }
        div{
            width: 100%;
            height: 100vh;;
        }
        iframe{
            width: 100%;
            height: 100%;
        }
    </style>
</head>
<body>
<?php
$lista = [
  'idArchivo' => 'analisis/%s.pdf',
  'idPare' => 'pare/%s',
  'idLegal' => 'legal/%s',
  'idEmb' => 'emb_pic/%s',
  'idRet' => 'retiro_embrio/%s',
  'idPaci' => 'paci/%s',
  'idStorage' => 'storage/%s',
];

foreach ($lista as $getId => $src) {
  if (isset($_GET[$getId])) {
      $id = $_GET[$getId];
      echo '<div><iframe src="' . sprintf($src, $id) . '" frameborder="0"></iframe></div>';
      break;
  }
}

?>
</body>
</html>