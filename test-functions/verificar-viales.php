<!DOCTYPE html>
<html lang="es">
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
    $stmt = $db->prepare("SELECT
        t.n_tan t, r.c, r.v, r.p, r.sta, r.tip_id
        from lab_tanque_res r
        inner join lab_tanque t on t.tan = r.t
        where r.sta <> ''
        order by r.t, r.c, r.v, r.p;");
    $stmt->execute();

    print("<p>total de registros: " .  $stmt->rowCount() . "</p>");
    
    while ($item = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $stmt_2 = $db->prepare("SELECT * from lab_andro_cap where des_dni=? and des_tip=? and des_fec=? and eliminado is false;");
        $stmt_2->execute([$item["sta"], 2, $item["tip_id"]]);
        
        while ($item_2 = $stmt_2->fetch(PDO::FETCH_ASSOC)) {
            $des = explode("|", $item_2["des"]);
            foreach ($des as $key => $value) {
                if ($value == $item["T"]."-".$item["C"]."-".$item["V"]."-".$item["P"]) {
                    // print("<pre>"); print_r($value); print("</pre>");
                    // print("<pre>"); print_r($item); print("</pre>");
                    print("<p>".$value."<br>".$item["sta"]."<br>".$item["tip_id"]."</p>");
                }
            }
        }
    }
?>
</body>
</html>