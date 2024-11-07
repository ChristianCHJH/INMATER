<?php
class Antecedentes {
    private $conn;
    private $table_name = "hc_antece";
 
    #region atributos
    public $id;
    public $dni;
    public $f_dia;
    public $f_tbc;
    public $f_hip;
    public $f_gem;
    public $f_hta;
    public $f_can;
    public $f_otr;
    public $m_dia;
    public $m_tbc;
    public $m_ets;
    public $m_hip;
    public $m_inf;
    public $m_ale;
    public $m_ale1;
    public $m_can;
    public $m_otr;
    public $h_str;
    public $h_dep;
    public $h_dro;
    public $h_tab;
    public $h_alc;
    public $h_otr;
    public $g_men;
    public $g_per;
    public $g_dur;
    public $g_vol;
    public $g_fur;
    public $g_ant;
    public $g_pap;
    public $g_pap1;
    public $g_pap2;
    public $g_dis;
    public $g_ges;
    public $g_abo;
    public $g_abo1;
    public $g_abo_ges;
    public $g_abo_com;
    public $g_pt;
    public $g_pp;
    public $g_vag;
    public $g_ces;
    public $g_nv;
    public $g_nm;
    public $g_neo;
    public $g_viv;
    public $g_fup;
    public $g_rn_men;
    public $g_rn_mul;
    public $g_rn_may;
    public $g_agh;
    public $g_his;
    public $g_obs;
    public $fe_exa;
    public $estado;
    public $idusercreate;
    public $iduserupdate;
    #endregion
 
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    public function setConnection($db) {
        $this->conn = $db;
    }

    function create() {
        $query = "INSERT INTO " . $this->table_name . " SET dni=:dni, f_dia=:f_dia, f_tbc=:f_tbc, f_hip=:f_hip, f_gem=:f_gem, f_hta=:f_hta, f_can=:f_can, f_otr=:f_otr, m_dia=:m_dia, m_tbc=:m_tbc, m_ets=:m_ets, m_hip=:m_hip, m_inf=:m_inf, m_ale=:m_ale, m_ale1=:m_ale1, m_can=:m_can, m_otr=:m_otr, h_str=:h_str, h_dep=:h_dep, h_dro=:h_dro, h_tab=:h_tab, h_alc=:h_alc, h_otr=:h_otr, g_men=:g_men, g_per=:g_per, g_dur=:g_dur, g_vol=:g_vol, g_fur=:g_fur, g_ant=:g_ant, g_pap=:g_pap, g_pap1=:g_pap1, g_pap2=:g_pap2, g_dis=:g_dis, g_ges=:g_ges, g_abo=:g_abo, g_abo1=:g_abo1, g_abo_ges=:g_abo_ges, g_abo_com=:g_abo_com, g_pt=:g_pt, g_pp=:g_pp, g_vag=:g_vag, g_ces=:g_ces, g_nv=:g_nv, g_nm=:g_nm, g_neo=:g_neo, g_viv=:g_viv, g_fup=:g_fup, g_rn_men=:g_rn_men, g_rn_mul=:g_rn_mul, g_rn_may=:g_rn_may, g_agh=:g_agh, g_his=:g_his, g_obs=:g_obs, fe_exa=:fe_exa, estado=:estado, idusercreate=:idusercreate";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":dni", htmlspecialchars(strip_tags($this->dni)));
        $stmt->bindParam(":f_dia", htmlspecialchars(strip_tags($this->f_dia)));
        $stmt->bindParam(":f_tbc", htmlspecialchars(strip_tags($this->f_tbc)));
        $stmt->bindParam(":f_hip", htmlspecialchars(strip_tags($this->f_hip)));
        $stmt->bindParam(":f_gem", htmlspecialchars(strip_tags($this->f_gem)));
        $stmt->bindParam(":f_hta", htmlspecialchars(strip_tags($this->f_hta)));
        $stmt->bindParam(":f_can", htmlspecialchars(strip_tags($this->f_can)));
        $stmt->bindParam(":f_otr", htmlspecialchars(strip_tags($this->f_otr)));
        $stmt->bindParam(":m_dia", htmlspecialchars(strip_tags($this->m_dia)));
        $stmt->bindParam(":m_tbc", htmlspecialchars(strip_tags($this->m_tbc)));
        $stmt->bindParam(":m_ets", htmlspecialchars(strip_tags($this->m_ets)));
        $stmt->bindParam(":m_hip", htmlspecialchars(strip_tags($this->m_hip)));
        $stmt->bindParam(":m_inf", htmlspecialchars(strip_tags($this->m_inf)));
        $stmt->bindParam(":m_ale", htmlspecialchars(strip_tags($this->m_ale)));
        $stmt->bindParam(":m_ale1", htmlspecialchars(strip_tags($this->m_ale1)));
        $stmt->bindParam(":m_can", htmlspecialchars(strip_tags($this->m_can)));
        $stmt->bindParam(":m_otr", htmlspecialchars(strip_tags($this->m_otr)));
        $stmt->bindParam(":h_str", htmlspecialchars(strip_tags($this->h_str)));
        $stmt->bindParam(":h_dep", htmlspecialchars(strip_tags($this->h_dep)));
        $stmt->bindParam(":h_dro", htmlspecialchars(strip_tags($this->h_dro)));
        $stmt->bindParam(":h_tab", htmlspecialchars(strip_tags($this->h_tab)));
        $stmt->bindParam(":h_alc", htmlspecialchars(strip_tags($this->h_alc)));
        $stmt->bindParam(":h_otr", htmlspecialchars(strip_tags($this->h_otr)));
        $stmt->bindParam(":g_men", htmlspecialchars(strip_tags($this->g_men)));
        $stmt->bindParam(":g_per", htmlspecialchars(strip_tags($this->g_per)));
        $stmt->bindParam(":g_dur", htmlspecialchars(strip_tags($this->g_dur)));
        $stmt->bindParam(":g_vol", htmlspecialchars(strip_tags($this->g_vol)));
        $stmt->bindParam(":g_fur", htmlspecialchars(strip_tags($this->g_fur)));
        $stmt->bindParam(":g_ant", htmlspecialchars(strip_tags($this->g_ant)));
        $stmt->bindParam(":g_pap", htmlspecialchars(strip_tags($this->g_pap)));
        $stmt->bindParam(":g_pap1", htmlspecialchars(strip_tags($this->g_pap1)));
        $stmt->bindParam(":g_pap2", htmlspecialchars(strip_tags($this->g_pap2)));
        $stmt->bindParam(":g_dis", htmlspecialchars(strip_tags($this->g_dis)));
        $stmt->bindParam(":g_ges", htmlspecialchars(strip_tags($this->g_ges)));
        $stmt->bindParam(":g_abo", htmlspecialchars(strip_tags($this->g_abo)));
        $stmt->bindParam(":g_abo1", htmlspecialchars(strip_tags($this->g_abo1)));
        $stmt->bindParam(":g_abo_ges", htmlspecialchars(strip_tags($this->g_abo_ges)));
        $stmt->bindParam(":g_abo_com", htmlspecialchars(strip_tags($this->g_abo_com)));
        $stmt->bindParam(":g_pt", htmlspecialchars(strip_tags($this->g_pt)));
        $stmt->bindParam(":g_pp", htmlspecialchars(strip_tags($this->g_pp)));
        $stmt->bindParam(":g_vag", htmlspecialchars(strip_tags($this->g_vag)));
        $stmt->bindParam(":g_ces", htmlspecialchars(strip_tags($this->g_ces)));
        $stmt->bindParam(":g_nv", htmlspecialchars(strip_tags($this->g_nv)));
        $stmt->bindParam(":g_nm", htmlspecialchars(strip_tags($this->g_nm)));
        $stmt->bindParam(":g_neo", htmlspecialchars(strip_tags($this->g_neo)));
        $stmt->bindParam(":g_viv", htmlspecialchars(strip_tags($this->g_viv)));
        $stmt->bindParam(":g_fup", htmlspecialchars(strip_tags($this->g_fup)));
        $stmt->bindParam(":g_rn_men", htmlspecialchars(strip_tags($this->g_rn_men)));
        $stmt->bindParam(":g_rn_mul", htmlspecialchars(strip_tags($this->g_rn_mul)));
        $stmt->bindParam(":g_rn_may", htmlspecialchars(strip_tags($this->g_rn_may)));
        $stmt->bindParam(":g_agh", htmlspecialchars(strip_tags($this->g_agh)));
        $stmt->bindParam(":g_his", htmlspecialchars(strip_tags($this->g_his)));
        $stmt->bindParam(":g_obs", htmlspecialchars(strip_tags($this->g_obs)));
        $stmt->bindParam(":fe_exa", htmlspecialchars(strip_tags($this->fe_exa)));
        $stmt->bindParam(":estado", htmlspecialchars(strip_tags($this->estado)));
        $stmt->bindParam(":idusercreate", htmlspecialchars(strip_tags($this->idusercreate)));
        // execute query
        if($stmt->execute()){
            return true;
        }

        // return $stmt->error;
        return false;
    }
}