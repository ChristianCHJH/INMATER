<?php 
include '../../seguridad_login.php';
function generateSqlWithValues($sql, $values) {
    foreach ($values as &$value) {
        if (is_null($value)) {
            $value = 'NULL';
        } elseif (is_string($value)) {
            $value = "'" . addslashes($value) . "'";
        } elseif (is_bool($value)) {
            $value = $value ? 'TRUE' : 'FALSE';
        } elseif (is_int($value) || is_float($value)) {
            // Numeric values do not need quotes
        } else {
            $value = "'" . addslashes((string) $value) . "'";
        }
    }
    $values = array_map('strval', $values);
    $search = array_fill(0, count($values), '?');
    return str_replace($search, $values, $sql);
}

$response = ['status' => false];

try {
    // Validar que todos los campos obligatorios estén presentes y no estén vacíos
    $required_fields = [
        'dni' => 'Número de documento',
        'tip' => 'Tipo de documento', 
        'don' => 'Tipo de cliente',
        'nom' => 'Nombre',
        'ape' => 'Apellidos',
        'fnac' => 'Fecha de nacimiento',
        'tcel' => 'Celular',
        'mai' => 'Email',
        'rem' => 'Referido',
        'medios_comunicacion_id' => 'Medios de comunicación',
        'm_tratante' => 'Médico tratante',
        'sede' => 'Sede de procedencia'
    ];

    foreach ($required_fields as $field => $field_name) {
        if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
            throw new Exception("$field_name no proporcionado");
        }
    }

    $tip = $_POST['tip'];
    $dni = $_POST['dni'];
    $talla = $_POST['talla'];
    $peso = $_POST['peso'];
    $don = $_POST['don'];
    $nom = $_POST['nom'];
    $ape = $_POST['ape'];
    $fnac = $_POST['fnac'];
    $tcel = $_POST['tcel'];
    $mai = $_POST['mai'];
    $rem = $_POST['rem'];
    $medios = $_POST['medios_comunicacion_id'];
    $m_tratante = $_POST['m_tratante'];
    $sede = $_POST['sede'];

    // Validación de Talla (Debe ser un número positivo si se proporciona)
    if (!empty($talla) && (!is_numeric($talla) || $talla <= 0)) {
        throw new Exception("La talla debe ser un número positivo");
    }

    // Validación de Peso (Debe ser un número positivo si se proporciona)
    if (!empty($peso) && (!is_numeric($peso) || $peso <= 0)) {
        throw new Exception("El peso debe ser un número positivo");
    }

    // Validación de DNI (Ejemplo: longitud exacta de 8 dígitos)
    if ($tip === 'DNI' && !preg_match('/^\d{8}$/', $dni)) {
        throw new Exception('El DNI debe tener 8 dígitos');
    }

    // Validación de Pasaporte (Ejemplo: longitud entre 6 y 9 caracteres alfanuméricos)
    if ($tip === 'PAS' && !preg_match('/^[a-zA-Z0-9]{6,9}$/', $dni)) {
        throw new Exception('El pasaporte debe tener entre 6 y 9 caracteres alfanuméricos');
    }

    // Validación de Carnet de Extranjería (Ejemplo: longitud exacta de 12 dígitos)
    if ($tip === 'CEX' && !preg_match('/^\d{12}$/', $dni)) {
        throw new Exception('El carnet de extranjería debe tener 12 dígitos');
    }

    // Validación de Fecha de Nacimiento (Debe proporcionar una edad positiva)
    $fnac_date = new DateTime($fnac);
    $current_date = new DateTime();
    $age = $current_date->diff($fnac_date)->y;

    if ($age <= 0) {
        throw new Exception("La fecha de nacimiento debe proporcionar una edad válida");
    }

    // Otros campos opcionales
    $tcas = $_POST['tcas'];
    $tofi = $_POST['tofi'];
    $dir = $_POST['dir'];
    $nac = $_POST['nac'];
    $depa = $_POST['depa'];
    $prov = $_POST['prov'];
    $dist = $_POST['dist'];
    $prof = $_POST['prof'];
    $san = $_POST['san'];
    $raz = $_POST['raz'];
    $nota = $_POST['nota'];
    $foto = $_FILES['foto'];
    $asesora = $_POST['asesora'];
    $query_counts = json_decode($_POST['query-counts']);

    if ($_POST['query-counts'] != '' && json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('El contador de registros fue adulterado');
    }
    if (!isset($_SESSION['login'])) {
        throw new Exception('Debes iniciar sesión con tu cuenta nuevamente');
    }
    // Obtener el valor de 'login' del POST
    $login = $_SESSION['login'];

    // Inicializar $sunatapi con valores predeterminados
    $sunatapi = [null, null, null, 0, 0];
    //print_r($query_counts); exit;
    // Verificar si existe el índice en $query_counts
    $formulario = 'nuevo_paciente';

    if (isset($query_counts->$login->$formulario)) {
        $form_data = $query_counts->$login->$formulario;
    
        // Obtener los datos del primer tipo y número disponible
        $tipo = reset($form_data); // Obtiene el primer tipo
        $numero = reset($tipo); // Obtiene el primer número
    
        if (isset($numero->ajax)) {
            $sunatapi[0] = $tipo; // last_doc_type
            $sunatapi[1] = $numero;   // last_doc_number
            $sunatapi[2] = date('Y-m-d H:i:s'); // current datetime
            $sunatapi[3] = $numero->ajax->success ?? 0; // ajax_count 
        }
        if (isset($numero->cache)) {
            $sunatapi[4] = $numero->cache->success ?? 0;   // cache_count
        }
    }

    //print_r($sunatapi);exit;

    $estado_registro = $_POST['query-counts'] == '' ? 'Manual' : 'Registrado';
    // Remover print_r y exit antes de producción
    //print_r($query_counts); exit;

    global $db;
	$base64_foto = "";
    $pass=$dni;
	if (empty($user_id)) {
		$user_id = null;
	}
    if (empty($rem)) {
        $rem = NULL; 
    }
    // verificar el medico tratante
    $stmt = $db->prepare("SELECT id, codigo from man_medico WHERE id=?;");
	$stmt->execute([$m_tratante]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    $med = $data["codigo"];

    $db->beginTransaction();

    // ingresar la foto del paciente
	if (isset($foto) and isset($foto['name']) and !empty($foto['name'])) {
		$nom_destination = 'paci/' . $dni . '/foto.jpg';
		if (is_uploaded_file($foto['tmp_name'])) {
			move_uploaded_file($foto['tmp_name'], $nom_destination);
			$base64_foto = base64_encode(file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/paci/" . $dni . "/foto.jpg"));
		}
	}
	$rPaci = $db->prepare("SELECT dni, nom, ape, med FROM hc_paciente WHERE dni=?;");
	$rPaci->execute([$dni]);
	// verificar si existe
	if ($rPaci->rowCount() < 1) {
        $dni = !empty($dni) ? $dni : '';
        $pass = !empty($pass) ? $pass : '';
        $medios_comunicacion_id = !empty($medios) ? $medios: 0;
        $sta = !empty($sta) ? $sta : '';
        $med = !empty($med) ? $med : '';
        $tip = !empty($tip) ? $tip : '';
        $nom = !empty($nom) ? $nom : '';
        $ape = !empty($ape) ? $ape : '';
        $fnac = !empty($fnac) ? $fnac : '1900-01-01';
        $tcel = !empty($tcel) ? $tcel : '';
        $tcas = !empty($tcas) ? $tcas : '';
        $tofi = !empty($tofi) ? $tofi : '';
        $mai = !empty($mai) ? $mai : '';
        $dir = !empty($dir) ? $dir : '';
        $nac = !empty($nac) ? $nac : '';
        $depa = !empty($depa) ? $depa : '';
        $prov = !empty($prov) ? $prov : '';
        $dist = !empty($dist) ? $dist : '';
        $prof = !empty($prof) ? $prof : '';
        $san = !empty($san) ? $san : '';
        $don = !empty($don) ? $don : '';
        $raz = !empty($raz) ? $raz : '';
        $talla = !empty($talla) ? $talla : '';
        $peso = !empty($peso) ? $peso : '';
        $rem = !empty($rem) ? $rem : '';
        $base64_foto = !empty($base64_foto) ? $base64_foto : '';
        $sede = !empty($sede) ? intval($sede) : 0;
        $user_id = !empty($user_id) ? $user_id : '';
        $medTratante = !empty($medTratante) ? intval($medTratante) : 0;
        $asesora = !empty($asesora) ? intval($asesora) : 0;
        $medio_referencia_id = !empty($medio_referencia_id) ? intval($medio_referencia_id) : 0;
        $nota = !empty($nota) ? intval($nota) : 0;
        
        $sql_hcpaciente = "INSERT INTO hc_paciente
            (dni, pass, medios_comunicacion_id, sta, med, tip, nom, ape, fnac, tcel, tcas, tofi, mai, dir, nac, depa, prov, dist, prof, san, don, raz, talla, peso, rem, foto_principal, idsedes, idusercreate, medico_tratante_id, asesor_medico_id, medio_referencia_id, estado_registro) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $values_hcpaciente = [
            $dni, $pass, $medios_comunicacion_id, $sta, $med, $tip, $nom, $ape, $fnac, $tcel, $tcas, $tofi, $mai, $dir, $nac, $depa, $prov, $dist, $prof, $san, $don, $raz, $talla, $peso, $rem, $base64_foto, $sede, $user_id, $medTratante, $asesora, $nota, $estado_registro
        ];
//         echo  $medios_comunicacion_id; exit;
//         $debug_sql = generateSqlWithValues($sql_hcpaciente, $values_hcpaciente);
// echo $debug_sql; exit;

			$stmt = $db->prepare($sql_hcpaciente);
			$stmt->execute($values_hcpaciente);
			
             
            //print_r($estado_registro); exit;

            $log_Paciente = $db->prepare(
                "INSERT INTO appinmater_log.hc_paciente (
                            dni, pass, sta, med, tip, nom, ape, fnac, tcel,
                            tcas, tofi, mai, dir, nac, depa, prov, dist, prof,
                            san, don, raz, talla, peso, rem, nota, fec, idsedes,
                            idusercreate, createdate, 
                            action,
                            sunat_doc_type,sunat_doc_number,sunat_doc_datetime,sunat_count,sunat_cache_count
                    )
                SELECT 
                    dni, pass, sta, med, tip, nom, ape, fnac, tcel, 
                    tcas, tofi, mai, dir, nac, depa, prov, dist, prof,
                    san, don, raz, talla, peso, rem, nota, fec, idsedes,
                    idusercreate,createdate, 'I',
                    $sunatapi[0],$sunatapi[1],$sunatapi[2],$sunatapi[3],$sunatapi[4]
                FROM appinmater_modulo.hc_paciente
                WHERE dni=?");
            $log_Paciente->execute(array($dni));
            $stmt = $db->prepare("INSERT INTO hc_antece (dni) VALUES (?)");
			$stmt->execute(array($dni));
            if (!file_exists('paci/' . $dni)) {
                mkdir('paci/' . $dni, 0755);
            }
	}

    $db->commit();
    $response['status'] = true;
    $response['message'] = 'Registro exitoso';
} catch (Exception $e) {
    $db->rollBack();
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>
