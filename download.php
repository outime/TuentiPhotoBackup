<?php

/*************************************************
* -> TuentiPhotoBackup                           *
* -> by Rubén Díaz <outime@gmail.com>            *
* -> https://github.com/outime/TuentiPhotoBackup *
*************************************************/

set_time_limit(0);

extract($_GET);

function get_string_between($string, $start, $end)
{
	$string = " ". $string;
	$ini = strpos($string,$start);
	if ($ini == 0) return "";
	$ini += strlen($start);
	$len = strpos($string, $end, $ini) - $ini;
	return substr($string, $ini, $len);
}

function launch_curl($uri, $post = false, $galleta = false)
{
	$ch = curl_init($uri);
	curl_setopt( $ch, CURLOPT_HEADER, true );
	curl_setopt( $ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:12.0) Gecko/20100101 Firefox/12.0" );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	if ($post)
	{
		curl_setopt( $ch, CURLOPT_POST, true );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $post );
	}
	if ($galleta) curl_setopt( $ch, CURLOPT_COOKIE, $galleta );
	$content = curl_exec($ch);
	curl_close($ch);
	
	return $content;
}

if ($step == 1) // identificación
{
	$status = array();
	
	// 1. Cogemos csfr y cookie inicial
	$content = launch_curl('http://m.tuenti.com/?m=Login');
	
	preg_match_all( '#Set-Cookie:(.*?);#', $content, $galleta );
	$galleta = trim(implode(';',$galleta[1])); // pid(alfanumerico); cookiename=1
	$csfr = get_string_between($content, 'name="csrf" value="', '"/>'); // alfanum (depende de pid)
	
	// 2. Nos logueamos
	$postargs = 'csrf='.$csfr.'&tuentiemailaddress='.urlencode($email).'&password='.urlencode($password).'&remember=1';
	$content = launch_curl('http://m.tuenti.com/?m=Login&f=process_login', $postargs, $galleta.'; cookiename=1');
	
	preg_match_all( '#Set-Cookie:(.*?);#', $content, $galleta );
	$galleta = trim(implode(';',$galleta[1]));
	
	if (strpos($galleta, 'mid=')) 
	{
		$galleta = $galleta . '; screen=1920-1080-1920-1040-1-20.74'; // para obtener fotos en 'alta' resolución
		$status['galleta'] = $galleta;
		$status['csfr'] = $csfr;
	}
	else
	{
		$status['galleta'] = null;
	}
	
	echo json_encode($status);
} // fin identificación

if ($step == 2) // recogida de fotos
{
	$status = array();
	
	// 1. Propio perfil para obtener url de fotos etiquetadas
	$content = launch_curl('http://m.tuenti.com/?m=Profile&func=my_profile', false, $galleta);
	file_put_contents('asd.txt', $content);
	$tagged_uri = get_string_between($content, '<div class="h">Photos</div><a id="photos"></a><div class="item"><div> <small> <a href="', '">');
	$tagged_uri = html_entity_decode('http://m.tuenti.com/'.$tagged_uri);

	// 2. Accedemos al album de fotos etiquetadas
	$content = launch_curl(urldecode($tagged_uri), false, $galleta);
	$first_pic = get_string_between($content, '</h1><div class="item"><a class="thumb" href="', '">');
	$first_pic = html_entity_decode('http://m.tuenti.com/'.$first_pic);
	
	// 3. Vamos a la primera imagen
	$content = launch_curl($first_pic, false, $galleta);
	$qty = get_string_between($content, '(1 of ', ')'); // cantidad de fotos en album
	
	$next = html_entity_decode('http://m.tuenti.com/'.get_string_between($content, ') <a href="', '">Next')); // enlace siguiente foto
	
	$first_pic_download = get_string_between($content, '"thumb fullSize"><img src="', '"'); // uri primera foto
	
	mkdir('./downloads/'.$_GET['email']);
	file_put_contents('./downloads/'.$_GET['email'].'/1.jpg', file_get_contents($first_pic_download)); // descarga efectiva de la primera
	
	for ($i=2; $i<=$qty; $i++)
	{
		$content = launch_curl($next, false, $galleta);
		
		$next = html_entity_decode('http://m.tuenti.com/'.get_string_between($content, ') <a href="', '">Next')); 
		$download_uri = get_string_between($content, '"thumb fullSize"><img src="', '"');
		
		file_put_contents('./downloads/'.$email.'/'.$i.'.jpg', file_get_contents($download_uri));
		file_put_contents('status.php', ($i/($qty/100))); // para la cutrebarra de progreso
		
		sleep(0.7); // no vaya a ser que por flood...
	}
	
	echo json_encode(array('status' => 'ok'));
} // fin recogida

?>