<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
include_once('../config/database.php');
include_once('../objects/antecedentes.php');

$data = json_decode(file_get_contents("php://input"));

$database = new Database();
$database->setConnection();
$antecedentes = new Antecedentes($database->getConnection());

// enviar servidor local
if(!empty($data->dni)) {
    $antecedentes->dni = $data->dni;
    $antecedentes->f_dia = $data->f_dia;
    $antecedentes->f_tbc = $data->f_tbc;
    $antecedentes->f_hip = $data->f_hip;
    $antecedentes->f_gem = $data->f_gem;
    $antecedentes->f_hta = $data->f_hta;
    $antecedentes->f_can = $data->f_can;
    $antecedentes->f_otr = $data->f_otr;
    $antecedentes->m_dia = $data->m_dia;
    $antecedentes->m_tbc = $data->m_tbc;
    $antecedentes->m_ets = $data->m_ets;
    $antecedentes->m_hip = $data->m_hip;
    $antecedentes->m_inf = $data->m_inf;
    $antecedentes->m_ale = $data->m_ale;
    $antecedentes->m_ale1 = $data->m_ale1;
    $antecedentes->m_can = $data->m_can;
    $antecedentes->m_otr = $data->m_otr;
    $antecedentes->h_str = $data->h_str;
    $antecedentes->h_dep = $data->h_dep;
    $antecedentes->h_dro = $data->h_dro;
    $antecedentes->h_tab = $data->h_tab;
    $antecedentes->h_alc = $data->h_alc;
    $antecedentes->h_otr = $data->h_otr;
    $antecedentes->g_men = $data->g_men;
    $antecedentes->g_per = $data->g_per;
    $antecedentes->g_dur = $data->g_dur;
    $antecedentes->g_vol = $data->g_vol;
    $antecedentes->g_fur = $data->g_fur;
    $antecedentes->g_ant = $data->g_ant;
    $antecedentes->g_pap = $data->g_pap;
    $antecedentes->g_pap1 = $data->g_pap1;
    $antecedentes->g_pap2 = $data->g_pap2;
    $antecedentes->g_dis = $data->g_dis;
    $antecedentes->g_ges = $data->g_ges;
    $antecedentes->g_abo = $data->g_abo;
    $antecedentes->g_abo1 = $data->g_abo1;
    $antecedentes->g_abo_ges = $data->g_abo_ges;
    $antecedentes->g_abo_com = $data->g_abo_com;
    $antecedentes->g_pt = $data->g_pt;
    $antecedentes->g_pp = $data->g_pp;
    $antecedentes->g_vag = $data->g_vag;
    $antecedentes->g_ces = $data->g_ces;
    $antecedentes->g_nv = $data->g_nv;
    $antecedentes->g_nm = $data->g_nm;
    $antecedentes->g_neo = $data->g_neo;
    $antecedentes->g_viv = $data->g_viv;
    $antecedentes->g_fup = $data->g_fup;
    $antecedentes->g_rn_men = $data->g_rn_men;
    $antecedentes->g_rn_mul = $data->g_rn_mul;
    $antecedentes->g_rn_may = $data->g_rn_may;
    $antecedentes->g_agh = $data->g_agh;
    $antecedentes->g_his = $data->g_his;
    $antecedentes->g_obs = $data->g_obs;
    $antecedentes->fe_exa = $data->fe_exa;
    $antecedentes->estado = $data->estado;
    $antecedentes->idusercreate = $data->idusercreate;
 
    /* http_response_code(503); // set response code - 201 created
    echo json_encode(array("message" => $antecedentes->create())); // tell the user    */ 

    if ($antecedentes->create()) {
        http_response_code(201); // set response code - 201 created
        echo json_encode(array("message" => "antecedentes was created.")); // tell the user
    } else { // if unable to create the antecedentes, tell the user
        http_response_code(503); // set response code - 503 service unavailable
        echo json_encode(array("message" => "Unable to create antecedentes.")); // tell the user
    }
} else { // tell the user data is incomplete
    http_response_code(400); // set response code - 400 bad request
    echo json_encode(array("message" => "Unable to create antecedentes. Data is incomplete.")); // tell the user
}
?>