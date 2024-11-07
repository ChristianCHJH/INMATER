<?php
// Incluye la clase ExamenHandler
include '../../ManExamenController.php';

// Instancia la clase ExamenHandler
$handler = new ManExamenController();

// Llama al método processData y retorna la respuesta JSON
echo $handler->loadData();