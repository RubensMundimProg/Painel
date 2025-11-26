<?php 

namespace Estrutura\Service;

class Conexao{
	/// Define o Ambiente
	public static $ambiente;
	
	private static $usuario;
	private static $senha;
	private static $banco;
	private static $host;
	private static $dsn;
	private static $debug = false;
	
	private static $conexao = '';

    static function setAmbiente($ambiente){
        self::$ambiente = $ambiente;
    }
	
	static function conectar(){
		self::definirBanco(self::$ambiente);
		
		$host = self::$host;
		$usuario = self::$usuario;
		$senha = self::$senha;
		$banco = self::$banco;
		$dsn = self::$dsn;

		if(self::$conexao == ''){
			try{
				self::$conexao = new \PDO($dsn, ''.$usuario.'', ''.$senha.'', array(\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''));
			}catch(\PDOException $e){
				echo $e->getMessage();
				exit;
			}
		};
	}
	
	static function desconectar(){
		self::$conexao = null;
	}
	
	static function definirBanco($banco='linux'){
        $conexao = \Estrutura\Service\Config::getConfig('db');

        $banco = APPLICATION_ENV;
        $dados[$banco] = ['host'=>$conexao['host'],'usuario'=>$conexao['username'],'senha'=>$conexao['password'],'banco'=>$conexao['database'],'dsn'=>$conexao['dsn']];

		try{
			if(isset($dados[$banco])){
				self::$host    = $dados[$banco]['host'];
				self::$usuario = $dados[$banco]['usuario'];
				self::$senha   = $dados[$banco]['senha'];
				self::$banco   = $dados[$banco]['banco'];
				self::$dsn     = $dados[$banco]['dsn'];
			}else{
				throw new \Exception('Configuração do Banco Selecionado não Existe!');
			}
		}catch(\Exception $e){
			echo $e->getMessage();
			die;
		}
	}
	
	static function listarSql($sql){
		if(self::$debug){
			echo $sql.'<br />';
		};
		
		self::conectar();
		
		try{
			$stmt = self::$conexao->prepare($sql);
			$stmt->execute();
			$lista = array();
			for($i=0; $obj = $stmt->fetchObject(); $i++){
				$lista[$i] = $obj;
			}
			
		self::desconectar();
			
			return $lista;
		}catch (Exception $e){
			echo $e->getMessage();
			die;
		}
	}
	
	static function execSql($sql){
		if(self::$debug){
			echo $sql.'<br />';
		};
		
		self::conectar();
		
		$status = false;
		if(self::$conexao->exec($sql)){
			$status = true;
		}else{
			$erro = self::$conexao->errorInfo();
			if($erro[0] == 00000){
				$status =  true;
			}else{
				echo $sql.'<br />';
				x(self::$conexao->errorInfo());
				die;
			}
		}
		
		self::desconectar();
		return $status;
	}
	
	static function rowCount($sql){
		if(self::$debug){
			echo $sql.'<br />';
		};
		
		self::conectar();
		
		$stmt = self::$conexao->prepare($sql);
		$stmt->execute();
		$obj = $stmt->rowCount();
		
		self::desconectar();
		return $obj;
	}
	
	static function lerSql($sql){
		if(self::$debug){
			echo $sql.'<br />';
		};
		
		self::conectar();
		
		$stmt = self::$conexao->prepare($sql);
		$stmt->execute();
		$obj = $stmt->fetchObject();
		
		self::desconectar();
		return $obj;
	}
	
	static function selectBanco($tabela){
		$sql = 'SELECT * FROM '.$tabela;
		
		$lista = self::listarSql($sql);
		
		$array = array();
		foreach($lista as $linha){
			$array[$linha->id] = $linha->nome;
		}
		
		return $array;
	}
	
 	static function begin(){
         return self::execSql("START TRANSACTION");
     }

     static function commit(){
         return self::execSql('COMMIT TRANSACTION');
     }

     static function rollback(){
         return self::execSql("ROLLBACK TRANSACTION");
     }
	 
	 static function debug(){
	 	self::$debug = true;
	 }
}
