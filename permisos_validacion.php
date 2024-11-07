<?php
        function validacion_permiso($permiso,$login){
            global $db;
            $permisos = $db->prepare("SELECT mau.* FROM appinmater_modulo.permisos_autorizacion_usuario pau
            inner join appinmater_modulo.man_autorizacion mau on mau.id = pau.id_autorizacion
            inner join appinmater_modulo.usuario usu on usu.id = pau.id_user
            where mau.id = $permiso and usu.userx = ?;");
            $permisos->execute(array($login));
            return $autori = $permisos->fetch(PDO::FETCH_ASSOC);
        }