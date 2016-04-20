function JQueryApplication(host){
	this.host = host;
	this.restapi = this.host+"restjson/modules/";
	this.restoken = this.host+"restjson/token/";
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
	var action = 0, id, email, pass, about;

	$(".tblcontent tbody tr #action-edit").click(function(){
		var row = $(this).parent().parent();
		$(".tblcontent tbody tr").removeClass("row-active");
		$(".tblcontent tbody tr #action-del").removeClass("option-active");
		$(".tblcontent tbody tr #action-edit").removeClass("option-active");
		$(row).addClass("row-active");
		$(this).addClass("option-active");

		//module code
		row =  $(row).children();
		id = $($(row)[0]).text();
		email = $($(row)[1]).text();
		pass = $($(row)[2]).text();
		about = $($(row)[3]).text();

		$(".cpanel-form input[name='email']").val(email);
		$(".cpanel-form textarea[name='desc']").text(about);
		$(".cpanel-form .btn-action").text("Editar");
		action = 1;
	});

	$(".tblcontent tbody tr #action-del").click(function(){
		var row = $(this).parent().parent();
		$(".tblcontent tbody tr").removeClass("row-active");
		$(".tblcontent tbody tr #action-del").removeClass("option-active");
		$(".tblcontent tbody tr #action-edit").removeClass("option-active");
		$(row).addClass("row-active");
		$(this).addClass("option-active");

		//module options
		row = $(row).children();
		id = $($(row)[0]).text();
		pass = $($(row)[2]).text();

		$.getJSON(jqueryapp.restoken+"del-student/"+pass+"/"+id,{},function(jr){
			if(jr.acepted){
				console.log(jr.acepted);
				alert("El alumno fue eliminado con éxito, redireccionando...");
				setTimeout(function(){
					location.href = jqueryapp.host+"cpanel/students";
				}, 1500);
			}
		}).fail(function(){
			alert("El alumno no fue eliminado, porfavor reintente.");
		});
	});

	$(".cpanel-form").submit(function(e){
		e.preventDefault();
	});

	$(".cpanel-form .btn-clear").click(function(){
		//write clear function
		$(".cpanel-form input[name='email']").val("");
		$(".cpanel-form input[name='password']").val("");
		$(".cpanel-form input[name='password-ver']").val("");
		$(".cpanel-form textarea[name='desc']").text("");
		$(".cpanel-form .btn-action").text("Agregar");
		$(".tblcontent tbody tr").removeClass("row-active");
		$(".tblcontent tbody tr #action-del").removeClass("option-active");
		$(".tblcontent tbody tr #action-edit").removeClass("option-active");
		$(".alerts .alert").remove();
		action = 0;
	});

	$(".cpanel-form .btn-action").click(function(){
		$(".alerts .alert").remove();
		switch(action){
			case 0:{
				//operation mutex
				console.log("Application debugger: current action -> add student");
				$.post(
					jqueryapp.restapi+"add-student",
					$("form").serialize(),
					function(jres){
						if(jres.acepted){
							console.log(jres.acepted);
							$("<div class=\"alert alert-success\">El alumno fue exitosamente agregado, redireccionando...</div>").appendTo("form .alerts");
							setTimeout(function(){
								location.href = jqueryapp.host+"cpanel/students";
							}, 1500);
						}
					}
				).fail(function(){
					$("<div class=\"alert alert-danger\">No se pudo agregar el curso, porfavor verifique los campos.</div>").appendTo("form .alerts");
				});
			}
			break;
			case 1:{
				console.log("Application debugger: current action -> edit student");
				$.post(
					jqueryapp.restoken+"edit-student", 
					(function(form){
						var serialized_fields = form.serialize();
						serialized_fields += "&token="+pass+"&id="+id;
						return serialized_fields;
					})($("form")),
					function(jres){
						if(jres.acepted){
							console.log(jres.acepted);
							$("<div class=\"alert alert-success\">El alumno fue exitosamente editado, redireccionando...</div>").appendTo("form .alerts");
							setTimeout(function(){
								location.href = jqueryapp.host+"cpanel/students";
							}, 1500);
						}
					}
				).fail(function(){
					$("<div class=\"alert alert-danger\">No se editó el alumno, porfavor verifique los campos.</div>").appendTo("form .alerts");
				});
			}
			break;
			default:
				console.log("Action not found");
			break
		}
	})
};

$(document).ready(jqueryapp.init);