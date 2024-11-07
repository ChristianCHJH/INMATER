<?php
class RiesgoQuirurgico {
    private $conn;
    private $table_name = "hc_riesgo_quirurgico";
 
    #region atributos
    public $id;
    public $tipodocumento;
    public $numerodocumento;
    public $nivel;
    public $fvigencia;
    public $nombre;
    public $obs;
    public $estado;
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
        $query = "INSERT INTO " . $this->table_name . " SET tipodocumento=:tipodocumento, numerodocumento=:numerodocumento, nivel=:nivel, fvigencia=:fvigencia, nombre=:nombre, obs=:obs, estado=:estado, idusercreate=:idusercreate";

        // prepare query
        $stmt = $this->conn->prepare($query);

        #region bind values
        $stmt->bindParam(":tipodocumento", htmlspecialchars(strip_tags($this->tipodocumento)));
        $stmt->bindParam(":numerodocumento", htmlspecialchars(strip_tags($this->numerodocumento)));
        $stmt->bindParam(":nivel", htmlspecialchars(strip_tags($this->nivel)));
        $stmt->bindParam(":fvigencia", htmlspecialchars(strip_tags($this->fvigencia)));
        $stmt->bindParam(":nombre", htmlspecialchars(strip_tags($this->nombre)));
        $stmt->bindParam(":obs", htmlspecialchars(strip_tags($this->obs)));
        $stmt->bindParam(":estado", htmlspecialchars(strip_tags($this->estado)));
        $stmt->bindParam(":idusercreate", htmlspecialchars(strip_tags($this->idusercreate)));
        #endregion

        // execute query
        if($stmt->execute()){
            return true;
        }

        return false;
    }
}