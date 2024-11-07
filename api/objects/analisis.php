<?php
class Analisis {
    private $conn;
    private $table_name = "hc_analisis";
 
    #region atributos
    public $id;
    public $a_dni;
    public $a_mue;
    public $a_nom;
    public $a_med;
    public $a_exa;
    public $a_sta;
    public $a_obs;
    public $cor;
    public $lab;
    public $a_fec;
    public $idusercreate;
    public $iduserupdate;
    #endregion
 
    public function __construct($db){
        $this->conn = $db;
    }

    public function setConnection($db) {
        $this->conn = $db;
    }

    function create() {
        // query to insert record
        $query = "INSERT INTO " . $this->table_name . " SET a_dni=:a_dni, a_mue=:a_mue, a_nom=:a_nom, a_med=:a_med, a_exa=:a_exa, a_sta=:a_sta, a_obs=:a_obs, cor=:cor, lab=:lab, idusercreate=:idusercreate, iduserupdate=:iduserupdate";

        // prepare query
        $stmt = $this->conn->prepare($query);

        #region bind values
        $stmt->bindParam(":a_dni", htmlspecialchars(strip_tags($this->a_dni)));
        $stmt->bindParam(":a_mue", htmlspecialchars(strip_tags($this->a_mue)));
        $stmt->bindParam(":a_nom", htmlspecialchars(strip_tags($this->a_nom)));
        $stmt->bindParam(":a_med", htmlspecialchars(strip_tags($this->a_med)));
        $stmt->bindParam(":a_exa", htmlspecialchars(strip_tags($this->a_exa)));
        $stmt->bindParam(":a_sta", htmlspecialchars(strip_tags($this->a_sta)));
        $stmt->bindParam(":a_obs", htmlspecialchars(strip_tags($this->a_obs)));
        $stmt->bindParam(":cor", htmlspecialchars(strip_tags($this->cor)));
        $stmt->bindParam(":lab", htmlspecialchars(strip_tags($this->lab)));
        $stmt->bindParam(":idusercreate", htmlspecialchars(strip_tags($this->idusercreate)));
        $stmt->bindParam(":iduserupdate", htmlspecialchars(strip_tags($this->iduserupdate)));
        #endregion

        // execute query
        if($stmt->execute()){
            return true;
        }

        return false;
    }
}