<?php
class LabAspiraT {
    private $conn;
    private $table_name = "lab_aspira_t";
 
    #region atributos
    public $pro;
    public $dia;
    public $beta;
    public $beta_rinicial;
    public $beta_evolucion;
    public $beta_sembarazo;
    public $nsacos;
    public $sembarazo_semanas;
    public $sembarazo_nnacidos;
    public $sembarazo_peso;
    public $t_cat;
    public $s_gui;
    public $s_cat;
    public $endo;
    public $inte;
    public $eco;
    public $med;
    public $emb;
    public $obs;
    #endregion
 
    public function __construct($db){
        $this->conn = $db;
    }

    public function setConnection($db) {
        $this->conn = $db;
    }

    function create() {
        // query to insert record
        $query = "INSERT INTO " . $this->table_name . " SET pro=:pro, dia=:dia, beta=:beta, beta_rinicial=:beta_rinicial, beta_evolucion=:beta_evolucion, beta_sembarazo=:beta_sembarazo, nsacos=:nsacos, sembarazo_semanas=:sembarazo_semanas, sembarazo_nnacidos=:sembarazo_nnacidos, sembarazo_peso=:sembarazo_peso, t_cat=:t_cat, s_gui=:s_gui, s_cat=:s_cat, endo=:endo, inte=:inte, eco=:eco, med=:med, emb=:emb, obs=:obs";

        // prepare query
        $stmt = $this->conn->prepare($query);

        #region bind values
        $stmt->bindParam(":pro", htmlspecialchars(strip_tags($this->pro)));
        $stmt->bindParam(":dia", htmlspecialchars(strip_tags($this->dia)));
        $stmt->bindParam(":beta", htmlspecialchars(strip_tags($this->beta)));
        $stmt->bindParam(":beta_rinicial", htmlspecialchars(strip_tags($this->beta_rinicial)));
        $stmt->bindParam(":beta_evolucion", htmlspecialchars(strip_tags($this->beta_evolucion)));
        $stmt->bindParam(":beta_sembarazo", htmlspecialchars(strip_tags($this->beta_sembarazo)));
        $stmt->bindParam(":nsacos", htmlspecialchars(strip_tags($this->nsacos)));
        $stmt->bindParam(":sembarazo_semanas", htmlspecialchars(strip_tags($this->sembarazo_semanas)));
        $stmt->bindParam(":sembarazo_nnacidos", htmlspecialchars(strip_tags($this->sembarazo_nnacidos)));
        $stmt->bindParam(":sembarazo_peso", htmlspecialchars(strip_tags($this->sembarazo_peso)));
        $stmt->bindParam(":t_cat", htmlspecialchars(strip_tags($this->t_cat)));
        $stmt->bindParam(":s_gui", htmlspecialchars(strip_tags($this->s_gui)));
        $stmt->bindParam(":s_cat", htmlspecialchars(strip_tags($this->s_cat)));
        $stmt->bindParam(":endo", htmlspecialchars(strip_tags($this->endo)));
        $stmt->bindParam(":inte", htmlspecialchars(strip_tags($this->inte)));
        $stmt->bindParam(":eco", htmlspecialchars(strip_tags($this->eco)));
        $stmt->bindParam(":med", htmlspecialchars(strip_tags($this->med)));
        $stmt->bindParam(":emb", htmlspecialchars(strip_tags($this->emb)));
        $stmt->bindParam(":obs", htmlspecialchars(strip_tags($this->obs)));
        #endregion

        // execute query
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
}