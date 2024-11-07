<?php
class Paciente {
    private $conn;
    private $table_name = "hc_paciente";
 
    #region atributos
    public $id;
    public $dni;
    public $foto_base64;
    public $pass;
    public $sta;
    public $med;
    public $tip;
    public $nom;
    public $ape;
    public $fnac;
    public $tcel;
    public $tcas;
    public $tofi;
    public $mai;
    public $dir;
    public $nac;
    public $depa;
    public $prov;
    public $dist;
    public $prof;
    public $san;
    public $don;
    public $raz;
    public $talla;
    public $peso;
    public $rem;
    public $nota;
    public $fec;
    public $idsedes;
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

    function read(){
        // select all query
        $query = "SELECT c.name as category_name, p.id, p.name, p.description, p.price, p.category_id, p.created
            FROM " . $this->table_name . " p
            LEFT JOIN categories c ON p.category_id = c.id
            ORDER BY p.created DESC";
    
        // prepare query statement
        $stmt = $this->conn->prepare($query);
    
        // execute query
        $stmt->execute();
    
        return $stmt;
    }

    function readOne(){
    
        // query to read single record
        $query = "SELECT c.name as category_name, p.id, p.name, p.description, p.price, p.category_id, p.created
            FROM " . $this->table_name . " p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.id = ?
            LIMIT 1";
    
        // prepare query statement
        $stmt = $this->conn->prepare( $query );
    
        // bind id of product to be updated
        $stmt->bindParam(1, $this->id);
    
        // execute query
        $stmt->execute();
    
        // get retrieved row
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
        // set values to object properties
        $this->name = $row['name'];
        $this->price = $row['price'];
        $this->description = $row['description'];
        $this->category_id = $row['category_id'];
        $this->category_name = $row['category_name'];
    }

    function foto_base64() {
        $path = $_SERVER["DOCUMENT_ROOT"] . "/app/paci/" . $this->dni . "/foto.jpg";

        if (!file_exists($path)) {
            $path = $_SERVER["DOCUMENT_ROOT"]  . "/app/_images/foto.gif";
        }

        /* $this->foto_base64 = $path; */
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $this->foto_base64 = 'data:image/' . $type . '; base64,' . base64_encode($data);
    }

    function create() {
        // query to insert record
        $query = "INSERT INTO " . $this->table_name . " SET dni=:dni, pass=:pass, sta=:sta, med=:med, tip=:tip, nom=:nom, ape=:ape, fnac=:fnac, tcel=:tcel, tcas=:tcas, tofi=:tofi, mai=:mai, dir=:dir, nac=:nac, depa=:depa, prov=:prov, dist=:dist, prof=:prof, san=:san, don=:don, raz=:raz, talla=:talla, peso=:peso, rem=:rem, nota=:nota, fec=:fec, idsedes=:idsedes, estado=:estado, idusercreate=:idusercreate";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // bind values
        $stmt->bindParam(":dni", htmlspecialchars(strip_tags($this->dni)));
        $stmt->bindParam(":pass", htmlspecialchars(strip_tags($this->pass)));
        $stmt->bindParam(":sta", htmlspecialchars(strip_tags($this->sta)));
        $stmt->bindParam(":med", htmlspecialchars(strip_tags($this->med)));
        $stmt->bindParam(":tip", htmlspecialchars(strip_tags($this->tip)));
        $stmt->bindParam(":nom", htmlspecialchars(strip_tags($this->nom)));
        $stmt->bindParam(":ape", htmlspecialchars(strip_tags($this->ape)));
        $stmt->bindParam(":fnac", htmlspecialchars(strip_tags($this->fnac)));
        $stmt->bindParam(":tcel", htmlspecialchars(strip_tags($this->tcel)));
        $stmt->bindParam(":tcas", htmlspecialchars(strip_tags($this->tcas)));
        $stmt->bindParam(":tofi", htmlspecialchars(strip_tags($this->tofi)));
        $stmt->bindParam(":mai", htmlspecialchars(strip_tags($this->mai)));
        $stmt->bindParam(":dir", htmlspecialchars(strip_tags($this->dir)));
        $stmt->bindParam(":nac", htmlspecialchars(strip_tags($this->nac)));
        $stmt->bindParam(":depa", htmlspecialchars(strip_tags($this->depa)));
        $stmt->bindParam(":prov", htmlspecialchars(strip_tags($this->prov)));
        $stmt->bindParam(":dist", htmlspecialchars(strip_tags($this->dist)));
        $stmt->bindParam(":prof", htmlspecialchars(strip_tags($this->prof)));
        $stmt->bindParam(":san", htmlspecialchars(strip_tags($this->san)));
        $stmt->bindParam(":don", htmlspecialchars(strip_tags($this->don)));
        $stmt->bindParam(":raz", htmlspecialchars(strip_tags($this->raz)));
        $stmt->bindParam(":talla", htmlspecialchars(strip_tags($this->talla)));
        $stmt->bindParam(":peso", htmlspecialchars(strip_tags($this->peso)));
        $stmt->bindParam(":rem", htmlspecialchars(strip_tags($this->rem)));
        $stmt->bindParam(":nota", htmlspecialchars(strip_tags($this->nota)));
        $stmt->bindParam(":fec", htmlspecialchars(strip_tags($this->fec)));
        $stmt->bindParam(":idsedes", htmlspecialchars(strip_tags($this->idsedes)));
        $stmt->bindParam(":estado", htmlspecialchars(strip_tags($this->estado)));
        $stmt->bindParam(":idusercreate", htmlspecialchars(strip_tags($this->idusercreate)));
        // execute query
        if($stmt->execute()){
            return true;
        }

        return false;
    }

    function update(){
    
        // update query
        $query = "UPDATE
                    " . $this->table_name . "
                SET
                    name = :name,
                    price = :price,
                    description = :description,
                    category_id = :category_id
                WHERE
                    id = :id";
    
        // prepare query statement
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->name=htmlspecialchars(strip_tags($this->name));
        $this->price=htmlspecialchars(strip_tags($this->price));
        $this->description=htmlspecialchars(strip_tags($this->description));
        $this->category_id=htmlspecialchars(strip_tags($this->category_id));
        $this->id=htmlspecialchars(strip_tags($this->id));
    
        // bind new values
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':price', $this->price);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':category_id', $this->category_id);
        $stmt->bindParam(':id', $this->id);
    
        // execute the query
        if($stmt->execute()){
            return true;
        }
    
        return false;
    }

    function delete(){
    
        // delete query
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
    
        // prepare query
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->id=htmlspecialchars(strip_tags($this->id));
    
        // bind id of record to delete
        $stmt->bindParam(1, $this->id);
    
        // execute query
        if($stmt->execute()){
            return true;
        }
    
        return false;
        
    }

    function search($keywords){
    
        // select all query
        $query = "SELECT
                    c.name as category_name, p.id, p.name, p.description, p.price, p.category_id, p.created
                FROM
                    " . $this->table_name . " p
                    LEFT JOIN
                        categories c
                            ON p.category_id = c.id
                WHERE
                    unaccent(p.name) ILIKE ? OR unaccent(p.description) ILIKE ? OR c.name ILIKE ?
                ORDER BY
                    p.created DESC";
    
        // prepare query statement
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $keywords=htmlspecialchars(strip_tags($keywords));
        $keywords = "%{$keywords}%";
    
        // bind
        $stmt->bindParam(1, $keywords);
        $stmt->bindParam(2, $keywords);
        $stmt->bindParam(3, $keywords);
    
        // execute query
        $stmt->execute();
    
        return $stmt;
    }

    public function readPaging($from_record_num, $records_per_page){
    
        // select query
        $query = "SELECT
                    c.name as category_name, p.id, p.name, p.description, p.price, p.category_id, p.created
                FROM
                    " . $this->table_name . " p
                    LEFT JOIN
                        categories c
                            ON p.category_id = c.id
                ORDER BY p.created DESC
                LIMIT ?, ?";
    
        // prepare query statement
        $stmt = $this->conn->prepare( $query );
    
        // bind variable values
        $stmt->bindParam(1, $from_record_num, PDO::PARAM_INT);
        $stmt->bindParam(2, $records_per_page, PDO::PARAM_INT);
    
        // execute query
        $stmt->execute();
    
        // return values from database
        return $stmt;
    }

    public function count(){
        $query = "SELECT COUNT(*) as total_rows FROM " . $this->table_name . "";
    
        $stmt = $this->conn->prepare( $query );
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
        return $row['total_rows'];
    }
}