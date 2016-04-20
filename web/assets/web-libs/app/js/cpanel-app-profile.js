function JQueryApplication(host) {
    this.host = host;
    this.restapi = this.host + "restjson/modules/";
    this.restoken = this.host + "restjson/token/";
}

JQueryApplication.prototype = {
    init: function() {}
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

    $(".cpanel-form").submit(function(e) {
        e.preventDefault();
    });

    $(".cpanel-form .btn-clear").click(function() {
        //write clear function
        $(".cpanel-form input[name='email']").val("");
        $(".cpanel-form input[name='password']").val("");
        $(".cpanel-form input[name='password-ver']").val("");
        $(".cpanel-form textarea[name='desc']").text("");
        $(".alerts .alert").remove();
    });

    $(".cpanel-form .btn-action").click(function() {
        $(".alerts .alert").remove();
        $.post(
            jqueryapp.restoken + "edit-profile", $("form").serialize(),
            function(jres) {
                if (jres.acepted) {
                    console.log(jres.acepted);
                    $("<div class=\"alert alert-success\">Su perfil fue exitosamente editado, redireccionando...</div>").appendTo("form .alerts");
                    setTimeout(function() {
                        location.href = jqueryapp.host + "cpanel/profile";
                    }, 1500);
                }
            }
        ).fail(function() {
            $("<div class=\"alert alert-danger\">Su perfil no se pudo ediar, porfavor verifique los campos.</div>").appendTo("form .alerts");
        });
    });
};

$(document).ready(jqueryapp.init);