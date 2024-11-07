<?php 
namespace App\Http\Controllers; 

use App\Models\Home;   
use App\Models\Usuario\Usuario;   
use Jenssegers\Blade\Blade;
use App\Core\Request;
use App\Helpers\RouteSelector;
use App\Helpers\AuthHelper;
/**
 * Controlador principal para manejar las solicitudes a la aplicación.
 */
class HomeController extends Controller {
 
    protected $blade;   

    /**
     * Constructor del controlador.
     *
     * @param Blade $blade Instancia del motor de plantillas Blade.
     */
    public function __construct(Blade $blade) {
        $this->blade = $blade;
        
		if (session_status() == PHP_SESSION_NONE) {
            session_start(); // Inicia la sesión si no está iniciada
        }
    }
	public function handle($request)
    {
        // Default handle method for controllers
    }
    /**
     * Método para mostrar la vista principal.
     */
    public function index() { 
        if(isset($_SESSION['login'])){
            $this->menu(); exit;
        }
        $data = ['name' => 'Bienvenido a blade']; 
        echo $this->blade->make('index', $data)->render(); exit;
    } 

    /**
     * Método para validar el login de un usuario.
     */
    public function validate(){  
        if ((isset($_POST['login']) && !empty($_POST['login'])) && $this->authentification($_POST['login'], $this->hash($_POST['pass']))) {
            $this->addUserLog(mb_strtolower($_POST['login']), 1, date("Y-m-d H:i:s"));
            $this->addLogInmater(mb_strtolower($_POST['login']), date("Y-m-d H:i:s"));
            $_SESSION['login'] = strtolower(trim(preg_replace('/\s+/',' ', $_POST['login'])));
			$_SESSION['_token'] = env('TOKEN_FORM'); 
            //echo 'logeado'; exit;
            $dir = $_SERVER['HTTP_HOST'] . substr(dirname(__FILE__), strlen($_SERVER['DOCUMENT_ROOT']));
            $this->menu();
        } else {
            header('Location: /index.php'); 
        }
    }

    /**
     * Método para mostrar una vista de bienvenida.
     *
     * @param string $view Nombre de la vista.
     * @param array $data Datos a pasar a la vista.
     */
    public function admin() {
        $this->render("test");
    }
	
	public function unauthorized(){
		echo $this->blade->make('404')->render(); exit;
	}
    
    public function forbidden(){
		echo $this->blade->make('forbidden')->render(); exit;
	}
    public function menu(){
        if(!isset($_SESSION['login']) ){
            echo $this->blade->make('index')->render(); exit;
        }
        
        $p_data = $this->getUser($_SESSION['login']);
        $data = $p_data['data'];
        $route = '';
        $_SESSION['role'] =  $data["role"]; 
        $route = RouteSelector::selectRoute($data); 
        $dataArray = $data->toArray();
        header('Location: /' . $route); 
    }
    /**
     * Método para hashear una contraseña.
     *
     * @param string $password Contraseña a hashear.
     * @return string Hash de la contraseña.
     */
    public function hash($password) {
        return hash('sha512', $password);
    }

    /**
     * Método para verificar una contraseña.
     *
     * @param string $password Contraseña en texto plano.
     * @param string $hash Hash a comparar.
     * @return bool Resultado de la verificación.
     */
    public function verify($password, $hash) {
        return ($hash == self::hash($password));
    }

    /**
     * Método para autenticar a un usuario.
     *
     * @param string $username Nombre de usuario.
     * @param string $pass Contraseña hasheada.
     * @return bool Resultado de la autenticación.
     */
    function authentification($username, $pass) {    
        $hasResults = Usuario::where('userx', $username)
            ->where('pass', $pass)
            ->where('estado', 1)
            ->exists();

        return $hasResults !== false;
    }

    /**
     * Método para agregar un registro de usuario.
     *
     * @param string $username Nombre de usuario.
     * @param int $idusercreate ID del usuario que creó el registro.
     * @param string $createdate Fecha de creación del registro.
     * @return mixed Resultado de la operación.
     */
    function addUserLog($username, $idusercreate, $createdate) {
        $user = new Home(); 
        return $user->addUserLog($username, $idusercreate, $createdate);
    }

    /**
     * Método para agregar un registro en Inmater.
     *
     * @param string $username Nombre de usuario.
     * @param string $createdate Fecha de creación del registro.
     * @return mixed Resultado de la operación.
     */
    function addLogInmater($username, $createdate) {
        $user = new Home(); 
        return $user->addLogInmater($username, $createdate);
    }

    /**
     * Método para obtener los datos de un usuario.
     *
     * @param string $user_id ID del usuario.
     * @return mixed Datos del usuario.
     */
    function getUser($user_id) {
        $user = new Home(); 
        return $user->getUser($user_id); 
    }

    /**
     * Métodos para renderizar las vistas de listas'.
     */
    public function lista() {  
        $this->render('list/lista'); 
    }

    public function listaFacturacion() { 
        $this->render('list/lista-facturacion');  
    }

    public function listaGenomics() { 
        $this->render('list/lista-genomics');  
    }

    public function listaEcografia() { 
        $this->render('list/lista-ecografia'); 
        echo $this->blade->make('')->render();
    }

    public function listaHisteroscopias() { 
        $this->render('list/lista-histeroscopias');  
    }

    public function listaConsulta() { 
        $this->render('list/lista-consulta');  
    }

    public function listaAdminlab() { 
        $this->render('list/lista-adminlab');  
    }

    public function listaAdmin() { 
        $this->render('list/lista-admin');  
    }

    public function listaSistemas() { 
        $this->render('list/lista-sistemas');  
    }

    public function auditoriaFacturacion() { 
        $this->render('list/auditoria-facturacion');  
    } 

    public function listaTransferencias() { 
        $this->render('list/lista-transferencias');  
    } 

    public function listaMarketing() { 
        $this->render('list/lista-marketing');  
    }    
}
