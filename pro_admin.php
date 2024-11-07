
<!DOCTYPE HTML>
<html>
<head>
    <title>Inmater Clínica de Fertilidad | Administración de procedimientos</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="_themes/tema_inmater.min.css" />
    <link rel="stylesheet" href="_themes/jquery.mobile.icons.min.css" />
    <link rel="stylesheet" href="css/jquery.mobile.structure-1.4.5.min.css" />
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/jquery.mobile-1.4.5.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .ui-dialog-contain {
            max-width: 1000px;
            margin: 1% auto 1%;
            padding: 0;
            position: relative;
            top: -35px;
        }
        .color { color:#F4062B !important; }
        .mayuscula {
            text-transform: uppercase; font-size:small;
        }
        .pagination {
            display: flex;
            justify-content: center;
            list-style: none;
            padding: 0;
        }
        .pagination li {
            margin: 0 5px;
        }
        .pagination li.active a {
            background-color: cornflowerblue;
        }
        .pagination li a {
            text-decoration: none;
            padding: 8px 16px;
            background: #eee;
            color: #333;
            border-radius: 4px;
        }
        .pagination li a.active {
            background: #333;
            color: #fff;
        }
        .pagination li a.disabled {
            pointer-events: none;
            background: #ddd;
        }
        .pagination ul {
            width: 100%;
            display: contents;
            list-style: none;
        }
        .search-button {
            flex-basis: content;
            display: inline-flex;
            align-items: center;
            padding: 5px 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #fff;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .search-button:hover {
            background-color: #f1f1f1;
        }
        .x-button {
            display: inline-flex;
            align-items: center;
            padding: 5px 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f1f1f1;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .x-button:hover {
            background-color: #ddd;
        }
        .search-button i, .x-button i {
            font-size: 16px;
        }
        .search-button i.fas.fa-search, .x-button i.fas.fa-times {
            color: #333;
        }
        .ui-input-text{
            width: 100%;
            margin: 0 0 0 5px;
        }
    </style>
</head>
<body>
<div data-role="page" class="ui-responsive-panel" id="pro_admin" data-dialog="true">
    <div data-role="header" data-position="fixed">
        <a href="r_pro.php" rel="external" class="ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-left ui-icon-delete">Cerrar</a>
        <h2>Administración de Protocolos</h2>
    </div><!-- /header -->

    <div class="ui-content" role="main">
        <form action="ajax/pro_admin_ajax.php" method="post" data-ajax="false" name="form2">
            <input type="hidden" name="accion">
            <input type="hidden" name="pro">
            <input type="hidden" name="pro2">
            <div style="display: flex; align-items: center; margin-bottom: 10px;">
                <button type="button" id="search-button" class="search-button"><i class="fas fa-search"></i></button>
                <input id="filtro" type="text" placeholder="Ingrese al menos 3 caracteres y presione Enter para buscar" style="flex-grow: 1; margin-left: 10px;">
            </div>
            <table class="table-stripe ui-responsive mayuscula ui-table ui-table-reflow">
                <thead>
                    <tr>
                        <th align="center">Protocolo</th>
                        <th align="center">Paciente</th>
                        <th align="center">Renombrar por</th>
                        <th align="center">Eliminar</th>
                    </tr>
                </thead>
                <tbody id="table-body">
                    <!-- Los registros se cargarán aquí mediante AJAX -->
                </tbody>
            </table>
        </form>
        <br><br>
        <!-- Paginador -->
        <div class="pagination">
            <ul id="pagination">
                <!-- Los enlaces de paginación se cargarán aquí mediante AJAX -->
            </ul>
        </div>
    </div><!-- /content -->
</div><!-- /page -->

<script>
    let currentRequest = null;

const showLoader = () => {
    Swal.fire({
        title: 'Cargando...',
        text: 'Por favor, espere mientras se cargan los resultados.',
        didOpen: () => {
            Swal.showLoading();
        }
    });
};

const hideLoader = () => {
    Swal.close();
};

const loadTable = (search = '', page = 1, datos = null) => {
    if (currentRequest) {
        currentRequest.abort();
    }

    datos = datos == null ? {
                                search: search,
                                page: page
                            } : datos;
        
    showLoader();

    currentRequest = $.ajax({
        url: 'ajax/pro_admin_ajax.php',
        type: 'GET',
        dataType: 'json',
        data: datos,
        success: function(data) {
            const $tableBody = $('#table-body');
            $tableBody.empty();
            $.each(data.rows, function(index, paci) {
                let eliminar = `<a href="#" onclick="borra('${paci.pro}','${paci.id_hc_reprod}','${paci.dni}')">Eliminar</a>`
                if (paci.eliminar == 1) {
                    eliminar = ``
                }
                $tableBody.append(`
                    <tr>
                        <td><a href="le_aspi${paci.dias}.php?id=${paci.pro}" rel="external">${paci.tip}-${paci.pro}-${paci.vec}</a></td>
                        <td>${paci.ape} ${paci.nom}</td>
                        <td>
                            <input type="text" name="nom${paci.pro}" id="nom${paci.pro}">
                            <a href="#" onclick="nombra('${paci.pro}')">Renombrar</a>
                        </td>
                        <td>${eliminar}</td>
                    </tr>
                `);
            });

            const $pagination = $('#pagination');
            $pagination.empty();
            if (data.currentPage > 1) {
                $pagination.append(`<li><a href="#" class="pagination-link" data-page="${data.currentPage - 1}">Anterior</a></li>`);
            }
            for (let i = 1; i <= data.totalPages; i++) {
                if (i === 1 || i === data.totalPages || Math.abs(i - data.currentPage) <= 1) {
                    $pagination.append(`
                        <li${i === data.currentPage ? ' class="active"' : ''}>
                            <a href="#" class="pagination-link" data-page="${i}">${i}</a>
                        </li>
                    `);
                } else if (i === 2 || i === data.totalPages - 1) {
                    $pagination.append('<li><a href="#">...</a></li>');
                }
            }
            if (data.currentPage < data.totalPages) {
                $pagination.append(`<li><a href="#" class="pagination-link" data-page="${data.currentPage + 1}">Siguiente</a></li>`);
            }
            return false;
        },
        complete: function() {
            hideLoader();
        }
    });
};
    $(document).ready(function() {
        

        const updateButtonState = () => {
            const searchValue = $('#filtro').val();
        };

        $('#filtro').on('input', function() {
            updateButtonState();
        });

        $('#filtro').on('keypress', function(e) {
            if (e.which === 13) { // Enter key pressed
                $('#search-button').click();
            } 
        });

        $('#search-button').on('click', function() {
            const search = $('#filtro').val();
            if ($(this).hasClass('search-button')) {
                if (search.length >= 3) {
                    loadTable(search);
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Búsqueda Incompleta',
                        text: 'Por favor, ingrese al menos 3 caracteres para buscar.'
                    });
                }
            } else {
                $('#filtro').val('');
                loadTable(); // Cargar sin filtro
                $('#search-button').removeClass('x-button').addClass('search-button').html('<i class="fas fa-search"></i>');
            }
        });

        $('#pagination').on('click', '.pagination-link', function(e) {
            e.preventDefault(); 
            const page = $(this).data('page');
            loadTable($('#filtro').val(), page);
        });

        // Inicializar carga de la tabla
        loadTable();
    });

    function borra(pro,id = null,dni = null) {
        Swal.fire({
            title: 'Confirmar',
            text: '¿Está seguro de que desea eliminar este registro?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {

            if (!result.isConfirmed) {
                return false
            }

            search = $("#filtro").val();
            page = $('#pagination li.active .pagination-link').data('page');
            
            datos =  {
                    accion: 1,
                    pro: pro,
                    pro2: id,
                    search: search,
                    page: page
                }
            loadTable(search,page,datos);  

            window.location.reload();
        });
        return false;
    }

    function nombra(pro) {
        // document.form2.accion.value = 2;
        // document.form2.pro.value = pro;
        // document.form2.submit();
        input = $('form').find('input[name=nom'+pro+']').val();
        console.log(input);
        search = $("#filtro").val();
        page = $('#pagination li.active .pagination-link').data('page');
            
        datos =  {
                    accion: 2,
                    pro: pro,
                    nombre : input,
                    search: search,
                    page: page
                };

        loadTable(search,page,datos); 
        return false;
    }
</script>
</body>
</html>
