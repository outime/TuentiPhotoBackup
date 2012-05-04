function recuperar() {
  var email = $('#email').val();
  var password = $('#password').val();
	
  $('#recuperar').attr('disabled', '');
	
  $.getJSON('download.php',{step:1,email:email,password:password},function(json) {
  
    $('#estado').append('<dt>Comprobando credenciales...</dt>');
	
      if (json.galleta)
      {
        galleta = json.galleta;
        csfr = json.csfr;
        $('#estado').append('<dd><span class="label label-success">OK</span></dd>');
        $('#datos').append('<dt>Base</dt> <dd><span class="label">Cookie</span> '+galleta+' <br/> <span class="label">csfr</span> '+csfr+'</dd>');
	  
        var timerID = setInterval('checkStatus()', 2000);
        $('#datos').append('<dt>Progreso</dt> <dd><div class="progress"><div class="bar" style="width: 0%;"></div></div></dd>');
        $('#estado').append('<dt>Descargando fotos...</dt>');
	  
        $.getJSON('download.php',{step:2,galleta:galleta,csfr:csfr,email:email},function(json) {
          if (json.status == 'ok') $('#estado').append('<dd><span class="label label-success">OK</span></dd>'); 
          $('.bar').attr('style', 'width: 100%');
          clearInterval(timerID);
	  })
        }
        else
        {
          $('#estado').append('<dd><span class="label label-important">NOPE</span></dd>');
          $('#recuperar').removeAttr('disabled');
        }
  })
}

function checkStatus()
{
  $.get('status.php', function(data) {
    $('.bar').attr('style', 'width: '+data+'%');
  });
}