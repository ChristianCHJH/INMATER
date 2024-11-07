<?php
class Recibo {
    private $conn;
    private $table_name = "recibos";
 
    #region atributos
    public $id;
    public $tip;
    public $fec;
    public $dni;
    public $nom;
    public $med;
    public $sede;
    public $correo_electronico;
    public $id_tipo_documento_facturacion;
    public $ruc;
    public $raz;
    public $direccionfiscal;
    public $t_ser;
    public $pak;
    public $ser;
    public $mon;
    public $tot;
    public $t1;
    public $m1;
    public $p1;
    public $t2;
    public $m2;
    public $p2;
    public $t3;
    public $m3;
    public $p3;
    public $anu;
    public $veri;
    public $man_ini;
    public $man_fin;
    public $anglo;
    public $user;
    public $comentarios;
    public $estado;
    public $idusercreate;
    public $createdate;
    public $iduserupdate;
    public $update;
    #endregion
 
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    public function setConnection($db) {
        $this->conn = $db;
    }

    // create product
    function create() {
        // query to insert record
        $query = "INSERT INTO " . $this->table_name . " SET id=:id, tip=:tip, fec=:fec, dni=:dni, nom=:nom, med=:med, sede=:sede, correo_electronico=:correo_electronico, id_tipo_documento_facturacion=:id_tipo_documento_facturacion, ruc=:ruc, raz=:raz, direccionfiscal=:direccionfiscal, t_ser=:t_ser, pak=:pak, ser=:ser, mon=:mon, tot=:tot, t1=:t1, m1=:m1, p1=:p1, t2=:t2, m2=:m2, p2=:p2, t3=:t3, m3=:m3, p3=:p3, anu=:anu, veri=:veri, man_ini=:man_ini, man_fin=:man_fin, anglo=:anglo, userx=:user, comentarios=:comentarios, estado=:estado, idusercreate=:idusercreate";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // bind values
        $stmt->bindParam(":id", htmlspecialchars(strip_tags($this->id)));
        $stmt->bindParam(":tip", htmlspecialchars(strip_tags($this->tip)));
        $stmt->bindParam(":fec", htmlspecialchars(strip_tags($this->fec)));
        $stmt->bindParam(":dni", htmlspecialchars(strip_tags($this->dni)));
        $stmt->bindParam(":nom", htmlspecialchars(strip_tags($this->nom)));
        $stmt->bindParam(":med", htmlspecialchars(strip_tags($this->med)));
        $stmt->bindParam(":sede", htmlspecialchars(strip_tags($this->sede)));
        $stmt->bindParam(":correo_electronico", htmlspecialchars(strip_tags($this->correo_electronico)));
        $stmt->bindParam(":id_tipo_documento_facturacion", htmlspecialchars(strip_tags($this->id_tipo_documento_facturacion)));
        $stmt->bindParam(":ruc", htmlspecialchars(strip_tags($this->ruc)));
        $stmt->bindParam(":raz", htmlspecialchars(strip_tags($this->raz)));
        $stmt->bindParam(":direccionfiscal", htmlspecialchars(strip_tags($this->direccionfiscal)));
        $stmt->bindParam(":t_ser", htmlspecialchars(strip_tags($this->t_ser)));
        $stmt->bindParam(":pak", htmlspecialchars(strip_tags($this->pak)));
        $stmt->bindParam(":ser", htmlspecialchars(strip_tags($this->ser)));
        $stmt->bindParam(":mon", htmlspecialchars(strip_tags($this->mon)));
        $stmt->bindParam(":tot", htmlspecialchars(strip_tags($this->tot)));
        $stmt->bindParam(":t1", htmlspecialchars(strip_tags($this->t1)));
        $stmt->bindParam(":m1", htmlspecialchars(strip_tags($this->m1)));
        $stmt->bindParam(":p1", htmlspecialchars(strip_tags($this->p1)));
        $stmt->bindParam(":t2", htmlspecialchars(strip_tags($this->t2)));
        $stmt->bindParam(":m2", htmlspecialchars(strip_tags($this->m2)));
        $stmt->bindParam(":p2", htmlspecialchars(strip_tags($this->p2)));
        $stmt->bindParam(":t3", htmlspecialchars(strip_tags($this->t3)));
        $stmt->bindParam(":m3", htmlspecialchars(strip_tags($this->m3)));
        $stmt->bindParam(":p3", htmlspecialchars(strip_tags($this->p3)));
        $stmt->bindParam(":anu", htmlspecialchars(strip_tags($this->anu)));
        $stmt->bindParam(":veri", htmlspecialchars(strip_tags($this->veri)));
        $stmt->bindParam(":man_ini", htmlspecialchars(strip_tags($this->man_ini)));
        $stmt->bindParam(":man_fin", htmlspecialchars(strip_tags($this->man_fin)));
        $stmt->bindParam(":anglo", htmlspecialchars(strip_tags($this->anglo)));
        $stmt->bindParam(":user", htmlspecialchars(strip_tags($this->user)));
        $stmt->bindParam(":comentarios", htmlspecialchars(strip_tags($this->comentarios)));
        $stmt->bindParam(":estado", htmlspecialchars(strip_tags($this->estado)));
        $stmt->bindParam(":idusercreate", htmlspecialchars(strip_tags($this->idusercreate)));

        // execute query
        if($stmt->execute()){
            return true;
        }

        return false;
    }
}