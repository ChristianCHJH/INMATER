<?php session_start(); ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset='utf-8'/>
        <link rel="icon" href="_images/favicon.png" type="image/x-icon">
        <link href='_agenda/fullcalendar.css' rel='stylesheet'/>
        <link href='_agenda/fullcalendar.print.css' rel='stylesheet' media='print'/>
        <link rel="stylesheet" href="css/jquery.mobile-1.0a4.1.min.css" />

        <style>
            .loader {
                position: fixed;
                height: 100%;
                left: 0;
                top: 0;
                width: 100%;
                background: #FFF;
                z-index: 9999;
            }
            .loader img {
                position: absolute;
                left: 50%;
                top: 50%;
                transform: translate(-50%, -50%); 
                -webkit-transform: translate(-50%, -50%);
                -moz-transform: translate(-50%, -50%);
            }

            body {
                margin: 0;
                padding: 0;
                font-family: "Lucida Grande", Helvetica, Arial, Verdana, sans-serif;
                font-size: 13px;
            }

            #script-warning {
                display: none;
                background: #eee;
                border-bottom: 1px solid #ddd;
                padding: 0 10px;
                line-height: 40px;
                text-align: center;
                font-weight: bold;
                font-size: 12px;
                color: red;
            }

            #loading {
                display: none;
                top: 10px;
                color: #ED0E11;
            }

            #calendar {
                margin: 10px auto;
                padding: 0 10px;
            }

            .fc-slats table tr:nth-child(1) {
                display: none;
            }

            .fc-slats table tr:nth-child(2) {
                display: none;
            }

            .fc-slats table tr:nth-child(3) {
                display: none;
            }

            .fc-slats table tr:nth-child(4) {
                display: none;
            }

            .fc-slats table tr:nth-child(5) {
                display: none;
            }

            .fc-slats table tr:nth-child(6) {
                display: none;
            }

            .fc-slats table tr:nth-child(7) {
                display: none;
            }

            .fc-slats table tr:nth-child(8) {
                display: none;
            }

            .fc-slats table tr:nth-child(9) {
                display: none;
            }

            .fc-slats table tr:nth-child(10) {
                display: none;
            }

            .fc-slats table tr:nth-child(11) {
                display: none;
            }

            .fc-slats table tr:nth-child(12) {
                display: none;
            }

            .fc-slats table tr:nth-child(48) {
                display: none;
            }

            .ui-body-c {
                text-shadow: none !important;
            }
            a.demo-class {
                pointer-events: auto !important;
            }
            .fc-toolbar {
                text-transform: uppercase;
            }
            span.leyenda-item {padding: 4px 12px;}
            p.leyenda-title {font-size: 13px; font-weight: 300;}
            .leyenda-content {padding-bottom: 20px;}
        </style>

        <?php
        if ($_GET['med'] <> '') { ?>
            <style> .fc-event-container {pointer-events: none; cursor: default;}</style>
        <?php } ?>
    </head>
    <body>
        <div class="loader">
            <img src="_images/load.gif" alt="">			
        </div>
        <div data-role="page">
            <div data-role="content">
                <div id='script-warning'>
                    <code>php/get-events.php</code> must be running.
                </div>

                <div id='loading'>
                    <b>CARGANDO AGENDA...</b>
                </div>

                <div class="leyenda-content">
                    <p class="leyenda-title"><b>LEYENDA:</b></p>
                    <span class='leyenda-item' style="color:#fff;background:green;">Ginecologia y Obstetricia</span>
                    <span class='leyenda-item' style="color:#fff;background:#3a87ad;">Intervenciones en Inmater</span>
                    <span class='leyenda-item' style="color:#fff;background:#687466;">Consultas Médicas</span>
                    <?php
                    if ($_SESSION['role'] <> 2) {
                        print('
                        <span class="leyenda-item" style="color:#fff;background:orange;">Intervenciones externas</span>
                        <span class="leyenda-item" style="color:#fff;background:deeppink;">No Disponible</span>');
                    } ?>
                </div>

                <div id='calendar'></div>
            </div>
        </div>

        <div data-role="page" data-url="detalle.html">
            <div data-role="header" id="detalle-header">
                <h1>Detalle</h1>
            </div>
            <div data-role="content" id="detalle-content"></div>
        </div>

        <script type="text/javascript" src="js/jquery-1.5.2.min.js"></script>
        <script type="text/javascript" src="js/jquery.mobile-1.0a4.1.min.js"></script>
        <script src='_agenda/lib/moment.min.js'></script>
        <script src='_agenda/lib/jquery.min.js'></script>
        <script src='_agenda/fullcalendar.min.js'></script>
        <script src='_agenda/lang/es.js'></script>

        <script>
            jQuery(window).load(function (event) {
                jQuery('.loader').fadeOut(1000);
            });

            $(document).ready(function () {
                window.parent.postMessage({cargando: 'si'}, '*');

                $('#calendar').fullCalendar({
                    header: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'month,agendaWeek,agendaDay'
                    },
                    editable: false,
                    allDaySlot: false,
                    eventLimit: false,
                    events: {
                        url: '_agenda/calendar/php/get-events.php?med=<?php echo $_GET['med']; ?>',
                        error: function () {
                            $('#script-warning').show();
                        },
                        complete: function() {
                            window.parent.postMessage({cargando: 'no'}, '*');
                        }
                    },
                    loading: function (e) {
                        if (e) {
                            window.parent.postMessage({cargando: 'si'}, '*');
                        } else {
                            window.parent.postMessage({cargando: 'no'}, '*');
                        }
                    },
                    eventClick: function (event) {
                        if (event.url && event.url != "detalle.html") {
                            window.open(event.url, "_parent");
                            return false;
                        } else {
                            $('#detalle-content').html(event.detalle);
                            $('#detalle-content').append('<br><br><a href="javascript:void(0);" onclick="parent.window.location=\'seguimiento-consulta.php?id=' + event.id + '\';">Ver el seguimiento de la consulta</a>').trigger("create");
                        }
                    }
                });
            });
        </script>
    </body>
</html>
