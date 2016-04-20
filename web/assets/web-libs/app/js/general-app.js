function JQueryApplication(host){
	this.host = host;
	this.restapi = this.host+"restjson/modules/";
}

JQueryApplication.prototype = {
	init: function(){}
};

/*
var jqueryapp = (function(h){
		return new JQueryApplication(h);
})("http://captaincode-hosting.no-ip.info/wavelearning/web/index.php/");
*/

var jqueryapp = (function(h){
		return new JQueryApplication(h);
})("http://captaincode-hosting.no-ip.info/wavelearning/web/index.php/");

jqueryapp.init = function(){
	$("#frmlogin button").click(function(){
		$(".alerts .alert").remove();
		$.post(jqueryapp.restapi+"login",
			$("#frmlogin").serialize(),
			function(jr){
				if(jr.acepted){
					$("<div class=\"alert alert-success\">Los datos introducidos son correctos, redireccionando.</div>").prependTo(".alerts");
					setTimeout(function(){
						location.href=jqueryapp.host+"cpanel";
					}, 1500);
				}
		})
			.fail(function(){
				$("<div class=\"alert alert-danger\">Los datos introducidos son incorrectos.</div>").prependTo(".alerts");
			});
	});

	$("#frmlogin").submit(function(e){
		e.preventDefault();
	});

	$("#frmsignup").submit(function(e){
		e.preventDefault();
	});

	$("#frmsignup button").click(function(){
		$(".alerts .alert").remove();

		if($("#frmsignup input[name='password']").val() !== $("#frmsignup input[name='password-ver']").val())
			$("<div class=\"alert alert-danger\">Las contraseñas no coinciden, porfavor introducelas nuevamente.</div>").prependTo(".alerts");
		else{
		$.post(jqueryapp.restapi+"signup",
			$("#frmsignup").serialize(),
			function(jr){
				if(jr.acepted){
					$("<div class=\"alert alert-success\">El usuario ha sido creado exitosamente, redireccionando.</div>").prependTo(".alerts");
					setTimeout(function(){
						location.href=jqueryapp.host+"login";
					}, 1500);
				}
			})
			.fail(function(){
				$("<div class=\"alert alert-danger\">Los datos introducidos son incorrectos, porfavor introdúscalos nuevamente.</div>").prependTo(".alerts");
			});
		}
	});
};

$(document).ready(jqueryapp.init);