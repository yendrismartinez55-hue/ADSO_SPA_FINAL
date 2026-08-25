<?php
/* PUNTO 3 TOTAL FACTURADO POR EMPLEADO */
function total_facturado($datos){
    $resultado =[];

    foreach ($datos["empleados"] as $empleado_id => $empleado){
        $total = 0;
        foreach ($datos["citas"] as $cita){
            if ($cita["empleado_id"] == $empleado_id){
                $total = $total + $cita["total"];
            }
        }
        $resultado[] = [
            "nombre" => $empleado["nombre"],
            "total" => $total
        ];
    }
    return $resultado;
}
/* PUNTO 4 SERVICIO MAS SOLICITADO */
function servicio_mas_solicitado($datos){
    $cantidad =[];

    foreach ($datos["citas"] as $cita){
        if (!isset($cantidad["servicio_id"])){
            $cantidad["servicio_id"] = 0;
        }
        $cantidad["servicio_id"] ++;
    }
    $mayor = 0;
    $servicio_mas_solicitado = 0;

    foreach ($cantidad as $servicio_id => $veces){
        if ($veces > $mayor){

            $mayor = $veces;
            $servicio_mas_solicitado = $servicio_id;
        }
    }
    $facturacion =0;
    foreach ($datos["citas"] as $cita){

        foreach ($cita["servicios"] as $servicio_id){
            if ($servicio_id == $servicio_mas_solicitado){
                $facturacion = $facturacion + $datos["servicios"][$servicio_id]["precio"];
            }
        }
    }
    return [
        "nombre" => $datos["servicios"][$servicio_mas_solicitado]["nombre"],
        "veces" => $mayor,
        "facturacion" => $facturacion
    ];
}

/* PUNTO 5 AGENDA DE UN DIA */
function agenda_dia($datos, $dia){

    $agenda = [];

    foreach ($datos["citas"] as $cita){

        if ($cita["dia"] == $dia){
            $agenda[] = $cita;

        }

    }

    /* Ordenar la agenda por hora */

    for ($i = 0; $i < count($agenda) - 1; $i++){
        for ($j = $i + 1; $j < count($agenda); $j++){

            if ($agenda[$j]["hora"] > $agenda[$j ]["hora"]){
                $temp = $agenda[$i];
                $agenda[$i] = $agenda[$j];
                $agenda[$j] = $temp;
            }
        }

    }  
    return $agenda;

}

/* PUNTO 6 DETECCION DE CONFLICTOS */
function detectar_conflictos($datos)
{
    $conflictos = [];

    foreach ($datos["empleados"] as $empleado_id => $empleado){
        $citas_empleado = [];

        foreach ($datos["citas"] as $cita){

            if ($cita["empleado_id"] == $empleado_id){

                $citas_empleado[] = $cita;


              
            }
        }

         for ($i = 0; $i < count($citas_empleado) - 1; $i++) {

            for ($j = $i + 1; $j < count($citas_empleado); $j++) {

                if (
                    $citas_empleado[$i]["dia"]
                    !=
                    $citas_empleado[$j]["dia"]
                ) {

                    continue;
                }
                 $inicio1 =
                    $citas_empleado[$i]["hora"];

                $fin1 =
                    $inicio1 +
                    $citas_empleado[$i]["duracion"];

                $inicio2 =
                    $citas_empleado[$j]["hora"];

                $fin2 =
                    $inicio2 +
                    $citas_empleado[$j]["duracion"];

                if (
                    $inicio1 < $fin2 &&
                    $inicio2 < $fin1
                ) {

                    $conflictos[] = [
                        "empleado" => $empleado["nombre"],
                        "dia" => $citas_empleado[$i]["dia"],
                        "cita1" => $citas_empleado[$i],
                        "cita2" => $citas_empleado[$j]
                    ];
                }
            }
         }
    }
    return $conflictos;
}

/* PUNTO 7 LIQUIDACION DE COMISIONES */
function liquidar_comisiones($datos)
{
    $resultado = [];

    $mayor_facturacion = 0;
    $empleado_mayor = 0;

    foreach ($datos["empleados"] as $empleado_id => $empleado) {

        $facturacion = 0;
        $cantidad_citas = 0;

        foreach ($datos["citas"] as $cita) {

            if ($cita["empleado_id"] == $empleado_id) {

                $facturacion =
                    $facturacion +
                    $cita["total"];
                $cantidad_citas++;
            }
        }

        /*
        Comisión
        */

        if ($cantidad_citas >= 6) {
            $porcentaje = 0.12;

        } else {

            $porcentaje = 0.08;
        }

        $comision =
            $facturacion * $porcentaje;

        $resultado[$empleado_id] = [

            "nombre" =>
                $empleado["nombre"],

            "citas" =>
                $cantidad_citas,
             "facturacion" =>
                $facturacion,

            "porcentaje" =>
                $porcentaje,

            "comision" =>
                $comision,

            "bono" =>
                0
        ];
         /*
        Buscar empleado con mayor facturación
        */

        if ($facturacion > $mayor_facturacion) {

            $mayor_facturacion =
                $facturacion;

            $empleado_mayor =
                $empleado_id;
        }
    }
     /*
    Bono
    */

    if ($empleado_mayor != 0) {

        $resultado[$empleado_mayor]["bono"] = 50000;
    }

    return $resultado;
}



$datos = [
    "empleados" =>[],
    "citas" =>[],
    "servicios" =>[
        1=>[
            "nombre" => "Limpieza facial",
            "precio" => 80.000,
            "duracion" => 2
        ],
        2=>[
            "nombre" => "Manicure",
            "precio" => 35.000,
            "duracion" => 1
        ],
        3=>[
            "nombre" => "Pedicure",
            "precio" => 40.000,
            "duracion" => 1
        ],
        4=>[
            "nombre" => "Masaje relajante",
            "precio" => 90.000,
            "duracion" => 1

        ],

        5=>[
            "nombre" => "Masaje descontracturante",
            "precio" => 100.000,
            "duracion" => 1
        ],

        6=>[
            "nombre" => "Exfoliante corporal",
            "precio" => 60.000,
            "duracion" => 1
        ],

        7=>[
            "nombre" => "Exfoliante antiedad",
            "precio" => 120.000,
            "duracion" => 2
        ]
    ]
    
];


$datos_prueba = false;

/* MENU */

do {
    echo "\n";
echo "ADSO- SPA\n";

 /*
    Si todavía no se han cargado los datos de prueba,
    se muestran normalmente las opciones 1 y 2.*/

 if ($datos_prueba == false) {

        echo "1. Registrar empleado\n";
        echo "2. Registrar cita\n";

    } else {

        echo "1. Registrar empleado (DESHABILITADA)\n";
        echo "2. Registrar cita (DESHABILITADA)\n";
    }

    echo "3. Total facturado por empleado\n";
    echo "4. Servicio mas solicitado\n";
    echo "5. Agenda de un dia\n";
    echo "6. Detección de conflictos\n";
    echo "7. Liquidación de comisiones\n";
    echo "8. Salir\n";


    $opcion = readline("Seleccione una opción: ");

/* PUNTO 1 REGISTRO DE EMPLEADOS */

if ($opcion == "1") {

        if ($datos_prueba == true) {

            echo "\n";
            echo "La opción 1 está deshabilitada.\n";
            echo "Los datos de prueba ya fueron cargados.\n";

        } else {

            echo "\n";
            echo "----- REGISTRAR EMPLEADO -----\n";

            $nombre =
                readline("Nombre: ");

            $especialidad =
                readline("Especialidad: ");

            $id =
                count($datos["empleados"]) + 1;

            $datos["empleados"][$id] = [

                "nombre" =>
                    $nombre,
                
                "especialidad" =>
                    $especialidad
            ];

            echo "\n";
            echo "Empleado registrado correctamente.\n";
        }
    }

    /*  PUNTO 2 REGISTRO DE CITAS */

    

    elseif ($opcion == "2") {

        if ($datos_prueba == true) {

            echo "\n";
            echo "La opción 2 está deshabilitada.\n";
            echo "Los datos de prueba ya fueron cargados.\n";

        } elseif (count($datos["empleados"]) == 0) {

            echo "\n";
            echo "Primero debe registrar un empleado.\n";

        } else {

            echo "\n";
            echo "----- REGISTRAR CITA -----\n";


            
echo "\n";
echo "╔════════════════════════════╦════════════╦════════════╗\n";
echo "║ SERVICIO                   ║ PRECIO     ║ DURACIÓN   ║\n";
echo "╠════════════════════════════╬════════════╬════════════╣\n";

foreach ($datos["servicios"] as $servicio) {

    echo "║ "
        . str_pad($servicio["nombre"], 26)
        . " ║ $"
        . str_pad(
            number_format($servicio["precio"], 0, ",", "."),
            10,
            " ",
            STR_PAD_RIGHT
        )
        . " ║ "
        . str_pad(
            $servicio["duracion"] ." hora". ($servicio["duracion"] > 1 ? "s" : ""),
            10
        )
        . " ║\n";
}

echo "╚════════════════════════════╩════════════╩════════════╝\n";


            /*
            Mostrar empleados
            */

            echo "\nEMPLEADOS:\n";

            foreach (
                $datos["empleados"]
                as $id => $empleado
            ) {

                echo $id
                    . ". "
                    . $empleado["nombre"]
                    . " - "
                    . $empleado["especialidad"]
                    . "\n";
            }

            $empleado_id =readline("Seleccione empleado: ");
                

            $cliente =readline("Cliente: ");

            $dia =readline( "Día (lunes a sábado): ");
             
             $hora =readline( "Hora: ");
               


            /*
            Mostrar servicios
            */

            echo "\n";
            echo "SERVICIOS:\n";

            foreach (
                $datos["servicios"]
                as $id => $servicio
            ) {
                echo $id
                    . ". "
                    . $servicio["nombre"]
                    . " - $"
                    . number_format(
                        $servicio["precio"],
                        0,
                        ",",
                        "."
                    )
                    . " - "
                    . $servicio["duracion"]
                    . " hora(s)\n";
            }


            /*
            Seleccionar servicios
            */
             $servicios_seleccionados = [];

            do {

                $servicio_id =readline("Seleccione servicio: ");
                

                if (
                    isset(
                        $datos["servicios"][
                            $servicio_id
                        ]
                    )
                ) {

                    $servicios_seleccionados[] =
                        $servicio_id;

                } else {

                    echo "Servicio no válido.\n";
                }

                $otro = readline("¿Agregar otro servicio? (s/n): ");
                   

            } while ($otro == "s");


            /*
            Calcular duración y total
            */

            $duracion = 0;
            $total = 0;

            foreach (
                $servicios_seleccionados
                as $servicio_id
            ) {

                $duracion =
                    $duracion +
                    $datos["servicios"][
                        $servicio_id
                    ]["duracion"];

                $total =
                    $total +
                    $datos["servicios"][
                        $servicio_id
                    ]["precio"];
            }


            /*
            Crear ID de la cita
            */

            $id_cita =
                count($datos["citas"]) + 1;


            /*
            Guardar la cita
            */

            $datos["citas"][$id_cita] = [
                
                "empleado_id" =>
                    $empleado_id,

                "cliente" =>
                    $cliente,

                "dia" =>
                    $dia,

                "hora" =>
                    $hora,

                "duracion" =>
                    $duracion,

                "total" =>
                    $total,

                "servicios" =>
                    $servicios_seleccionados
            ];
             echo "\n";
            echo "Cita registrada correctamente.\n";

            echo "Total de la cita: $"
                . number_format(
                    $total,
                    0,
                    ",",
                    "."
                )
                . "\n";
        }
    }

/* PUNTO 3 TOTAL FACTURADO POR EMPLEADO */

    elseif ($opcion == "3") {

        $resultado =
            total_facturado($datos);

        echo "\n";
        echo "=============================================\n";
        echo "       TOTAL FACTURADO POR EMPLEADO\n";
        echo "=============================================\n";

        foreach ($resultado as $empleado) {

            echo "Empleado: "
                . $empleado["nombre"]
                . "\n";

            echo "Total facturado: $"
                . number_format(
                    $empleado["total"],
                    0,
                    ",",
                    "."
                )
                . "\n";
            
            echo "---------------------------------------------\n";
        }
    }

/* PUNTO 4 SERVICIO MAS SOLICITADO */

    elseif ($opcion == "4") {

        $resultado =
            servicio_mas_solicitado($datos);

        echo "\n";
        echo "       SERVICIO MÁS SOLICITADO\n";

        echo "Servicio: "
            . $resultado["nombre"]
            . "\n";

        echo "Cantidad de veces solicitado: "
            . $resultado["veces"]
            . "\n";

        echo "Facturación total: $"
            . number_format(
                $resultado["facturacion"],
                0,
                ",",
                "."
            )
            . "\n";
    }

/* PUNTO 5 AGENDA DE UN DIA */

    elseif ($opcion == "5") {

        $dia =readline("Ingrese el día (lunes a sábado): ");
            

        $agenda =
            agenda_dia($datos, $dia);

        echo "\n";
        echo "       AGENDA DEL DÍA: "
        
            . strtoupper($dia)
            . "\n";

        if (count($agenda) == 0) {

            echo "No hay citas para este día.\n";

        } else {

            foreach ($agenda as $cita) {

                $empleado =
                    $datos["empleados"][
                        $cita["empleado_id"]
                    ]["nombre"];

                echo "Hora: "
                    . $cita["hora"]
                    . ":00 - "
                    . "Empleado: "
                    . $empleado
                    . " - Cliente: "
                    . $cita["cliente"]
                    . " - Total: $"
                    . number_format(
                        $cita["total"],
                        0,
                        ",",
                        "."
                    )
                    . "\n";
            }
        }
    }

/* PUNTO 6 DETECCION DE CONFLICTOS */

    elseif ($opcion == "6") {

        $conflictos =
            detectar_conflictos($datos);

        echo "\n";
        echo "       DETECCIÓN DE CONFLICTOS\n";

        if (count($conflictos) == 0) {

            echo "No se encontraron conflictos.\n";

        } else {

            foreach ($conflictos as $conflicto) {

                echo "Empleado: "
                    . $conflicto["empleado"]
                    . "\n";

                echo "Día: "
                    . $conflicto["dia"]
                    . "\n";

                echo "Cita 1: "
                    . "Hora: "
                    . $conflicto["cita1"]["hora"]
                    . ":00 - Cliente: "
                    . $conflicto["cita1"]["cliente"]
                    . "\n";

                echo "Cita 2: "
                    . "Hora: "
                    . $conflicto["cita2"]["hora"]
                    . ":00 - Cliente: "
                    . $conflicto["cita2"]["cliente"]
                    . "\n";

                echo "---------------------------------------------\n";
            }
        }
    }

/* PUNTO 7 LIQUIDACION DE COMISIONES */

    elseif ($opcion == "7") {

        $resultado =
            liquidar_comisiones($datos);

        echo "\n";
        echo "       LIQUIDACIÓN DE COMISIONES\n";

        foreach ($resultado as $empleado) {

            echo "Empleado: "
                . $empleado["nombre"]
                . "\n";

            echo "Cantidad de citas: "
                . $empleado["citas"]
                . "\n";

            echo "Facturación: $"
                . number_format(
                    $empleado["facturacion"],
                    0,
                    ",",
                    "."
                )
                . "\n";

            echo "Porcentaje de comisión: "
                . ($empleado["porcentaje"] * 100)
                . "%\n";

            echo "Comisión: $"
                . number_format(
                    $empleado["comision"],
                    0,
                    ",",
                    "."
                )
                . "\n";

            echo "Bono: $"
                . number_format(
                    $empleado["bono"],
                    0,
                    ",",
                    "."
                )
                . "\n";

            echo "---------------------------------------------\n";
        }
    }

/*  DP CARGA DE DATOS DE PRUEBA */

    elseif ($opcion == "dp") {

        if ($datos_prueba == true) {

            echo "\n";
            echo "Los datos de prueba ya fueron cargados.\n";

        } else {
            /* EMPLEADOS DE PRUEBA */

            $datos["empleados"] = [

                1 => [
                    "nombre" =>
                        "Ana",

                    "especialidad" =>
                        "Manicure"
                ],

                2 => [
                    "nombre" =>
                        "Laura",

                    "especialidad" =>
                        "Facial"
                ],

                3 => [
                    "nombre" =>
                        "Camila",

                    "especialidad" =>
                        "Masajes"
                ],

                4 => [
                    "nombre" =>
                        "Valentina",

                    "especialidad" =>
                        "Corporal"
                ]
            ];

            /*
            =============================================
            15 CITAS
            =============================================
            */

            $datos["citas"] = [

                1 => [
                    "empleado_id" => 1,
                    "cliente" => "María",
                    "dia" => "lunes",
                    "hora" => 8,
                    "servicios" => [2, 3],
                    "duracion" => 2,
                    "total" => 75000
                ],

                2 => [
                    "empleado_id" => 2,
                    "cliente" => "Sofía",
                    "dia" => "lunes",
                    "hora" => 9,
                    "servicios" => [1],
                    "duracion" => 2,
                    "total" => 80000
                ],

                3 => [
                    "empleado_id" => 3,
                    "cliente" => "Daniela",
                    "dia" => "lunes",
                    "hora" => 10,
                    "servicios" => [4, 5],
                    "duracion" => 2,
                    "total" => 190000
                ],

                4 => [
                    "empleado_id" => 4,
                    "cliente" => "Paula",
                    "dia" => "lunes",
                    "hora" => 11,
                    "servicios" => [6, 7],
                    "duracion" => 3,
                    "total" => 180000
                ],

                5 => [
                    "empleado_id" => 1,
                    "cliente" => "Carolina",
                    "dia" => "martes",
                    "hora" => 9,
                    "servicios" => [2],
                    "duracion" => 1,
                    "total" => 35000
                ],

                6 => [
                    "empleado_id" => 2,
                    "cliente" => "Juliana",
                    "dia" => "martes",
                    "hora" => 10,
                    "servicios" => [1, 7],
                    "duracion" => 4,
                    "total" => 200000
                ],

                7 => [
                    "empleado_id" => 3,
                    "cliente" => "Andrea",
                    "dia" => "martes",
                    "hora" => 8,
                    "servicios" => [4],
                    "duracion" => 1,
                    "total" => 90000
                ],

                8 => [
                    "empleado_id" => 4,
                    "cliente" => "Natalia",
                    "dia" => "martes",
                    "hora" => 14,
                    "servicios" => [6],
                    "duracion" => 1,
                    "total" => 60000
                ],

                9 => [
                    "empleado_id" => 1,
                    "cliente" => "Luisa",
                    "dia" => "miércoles",
                    "hora" => 8,
                    "servicios" => [2, 3],
                    "duracion" => 2,
                    "total" => 75000
                ],

                10 => [
                    "empleado_id" => 2,
                    "cliente" => "Gabriela",
                    "dia" => "miércoles",
                    "hora" => 13,
                    "servicios" => [1],
                    "duracion" => 2,
                    "total" => 80000
                ],

                11 => [
                    "empleado_id" => 3,
                    "cliente" => "Isabela",
                    "dia" => "jueves",
                    "hora" => 9,
                    "servicios" => [4, 5],
                    "duracion" => 2,
                    "total" => 190000
                ],

                12 => [
                    "empleado_id" => 4,
                    "cliente" => "Valeria",
                    "dia" => "jueves",
                    "hora" => 10,
                    "servicios" => [6, 3],
                    "duracion" => 2,
                    "total" => 100000
                ],

                 /*
                CONFLICTO INTENCIONAL

                Ana:
                Cita 13: 14:00 - 16:00
                Cita 14: 15:00 - 16:00
                */

                13 => [
                    "empleado_id" => 1,
                    "cliente" => "Patricia",
                    "dia" => "viernes",
                    "hora" => 14,
                    "servicios" => [1],
                    "duracion" => 2,
                    "total" => 80000
                ],

                14 => [
                    "empleado_id" => 1,
                    "cliente" => "Mónica",
                    "dia" => "viernes",
                    "hora" => 15,
                    "servicios" => [2],
                    "duracion" => 1,
                    "total" => 35000
                ],

                15 => [
                    "empleado_id" => 4,
                    "cliente" => "Sara",
                    "dia" => "sábado",
                    "hora" => 9,
                    "servicios" => [7, 6],
                    "duracion" => 3,
                    "total" => 180000
                ]
            ];

             /*
            Activamos los datos de prueba
            */

            $datos_prueba = true;


            echo "\n";
            echo "=============================================\n";
            echo "       DATOS DE PRUEBA CARGADOS\n";
            echo "=============================================\n";
            echo "4 empleados cargados.\n";
            echo "15 citas cargadas.\n";
            echo "Las opciones 1 y 2 están deshabilitadas.\n";
        }
    }


    /*
    SALIR
    */

    elseif ($opcion == "8") {

        echo "\n";
        echo "        PROGRAMA FINALIZADO\n";
    }


    /*
    OPCIÓN NO VÁLIDA
    */

    else {

        echo "\n";
        echo "Opción no válida.\n";
    }


} while ($opcion != "8");



    
?>