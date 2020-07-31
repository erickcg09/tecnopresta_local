<?php

require_once 'conexion.php';

class sql {

	function conActivo($nombreActivo){

		// el array es para que el JSON funcione con tíldes
		$pdo = new \PDO(DB_Str, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		
		if ($pdo != null) {		
			
			$consultaSQL = "SELECT id_activo, etiqueta, marca, 
							descripcion, imagen FROM t_activo						
								INNER JOIN t_marca 
									ON t_activo.id_marca = t_marca.id_marca 								
							WHERE etiqueta like '%$nombreActivo%' ORDER BY etiqueta";

			$sql = $pdo->query($consultaSQL);

			$rs = [];
			while ($row = $sql->fetch(\PDO::FETCH_ASSOC)) {
					$rs[] = [						
						'id_activo' => $row['id_activo'],	                
						'etiqueta' => $row['etiqueta'],
						'marca' => $row['marca'],
						'descripcion' => $row['descripcion'],
						'imagen' => $row['imagen']									
					];	
			}
            return $rs;
		}	
		$pdo = null;
	}
	
	function conActivoId($id_activo){

		// el array es para que el JSON funcione con tíldes
		$pdo = new \PDO(DB_Str, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		
		if ($pdo != null) {		
			
			$consultaSQL = "SELECT id_activo, etiqueta, marca, 
							descripcion, imagen FROM t_activo						
								INNER JOIN t_marca 
									ON t_activo.id_marca = t_marca.id_marca 								
							WHERE id_activo = $id_activo";

			$sql = $pdo->query($consultaSQL);

			$rs = [];
			while ($row = $sql->fetch(\PDO::FETCH_ASSOC)) {
					$rs[] = [						
						'id_activo' => $row['id_activo'],	                
						'etiqueta' => $row['etiqueta'],
						'marca' => $row['marca'],
						'descripcion' => $row['descripcion'],
						'imagen' => $row['imagen']									
					];	
			}
            return $rs;
		}	
		$pdo = null;
	}

	function conMarca(){

		// el array es para que el JSON funcione con tíldes
		$pdo = new \PDO(DB_Str, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		
		if ($pdo != null) {		
			
			$consultaSQL = "SELECT id_marca, marca FROM t_marca	ORDER BY marca";
			$sql = $pdo->query($consultaSQL);
			$rs = [];
			while ($row = $sql->fetch(\PDO::FETCH_ASSOC)) {
					$rs[] = [						
						'id_marca' => $row['id_marca'],	                
						'marca' => $row['marca']									
					];	
			}
            return $rs;
		}	
		$pdo = null;
	}

	function conAlias(){

		// el array es para que el JSON funcione con tíldes
		$pdo = new \PDO(DB_Str, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		
		if ($pdo != null) {		
			
			$consultaSQL = "SELECT alias_id, alias FROM t_alias	ORDER BY alias";
			$sql = $pdo->query($consultaSQL);
			$rs = [];
			while ($row = $sql->fetch(\PDO::FETCH_ASSOC)) {
					$rs[] = [						
						'alias_id' => $row['alias_id'],	                
						'alias' => $row['alias']									
					];	
			}
            return $rs;
		}	
		$pdo = null;
	}

	function conActivoAliasId($alias_id){

		// el array es para que el JSON funcione con tíldes
		$pdo = new \PDO(DB_Str, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		
		if ($pdo != null) {		
			
			$consultaSQL = "SELECT t_alias.alias_id, alias, alias_imagen, 
							(disponible_cantidad - disponible_prestado) AS disponible 
							FROM t_alias						
								INNER JOIN t_disponible 
									ON t_alias.alias_id = t_disponible.alias_id
									WHERE t_alias.alias_Id = $alias_id";

			$sql = $pdo->query($consultaSQL);
			$rs = [];
			while ($row = $sql->fetch(\PDO::FETCH_ASSOC)) {
					$rs[] = [						
						'alias_id' => $row['alias_id'],	                
						'alias' => $row['alias'],
						'alias_imagen' => $row['alias_imagen'],
						'disponible' => $row['disponible']									
					];	
			}
            return $rs;
		}	
		$pdo = null;
	}

	function conActivoAlias($aliasActivo){

		// el array es para que el JSON funcione con tíldes
		$pdo = new \PDO(DB_Str, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		
		if (empty($aliasActivo)) {

			$consultaSQL = "SELECT t_alias.alias_id, alias, alias_imagen, 
							(disponible_cantidad - disponible_prestado) AS disponible 
							FROM t_alias						
								INNER JOIN t_disponible 
									ON t_alias.alias_id = t_disponible.alias_id 								
							ORDER BY alias";

		} else {

			$consultaSQL = "SELECT t_alias.alias_id, alias, alias_imagen, 
							(disponible_cantidad - disponible_prestado) AS disponible 
							FROM t_alias						
							INNER JOIN t_disponible 
							ON t_alias.alias_id = t_disponible.alias_id 								
							WHERE alias like '%$aliasActivo%' ORDER BY alias";			
		}

		if ($pdo != null) {								

			$sql = $pdo->query($consultaSQL);

			$rs = [];
			while ($row = $sql->fetch(\PDO::FETCH_ASSOC)) {
					$rs[] = [						
						'alias_id' => $row['alias_id'],	                
						'alias' => $row['alias'],
						'alias_imagen' => $row['alias_imagen'],
						'disponible' => $row['disponible']							
					];	
			}
            return $rs;
		}	
		$pdo = null;
	}

	function conSolicitud(){

		// el array es para que el JSON funcione con tíldes
		$pdo = new \PDO(DB_Str, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
				
		$consultaSQL = "SELECT solicitud_fechaRetiro, solicitud_fechaDevolucion,
						solicitud_cantidad, t_alias.alias_id, alias, alias_imagen						
						FROM t_solicitud 
						INNER JOIN t_alias 
							ON t_solicitud.alias_id = t_alias.alias_id";
		
		if ($pdo != null) {								

			$sql = $pdo->query($consultaSQL);

			$rs = [];
			while ($row = $sql->fetch(\PDO::FETCH_ASSOC)) {
					$rs[] = [						
						'solicitud_fechaRetiro' => $row['solicitud_fechaRetiro'],	                
						'solicitud_fechaDevolucion' => $row['solicitud_fechaDevolucion'],
						'solicitud_cantidad' => $row['solicitud_cantidad'],
						'alias_id' => $row['alias_id'],
						'alias' => $row['alias'],
						'alias_imagen' => $row['alias_imagen']							
					];	
			}
            return $rs;
		}	
		$pdo = null;
	}
	
	function conActivoPrestamoAliasId($alias_id){

		// el array es para que el JSON funcione con tíldes
		$pdo = new \PDO(DB_Str, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		
		if ($pdo != null) {		
			
			$consultaSQL = "SELECT id_activo, nombre, modelo, marca, color, numero_activo 
							FROM t_activo						
							INNER JOIN t_marca 
								ON t_activo.id_marca = t_marca.id_marca
							INNER JOIN t_color
								ON t_color.id_color = t_activo.id_color 								
							WHERE alias_id = $alias_id";

			$sql = $pdo->query($consultaSQL);

			$rs = [];
			while ($row = $sql->fetch(\PDO::FETCH_ASSOC)) {
					$rs[] = [						
						'id_activo' => $row['id_activo'],	                
						'nombre' => $row['nombre'],
						'modelo' => $row['modelo'],
						'marca' => $row['marca'],
						'color' => $row['color'],
						'numero_activo' => $row['numero_activo']																		
					];	
			}
            return $rs;
		}	
		$pdo = null;
	}

}

?>
