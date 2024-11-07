const URL = "http://localhost:8000/api/";

$(document).ready(function () {

    $.ajax({
        url: URL + "motivos",
        dataTyo: "json",
        success: function (data) {
            $.each(data, function (key, value) {
                $('#motivotipo_id').append('<option value="' + value.id + '">' + value.descripcion + '</option>');
            });
        },
        error: function (data) {
            console.log(data);
        }
    })

    var table = $('#tbl_credito').DataTable({
        ajax: {
            url: URL + "notas-credito-recibo",
            dataSrc: ""
        },
        columns: [
            { data: "nom" },
            { data: "med" },
            { data: "serie" },
            { data: "correlativo" },
            { data: "fec" },
            {
                data: null,
                render: function (data) {
                    return `
                        <a href="#" id="show_button" class="btn btn-info btn-sm" data-toggle="modal" data-target=".nc"><i class="fas fa-eye"></i></a>
                        <a href="#" id="print_button" class="btn btn-success btn-sm"><i class="fas fa-print"></i></a>
                    `;
                }
            }
        ],
        language: {
            "lengthMenu": "Mostrar _MENU_ registros",
            "zeroRecords": "No se encontraron resultados",
            "info": "Registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "infoEmpty": "Registros del 0 al 0 de un total de 0 registros",
            "infoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sSearch": "Buscar:",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "Último",
                "sNext": "Siguiente",
                "sPrevious": "Anterior"
            },
            "sProcessing": "Procesando...",
        },
        responsive: "true",
        dom: 'Bfrtilp',
        buttons: [{
            extend: 'excelHtml5',
            text: '<img src="_images/excel.png" height="18" width="18" alt="icon name"> ',
            titleAttr: 'Clic para Exportar a Excel',
            className: 'btn-excel'
        }],
        "bLengthChange": false,
        // "bFilter": false,
        "bInfo": false,
    });

    $('#tbl_credito tbody').on('click', '#show_button', function () {
        var rowData = table.row($(this).closest('tr')).data();
        $.ajax({
            url: URL + "notas-credito-recibo/" + rowData.id,
            type: "GET",
            dataType: "json",
            success: function (data) {
                if (data.length != 1) {
                    data = data[0];
                }
                data = data[0];
                $('#comprobantetipo_id option[value="' + data.comprobantetipo_id + '"]').attr('selected', 'selected');
                $('#ver_documento_credito [name="serie"]').val(data.serie);
                $('#ver_documento_credito [name="correlativo"]').val(data.correlativo);
                $('#motivotipo_id option[value="' + data.motivo_id + '"]').attr('selected', 'selected');
                $('#documentotipo_id option[value="' + data.tip + '"]').attr('selected', 'selected');
                $('#ver_documento_credito [name="nombre"]').val(data.nom);
                $('#ver_documento_credito [name="direccion"]').val(data.direccionfiscal);
                $('#ver_documento_credito [name="correo"]').val(data.correo_electronico);
                $('#ver_documento_credito [name="observacion"]').val(data.observacion);

            }
        })

    });

    // Filters
    $('#tbl_credito tbody').on('click', '#print_button', function () {
        var rowData = table.row($(this).closest('tr')).data();
        window.location.href = "/factu_mifact_impresion.php?=tip" + rowData.tip + "&id=" + rowData.id;
    });

    $('#min,#max').on('change', function () {
        table.columns(4).search(this.value).draw();
    });
    
    $('#paciente').on('keyup', function () {
        table.columns(0).search($(this).val()).draw();
    })
 
    $('#medico').on('keyup', function () {
        table.columns(1).search($(this).val()).draw();
    })
 
    $('#serie').on('keyup', function () {
        table.columns(2).search($(this).val()).draw();
    })
    $('#filtro_correlativo').on('keyup', function () {
        table.columns(3).search($(this).val()).draw();
    })
});