<?php
//error_reporting(E_ALL);
class Cls_DBManager{ 
// Atributos // 

// creamos el contructor y  La conexion se crea al crear la instancia de la clase DBManager ... el contructor es lo que se conoce como metodo init()

private $servidor;
	private $usuario;
	private $password;
	private $base_datos;
	private $link;
	private $consulta;
	private $result;
	static $_instance;
	
	function __construct($conexion_persistente=false){
		$this->usuario="";
		$this->password="";
		$this->servidor="";
		$this->base_datos="";
		$this->conectar($conexion_persistente);
	}
		
/* Realliza la conexión a la base de datos */
	public function conectar($conexion_persistente=false){
		try {
				$ip_local="";
				if(isset($_SERVER['REMOTE_ADDR'])){
					$ip_local=$_SERVER['REMOTE_ADDR']; //IP QUE REALIZA LA PETICION
				}
				$ip_local=str_replace(".","_",$ip_local); //IP SIN PUNTOS
				$ip_local_reenviada="";
				if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])){ 
					$ip_local_reenviada=$_SERVER['HTTP_X_FORWARDED_FOR']; //IP REMOTA
				}
				$ip_local_reenviada=str_replace(".","_",$ip_local_reenviada); //IP REMOTA SIN PUNTOS
				if($ip_local_reenviada==$ip_local || $ip_local_reenviada==""){ $ip_local_reenviada=""; }else{ $ip_local_reenviada="_R".$ip_local_reenviada; } //SE CONCATENA PARA MAYOR TRAZABILIDAD
			//$this->link = new PDO('mysql:host=localhost;dbname='.$this->base_datos, $this->usuario, $this->password);
			$this->link = new PDO('pgsql:dbname=jg_db_001;host=localhost;application_name=jg_db_001_'.$ip_local.$ip_local_reenviada, 'postgres', '123', array(PDO::ATTR_PERSISTENT => $conexion_persistente));
			//$this->link->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, TRUE);
		} catch (PDOException $e) {
			print "<p>Error: No puede conectarse con la base de datos.</p>\n".$e->getMessage();
			//print "<p>Error: ".$e->getMessage()."</p>\n";
			exit();
		}
	}

	public function Ejecutar($sql){
		try {
			$consulta = $this->link->prepare($sql);
			if(!$consulta->execute()){
                //echo $sql.'<br><br><br>';
                $error = $consulta->errorInfo();
                throw new Exception("Error en ejecución de consulta. ".$error[2]);
			}
			
		} catch (Exception $e){
			echo $e->GetMessage();
            exit;
		}
		@$r = $consulta->fetchAll(PDO::FETCH_ASSOC);
		return $r;
	}
	
	public function EjecutarParaScript($sql){
		$archivo="";
		if(isset($_SESSION['nombre_usuario'])){
			$archivo=$_SESSION['nombre_usuario'].".log";
		}
		else{
			$archivo="login.log";
		}
		
		$fp = fopen ("../../LogSql/$archivo","a");
		$texto=date("Y-m-d H:i:s")."---".$sql."\n";
		fwrite ($fp, $texto);
		fclose($fp);
		
		try {
			$consulta = $this->link->prepare($sql);
			if(!$consulta->execute()){
				$mensaje_error = ($this->link->errorInfo());
				throw new Exception($mensaje_error[2]);
			}
			
		} catch (PDOException $e) {
			//echo 'error: ' . $e->getMessage();
			$fp = fopen ("../../LogSql/$archivo","a");
			fwrite ($fp, date("Y-m-d H:i:s")." ---". $e->getMessage()."\n".$this->link->errorInfo()."\n");
			fclose($fp);
		}
		@$r = $consulta->fetchAll(PDO::FETCH_ASSOC);
		return $r;
	}
	
	//Devuelve el último id del insert introducido
	public function lastID(){
		//return $this->link->lastInsertId();
		$data = $this->link->query("select lastval() ultimo");
		$registro = $data->fetchAll(PDO::FETCH_ASSOC);
		return $registro[0]['ultimo'];
	}
	
	public function Begin(){
		$consulta = $this->link->prepare("BEGIN");
		$consulta->execute();
	}
	
	public function Commit(){
		$consulta = $this->link->prepare("COMMIT");
		$consulta->execute();
	}
	
	public function RollBack(){
		$consulta = $this->link->prepare("ROLLBACK");
		$consulta->execute();
	}
	
	/*
	//COPIADO
	protected $transactionCounter = 0; 
    function beginTransaction() 
    { 
        if(!$this->transactionCounter++) 
            return $this->beginTransaction(); 
       return $this->transactionCounter >= 0; 
    } 

    function CommitTransaction() 
    { 
       if(!--$this->transactionCounter) 
           return $this->CommitTransaction(); 
       return $this->transactionCounter >= 0; 
    } 

    function RollbackTransaction() 
    { 
        if($this->transactionCounter >= 0) 
        { 
            $this->transactionCounter = 0; 
            return $this->RollbackTransaction(); 
        } 
        $this->transactionCounter = 0; 
        return false; 
    } 
	*/
}
?>