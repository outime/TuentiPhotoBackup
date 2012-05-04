<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>Tuenti Photo Backup</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="static/css/bootstrap.min.css" rel="stylesheet">
<style type="text/css">body { margin-top:20px; }</style>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>
<script src="static/js/general.min.js" type="text/javascript"></script>
</head>
<body>
<div class="container">
  <div class="row">
    <div class="span2">
      <input class="span2" type="email" placeholder="e-mail" id="email" required />
	  <input class="span2" type="password" placeholder="contraseña" id="password" required />
	  <button type="submit" class="btn btn-primary" id="recuperar" onclick="recuperar()">recuperar fotos »</button>
	  <br /><br />
    </div>
    <div class="span10">
      <div class="well" id="estado"><h3>Estado</h3><dl class="dl-horizontal"></div>
	  <div class="well" id="datos"><h3>Datos</h3><dl class="dl-horizontal"></div>
	  <a href="https://github.com/outime/TuentiPhotoBackup">:-)</a>
    </div>
  </div>
</div>

</body>
</html>