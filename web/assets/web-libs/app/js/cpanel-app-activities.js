function JQueryApplication(host) {
    this.host = host;
    this.restapi = this.host + "restjson/modules/";
    this.restoken = this.host + "restjson/token/";
}

JQueryApplication.prototype = {
    init: function() {},
    linkDinamicHandlers: function() {}
};

/*
var jqueryapp = (function(h){
		return new JQueryApplication(h);
})("http://captaincode-hosting.no-ip.info/wavelearning/web/index.php/");
*/

var jqueryapp = (function(h) {
    return new JQueryApplication(h);
})("http://captaincode-hosting.no-ip.info/wavelearning/web/index.php/");

jqueryapp.init = function() {
    var action = 0,
        cid, ctoken, aid, name, date, desc;

    jqueryapp.linkDinamicHandlers = function() {
        $(".table-activities tbody tr").on("click", "td #action-edit", function() {
            var row = $(this).parent().parent();
            $(".table-activities tbody tr").removeClass("row-active");
            $(".table-activities tbody tr #action-del").removeClass("option-active");
            $(".table-activities tbody tr #action-edit").removeClass("option-active");
            $(row).addClass("row-active");
            $(this).addClass("option-active");

            //module code
            row = $(row).children();
            aid = $($(row)[0]).text();
            name = $($(row)[1]).text();
            date = $($(row)[2]).text();
            desc = $($(row)[3]).text();

            $(".cpanel-form input[name='name']").val(name);
            $(".cpanel-form input[name='adate']").val(date);
            $(".cpanel-form textarea[name='description']").text(desc);
            $(".cpanel-form .btn-action").text("Editar");
            action = 1;
        });

        $(".table-activities tbody tr").on("click", "td #action-del",function() {
            var row = $(this).parent().parent();
            $(".table-activities tbody tr").removeClass("row-active");
            $(".table-activities tbody tr #action-del").removeClass("option-active");
            $(".table-activities tbody tr #action-edit").removeClass("option-active");
            $(row).addClass("row-active");
            $(this).addClass("option-active");

            //module options
            row = $(row).children();
            aid = $($(row)[0]).text();

            $.getJSON(jqueryapp.restoken + "del-activity/" + ctoken + "/" + aid + "/"+ cid, {}, function(jr) {
                if (jr.acepted) {
                    console.log(jr.acepted);
                    alert("La actividad fue eliminada con éxito, redireccionando...");
                    setTimeout(function() {
                        location.href = jqueryapp.host + "cpanel/courses";
                    }, 1500);
                }
            }).fail(function() {
                alert("El actividad no fue eliminada, porfavor reintente.");
            });
        });
    };

    $("#datepicker").datepicker({
        dateFormat: "yy-mm-dd"
    });

    $(".cpanel-form").submit(function(e) {
        e.preventDefault();
    });

    $(".courses-table tbody tr #action-list").click(function() {
        var row = $(this).parent().parent();
        $(".tblcontent tbody tr").removeClass("row-active");
        $(".tblcontent tbody tr #action-list").removeClass("option-active");
        $(".tblcontent tbody tr #action-add").removeClass("option-active");
        $(".tblcontent tbody tr #action-edit").removeClass("option-active");
        $(".tblcontent tbody tr #action-del").removeClass("option-active");
        $(row).addClass("row-active");
        $(this).addClass("option-active");

        //module code
        row = $(row).children();
        cid = $($(row)[0]).text();
        ctoken = $($(row)[2]).text();

        $(".table-activities .course-title").text("Actividades de " + $($(row)[1]).text());

        $.getJSON(jqueryapp.restoken + "activities/" + ctoken + "/" + cid, {},
            function(jres) {
                $(".table-activities .table-body tr").remove();
                $(".table-activities").attr("style", "display:block");
                $.each(jres, function(key, value) {
                    var tt = value;
                    $(
                        "<tr>\
							<td>" + tt.id + "</td>\
							<td>" + tt.name + "</td>\
							<td>" + tt.adate + "</td>\
							<td class='no-visible'>" + tt.description + "</td>\
							<td>\
								<span id='action-edit'>Editar <span class='glyphicon glyphicon-pencil'></span></span>\
								| <span id='action-del'>Eliminar <span class='glyphicon glyphicon-remove'></span></span>\
							</td>\
						</tr>"
                    ).appendTo(".table-activities .table-body");
                    jqueryapp.linkDinamicHandlers();
                });
            }).fail(function() {
            $("<div class='alert alert-danger'>No se pudo obtener la lista de actividades, favor de asignar por lo menos una actividad.</div>").appendTo(".courses-table .alerts");
        });
    });

    $(".courses-table tbody tr #action-add").click(function() {
        var row = $(this).parent().parent();
        $(".tblcontent tbody tr").removeClass("row-active");
        $(".tblcontent tbody tr #action-list").removeClass("option-active");
        $(".tblcontent tbody tr #action-add").removeClass("option-active");
        $(".table-activities .table-body tr").remove();
        $(".table-activities").attr("style", "display:none");
        $(row).addClass("row-active");
        $(this).addClass("option-active");
        $(".alerts .alert").remove();
        row = $(row).children();
        cid = $($(row)[0]).text();
        ctoken = $($(row)[2]).text();
        action = 0;
    });

    $(".cpanel-form .btn-clear").click(function() {
        //write clear function
        $(".cpanel-form input[name='name']").val("");
        $(".cpanel-form input[name='adate']").val("");
        $(".cpanel-form textarea[name='description']").text("");
        $(".cpanel-form .btn-action").text("Agregar");
        $(".tblcontent tbody tr").removeClass("row-active");
        $(".tblcontent tbody tr #action-list").removeClass("option-active");
        $(".tblcontent tbody tr #action-add").removeClass("option-active");
        $(".table-activities .table-body tr").remove();
        $(".table-activities").attr("style", "display:none");
        $(".alerts .alert").remove();
        action = 0;
    });

    $(".cpanel-form .btn-action").click(function() {
        $(".alerts .alert").remove();
        switch (action) {
            case 0:
                {
                    //operation mutex
                    console.log("Application debugger: current action -> add activity");
                    $.post(
                        jqueryapp.restoken + "add-activity", (function(p) {
                            return p + "&cid=" + cid;
                        })($("form").serialize()),
                        function(jres) {
                            if (jres.acepted) {
                                console.log(jres.acepted);
                                $("<div class=\"alert alert-success\">La actividad fue exitosamente agregada, redireccionando...</div>").appendTo("form .alerts");
                                setTimeout(function() {
                                    location.href = jqueryapp.host + "cpanel/activities";
                                }, 1500);
                            }
                        }
                    ).fail(function() {
                        $("<div class=\"alert alert-danger\">No se pudo agregar la actividad, porfavor verifique los campos.</div>").appendTo("form .alerts");
                    });
                }
                break;
            case 1:
                {
                    console.log("Application debugger: current action -> edit activity");
                    $.post(
                        jqueryapp.restoken + "edit-activity", (function(form) {
                            var serialized_fields = form.serialize();
                            serialized_fields += "&ctoken=" + ctoken + "&cid=" + cid + "&aid=" + aid;
                            return serialized_fields;
                        })($("form")),
                        function(jres) {
                            if (jres.acepted) {
                                console.log(jres.acepted);
                                $("<div class=\"alert alert-success\">La actividad fue exitosamente editada, redireccionando...</div>").appendTo("form .alerts");
                                setTimeout(function() {
                                    location.href = jqueryapp.host + "cpanel/activities";
                                }, 1500);
                            }
                        }
                    ).fail(function() {
                        $("<div class=\"alert alert-danger\">No se editó la actividad, porfavor verifique los campos.</div>").appendTo("form .alerts");
                    });
                }
                break;
            default:
                console.log("Action not found");
                break
        }


    });
};

$(document).ready(jqueryapp.init);