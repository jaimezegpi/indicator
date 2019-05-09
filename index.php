<?php

/**
 * [updateIndicator Obtiene el indicador deseado desde la fuente ya sea online o archivo]
 * @param  [string] $indicator [nombre del indicador, uf, utm,ipc,dolar]
 * @return [float]            [valor]
 */
function updateIndicator( $indicator ){
	$nombre_archivo = 'indicator_'.$indicator.'.txt';
	$valor_respaldo = "";
	if (file_exists($nombre_archivo)) {
		$file_date =  date ("Ymd", filemtime($nombre_archivo) );
		//echo $file_date." - ".date("Ymd")." <br>";
		$valor = file_get_contents($nombre_archivo);
		if ( $file_date==date("Ymd") ){
			return $valor;
		}else{
			$valor_respaldo = $valor;
		}
	}

	$apiUrl = 'http://mindicador.cl/api';
	$json = "";
	if ( ini_get('allow_url_fopen') ) {
		$json = file_get_contents($apiUrl);
	} else {
		$curl = curl_init($apiUrl);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$json = curl_exec($curl);
		curl_close($curl);
	}
	if (!$json){
		if ($valor_respaldo){
			return $valor_respaldo;
		}else{
			return false;
		}
	}else{
		$dailyIndicators = json_decode($json);
	}
	$data = $dailyIndicators->$indicator->valor;
	if( isset($data) ){
		if ($data){
			file_put_contents($nombre_archivo, $data);
			return $data;
		}else{
			if ($valor_respaldo){
				return $valor_respaldo;
			}else{
				return false;
			}
		}

	}else{
		if ($valor_respaldo){
			return $valor_respaldo;
		}else{
			return false;
		}
	}
}

/**
 * [getIndicator Obtiene el indicador deseado]
 * @return [print] [Imprime en pantalla el valor obtenido o nada si no existe]
 */
function getIndicator(){
	if ( isset($_GET["indicator"]) ){
		$arr = array("uf","ipc","dolar","uf","utm");
		if ( in_array($_GET["indicator"], $arr) ){
			echo updateIndicator( $_GET["indicator"] );
		}else{

		}
	}
}

getIndicator();