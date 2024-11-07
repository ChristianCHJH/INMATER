<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php
    require($_SERVER["DOCUMENT_ROOT"] . "/_database/database.php");
    require($_SERVER["DOCUMENT_ROOT"] . "/config/environment.php");

    global $db;
    $stmt = $db->prepare("SELECT *
    from hc_paciente hp
    order by createdate desc");
    $stmt->execute();

    print("<p>total de registros: " .  $stmt->rowCount() . "</p>");

    while ($item = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $foto_url = $_SERVER["DOCUMENT_ROOT"] . "/paci/" . $item['dni'] . "/foto.jpg";

        if (file_exists($foto_url)) {
            /* print("<a href='" . "/paci/" . $item['dni'] . "/foto.jpg" . "'>Link Foto</a>");
            print("<pre>"); print_r($item); print("</pre>"); */
            //
            $path = $_SERVER["DOCUMENT_ROOT"] . "/paci/" . $item['dni'] . "/foto.jpg";
            // $type = pathinfo($path, PATHINFO_EXTENSION);
            $data_foto = file_get_contents($path);
            // $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            $info = base64_encode($data_foto);
            //var_dump($info);

            // actualizo segun el valor del dia en que transfirio
            $stmt_upd = $db->prepare("UPDATE hc_paciente set iduserupdate = ?,updatex=?, foto_principal = ? where dni = ?;");
            $hora_actual = date("Y-m-d H:i:s");
            $stmt_upd->execute(['sistemas',$hora_actua, $info, $item["dni"]]);
            $log_Paciente = $db->prepare(
                "INSERT INTO appinmater_log.hc_paciente (
                            dni, pass, sta, med, tip, nom, ape, fnac, tcel,
                            tcas, tofi, mai, dir, nac, depa, prov, dist, prof,
                            san, don, raz, talla, peso, rem, nota, fec, idsedes,
                            idusercreate, createdate, 
                            action
                    )
                SELECT 
                    dni, pass, sta, med, tip, nom, ape, fnac, tcel, 
                    tcas, tofi, mai, dir, nac, depa, prov, dist, prof,
                    san, don, raz, talla, peso, rem, nota, fec, idsedes,
                    iduserupdate,updatex, 'U'
                FROM appinmater_modulo.hc_paciente
                WHERE dni=?");
            $log_Paciente->execute(array($item["dni"]));
        }
    }
    ?>
</body>
</html>