$( document ).ready(function() {
    var idinforme="";
    var origen="";
    var modalConfirm = function(callback) {
        $(".btn_eliminar_informe").on("click", function(){
            $("#eliminar_informe").modal('show');
            idinforme = $(this).attr("data-informe")
            origen = $(this).attr("data-origen")
        });
        $("#modal-btn-si").on("click", function(){
            callback(true, idinforme, origen);
            $("#eliminar_informe").modal('hide');
        });
        $("#modal-btn-no").on("click", function(){
            callback(false, idinforme, origen);
            $("#eliminar_informe").modal('hide');
        });
    };

    modalConfirm(function(confirm, idinforme, origen) {
        if ( ! confirm ) return;

        $.post("_operaciones/eliminar_"+origen+".php", {idinforme: idinforme}, function (data) {
            location.reload();
        });
    });
});