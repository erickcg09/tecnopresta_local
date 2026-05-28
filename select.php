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

	function conActivoAlias($aliasActivo, $codigo){

		// el array es para que el JSON funcione con tíldes
		$pdo = new \PDO(DB_Str, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		
		if (empty($aliasActivo)) {

			$consultaSQL = "SELECT t_alias.alias_id, alias, alias_imagen							 
							FROM t_alias						
							WHERE codigo = $codigo ORDER BY alias";

		} else {

			$consultaSQL = "SELECT t_alias.alias_id, alias, alias_imagen 
							FROM t_alias						
							WHERE codigo = $codigo and alias like '%$aliasActivo%' 
							ORDER BY alias";			
		}

		if ($pdo != null) {								

			$sql = $pdo->query($consultaSQL);

			$rs = [];
			while ($row = $sql->fetch(\PDO::FETCH_ASSOC)) {
					$rs[] = [						
						'alias_id' => $row['alias_id'],	                
						'alias' => $row['alias'],
						'alias_imagen' => $row['alias_imagen']						
					];	
			}
            return $rs;
		}	
		$pdo = null;
	}

	function conSolicitud($codigo){

		// el array es para que el JSON funcione con tíldes
		$pdo = new \PDO(DB_Str, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
				
		$consultaSQL = "SELECT t_solicitud.solicitud_Id, solicitud_fecha, solicitud_fechaRetiro, 
						solicitud_fechaDevolucion, solicitud_uso, solicitud_nombre_funcionario,
						solicitud_horaRetiro, solicitud_horaDevolucion					
						FROM  t_solicitud
						WHERE t_solicitud.solicitud_codigo_presupuestario = $codigo and solicitud_aprobada = 0
						ORDER BY solicitud_fechaRetiro,solicitud_fechaDevolucion";
		
		if ($pdo != null) {								

			$sql = $pdo->query($consultaSQL);

			$rs = [];
			while ($row = $sql->fetch(\PDO::FETCH_ASSOC)) {
					$rs[] = [											
						'solicitud_Id' => $row['solicitud_Id'],
						'solicitud_fecha' => $row['solicitud_fecha'],
						'solicitud_fechaRetiro' => $row['solicitud_fechaRetiro'],
						'solicitud_fechaDevolucion' => $row['solicitud_fechaDevolucion'],
						'solicitud_uso' => $row['solicitud_uso'],
						'solicitud_nombre_funcionario' => $row['solicitud_nombre_funcionario'],
						'solicitud_horaRetiro' => $row['solicitud_horaRetiro'],
						'solicitud_horaDevolucion' => $row['solicitud_horaDevolucion']					
					];
			}
            return $rs;
		}	
		$pdo = null;
	}

	function conPrestamoEncabezado($prestamo_Id, $codigo) {

		// el array es para que el JSON funcione con tíldes
		$pdo = new \PDO(DB_Str, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
				
		$consultaSQL = "SELECT prestamo_Id, prestamo_fecha, prestamo_fechaRetiro, prestamo_horaRetiro, 
						prestamo_fechaDevolucion, prestamo_horaDevolucion, prestamo_uso, prestamo_nombre_funcionario					
						FROM  t_prestamo
						WHERE prestamo_Id = $prestamo_Id and prestamo_codigo_presupuestario = '$codigo'";
		
		if ($pdo != null) {								

			$sql = $pdo->query($consultaSQL);

			$rs = [];
			while ($row = $sql->fetch(\PDO::FETCH_ASSOC)) {
					$rs[] = [											
						'prestamo_Id' => $row['prestamo_Id'],
						'prestamo_fecha' => $row['prestamo_fecha'],
						'prestamo_fechaRetiro' => $row['prestamo_fechaRetiro'],
						'prestamo_horaRetiro' => $row['prestamo_horaRetiro'],
						'prestamo_fechaDevolucion' => $row['prestamo_fechaDevolucion'],
						'prestamo_horaDevolucion' => $row['prestamo_horaDevolucion'],
						'prestamo_uso' => $row['prestamo_uso'],
						'prestamo_nombre_funcionario' => $row['prestamo_nombre_funcionario']					
					];
			}
            return $rs;
		}	
		$pdo = null;
	}

	function conPrestamoDetalleActivo($prestamo_Id, $codigo){

		// el array es para que el JSON funcione con tíldes
		$pdo = new \PDO(DB_Str, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		
		if ($pdo != null) {		
			
			$consultaSQL = "SELECT t_activo.id_activo, modelo, marca, color, numero_activo 
							FROM t_activo						
							INNER JOIN t_marca 
								ON t_activo.id_marca = t_marca.id_marca
							INNER JOIN t_color
								ON t_color.id_color = t_activo.id_color
							INNER JOIN t_placa
								ON t_placa.id_activo = t_activo.id_activo and codigo = '$codigo'  
							INNER JOIN t_prestamo_detalle
								ON t_prestamo_detalle.prestamo_detalle_id_activo = t_placa.id_activo
							WHERE t_prestamo_detalle.prestamo_Id = $prestamo_Id";

			$sql = $pdo->query($consultaSQL);

			$rs = [];
			while ($row = $sql->fetch(\PDO::FETCH_ASSOC)) {
					$rs[] = [						
						'id_activo' => $row['id_activo'],	                						
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

	function conSolicitudEncabezado($solicitud_Id) {

		// el array es para que el JSON funcione con tíldes
		$pdo = new \PDO(DB_Str, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
				
		$consultaSQL = "SELECT solicitud_Id, solicitud_fecha, solicitud_fechaRetiro,
						solicitud_horaRetiro, solicitud_fechaDevolucion, solicitud_horaDevolucion, 
						solicitud_uso, solicitud_nombre_funcionario					
						FROM  t_solicitud
						WHERE solicitud_Id = $solicitud_Id";
		
		if ($pdo != null) {								

			$sql = $pdo->query($consultaSQL);

			$rs = [];
			while ($row = $sql->fetch(\PDO::FETCH_ASSOC)) {
					$rs[] = [											
						'solicitud_Id' => $row['solicitud_Id'],
						'solicitud_fecha' => $row['solicitud_fecha'],
						'solicitud_fechaRetiro' => $row['solicitud_fechaRetiro'],
						'solicitud_horaRetiro' => $row['solicitud_horaRetiro'],
						'solicitud_fechaDevolucion' => $row['solicitud_fechaDevolucion'],
						'solicitud_horaDevolucion' => $row['solicitud_horaDevolucion'],
						'solicitud_uso' => $row['solicitud_uso'],
						'solicitud_nombre_funcionario' => $row['solicitud_nombre_funcionario']					
					];
			}
            return $rs;
		}	
		$pdo = null;
	}
	
	function conSolicitudDetalle($solicitud_Id) {

		// el array es para que el JSON funcione con tíldes
		$pdo = new \PDO(DB_Str, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
				
		$consultaSQL = "SELECT solicitud_detalle_cantidad, alias, alias_imagen 
						FROM t_solicitud_detalle
						INNER JOIN t_alias 
						ON t_solicitud_detalle.solicitud_detalle_alias_id = t_alias.alias_id
						WHERE solicitud_Id = $solicitud_Id";
		
		if ($pdo != null) {								

			$sql = $pdo->query($consultaSQL);

			$rs = [];
			while ($row = $sql->fetch(\PDO::FETCH_ASSOC)) {
					$rs[] = [											
						'solicitud_detalle_cantidad' => $row['solicitud_detalle_cantidad'],
						'alias' => $row['alias'],
						'alias_imagen' => $row['alias_imagen']					
					];
			}
            return $rs;
		}	
		$pdo = null;
	}

	function conSolicitudDetalleActivo($solicitud_Id, $codigo){

		// el array es para que el JSON funcione con tíldes
		$pdo = new \PDO(DB_Str, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		
		if ($pdo != null) {		
			
			$consultaSQL = "SELECT t_activo.id_activo, modelo, marca, color, numero_activo, solicitud_detalle_cantidad 
							FROM t_activo						
							INNER JOIN t_marca 
								ON t_activo.id_marca = t_marca.id_marca
							INNER JOIN t_color
								ON t_color.id_color = t_activo.id_color
							INNER JOIN t_placa
								ON t_placa.id_activo = t_activo.id_activo and codigo = '$codigo'
							INNER JOIN t_solicitud_detalle
								ON t_solicitud_detalle.solicitud_detalle_alias_id = t_placa.alias_id
							WHERE t_solicitud_detalle.solicitud_Id = $solicitud_Id";

			$sql = $pdo->query($consultaSQL);

			$rs = [];
			while ($row = $sql->fetch(\PDO::FETCH_ASSOC)) {
					$rs[] = [						
						'id_activo' => $row['id_activo'],	                						
						'modelo' => $row['modelo'],
						'marca' => $row['marca'],
						'color' => $row['color'],
						'numero_activo' => $row['numero_activo'],
						'solicitud_detalle_cantidad' => $row['solicitud_detalle_cantidad']								
					];	
			}
            return $rs;
		}	
		$pdo = null;
	}

	function conCantidadSolicitudes($codigo){

		// el array es para que el JSON funcione con tíldes
		$pdo = new \PDO(DB_Str, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		
		if ($pdo != null) {		
			
			$consultaSQL = "SELECT COUNT(*) AS cantidad FROM t_solicitud
							INNER JOIN t_solicitud_detalle 
							ON t_solicitud_detalle.solicitud_Id = t_solicitud.solicitud_Id
							WHERE solicitud_codigo_presupuestario = '$codigo' and
							solicitud_aprobada = 0";

			$sql = $pdo->query($consultaSQL);

			$rs = [];
			while ($row = $sql->fetch(\PDO::FETCH_ASSOC)) {
					$rs[] = [						
						'cantidad' => $row['cantidad']																		
					];
			}
            return $rs;
		}	
		$pdo = null;
	}

	function conCantidadPrestamo($codigo){

		// el array es para que el JSON funcione con tíldes
		$pdo = new \PDO(DB_Str, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		
		if ($pdo != null) {		
			
			$consultaSQL = "SELECT COUNT(*) AS cantidad FROM t_prestamo_detalle
							INNER JOIN t_prestamo 
							ON t_prestamo_detalle.prestamo_Id = t_prestamo.prestamo_Id
							WHERE prestamo_codigo_presupuestario = '$codigo' and
							prestamo_detalle_devuelto = 0";

			$sql = $pdo->query($consultaSQL);

			$rs = [];
			while ($row = $sql->fetch(\PDO::FETCH_ASSOC)) {
					$rs[] = [						
						'cantidad' => $row['cantidad']																		
					];
			}
            return $rs;
		}	
		$pdo = null;
	}

	function conVistaDevolucion($codigo){

		// el array es para que el JSON funcione con tíldes
		$pdo = new \PDO(DB_Str, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		
		if ($pdo != null) {		
			
			$consultaSQL = "SELECT
							t_prestamo.prestamo_Id, 
							prestamo_fechaRetiro,
							prestamo_detalle_fechaDevolucion,
							prestamo_detalle_id_activo,
							prestamo_nombre_solicitante,
							alias_imagen,alias							
							FROM
								t_prestamo
							INNER JOIN
								t_prestamo_detalle ON t_prestamo_detalle.prestamo_Id = t_prestamo.prestamo_Id
							INNER JOIN     
								t_placa ON t_placa.id_activo = t_prestamo_detalle.prestamo_detalle_id_activo
							INNER JOIN
								t_alias ON t_alias.alias_id  = t_placa.alias_id
							WHERE
								prestamo_detalle_devuelto = 0 and prestamo_codigo_presupuestario = $codigo
							ORDER BY t_prestamo.prestamo_fecha DESC;";

				$sql = $pdo->query($consultaSQL);

			$rs = [];
			while ($row = $sql->fetch(\PDO::FETCH_ASSOC)) {
					$rs[] = [
						'prestamo_Id' => $row['prestamo_Id'],						
						'prestamo_fechaRetiro' => $row['prestamo_fechaRetiro'],
						'prestamo_detalle_fechaDevolucion' => $row['prestamo_detalle_fechaDevolucion'],
						'prestamo_nombre_solicitante' => $row['prestamo_nombre_solicitante'],
						'alias_imagen' => $row['alias_imagen'],
						'alias' => $row['alias']
					];
			}
            return $rs;
		}	
		$pdo = null;
	}

	function conVistaDevolucionEncabezado($codigo){

		// el array es para que el JSON funcione con tíldes
		$pdo = new \PDO(DB_Str, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		
		if ($pdo != null) {		
			
			$consultaSQL = "SELECT
							t_prestamo.prestamo_Id, 
							prestamo_fechaRetiro, prestamo_horaRetiro,
							prestamo_fechaDevolucion, prestamo_horaDevolucion,						
							prestamo_nombre_solicitante											
							FROM
								t_prestamo
							INNER JOIN
								t_prestamo_detalle ON t_prestamo_detalle.prestamo_Id = t_prestamo.prestamo_Id
							WHERE
								prestamo_detalle_devuelto = 0 and prestamo_codigo_presupuestario = '$codigo'
							ORDER BY t_prestamo.prestamo_fecha DESC;";

				$sql = $pdo->query($consultaSQL);

			$rs = [];
			while ($row = $sql->fetch(\PDO::FETCH_ASSOC)) {
					$rs[] = [
						'prestamo_Id' => $row['prestamo_Id'],						
						'prestamo_fechaRetiro' => $row['prestamo_fechaRetiro'],
						'prestamo_horaRetiro' => $row['prestamo_horaRetiro'],
						'prestamo_fechaDevolucion' => $row['prestamo_fechaDevolucion'],
						'prestamo_horaDevolucion' => $row['prestamo_horaDevolucion'],
						'prestamo_nombre_solicitante' => $row['prestamo_nombre_solicitante']
					];
			}
            return $rs;
		}	
		$pdo = null;
	}

	function conVistaDevolucionDetalle($prestamo_Id, $codigo) {

		// el array es para que el JSON funcione con tíldes
		$pdo = new \PDO(DB_Str, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	
		if ($pdo != null) {		
			
			$consultaSQL = "SELECT alias_imagen, alias, modelo, marca  
							FROM t_activo						
							INNER JOIN t_marca 
								ON t_activo.id_marca = t_marca.id_marca
							INNER JOIN t_color
								ON t_color.id_color = t_activo.id_color
							INNER JOIN t_placa
								ON t_placa.id_activo = t_activo.id_activo and t_placa.codigo = '$codigo'  
							INNER JOIN t_prestamo_detalle
								ON t_prestamo_detalle.prestamo_detalle_id_activo = t_placa.id_activo
							INNER JOIN t_alias
								ON t_alias.alias_id  = t_placa.alias_id  and t_alias.codigo = '$codigo' 
							WHERE t_prestamo_detalle.prestamo_Id = $prestamo_Id";

				$sql = $pdo->query($consultaSQL);

			$rs = [];
			while ($row = $sql->fetch(\PDO::FETCH_ASSOC)) {
					$rs[] = [
						'alias_imagen' => $row['alias_imagen'],						
						'alias' => $row['alias'],
						'modelo' => $row['modelo'],
						'marca' => $row['marca']
					];
			}
            return $rs;
		}	
		$pdo = null;
	}

	function conDisponible($codigo, $alias_id) {

		// el array es para que el JSON funcione con tíldes
		$pdo = new \PDO(DB_Str, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	
		if ($pdo != null) {		
			
			$consultaSQL = "SELECT count(*) AS disponible FROM t_placa WHERE codigo = '$codigo' and prestar = 1
							and alias_id = $alias_id";

				$sql = $pdo->query($consultaSQL);

			$rs = [];
			while ($row = $sql->fetch(\PDO::FETCH_ASSOC)) {
					$rs[] = [
						'disponible' => $row['disponible']
							];
			}
            return $rs;
		}	
		$pdo = null;

	}


}




?>
