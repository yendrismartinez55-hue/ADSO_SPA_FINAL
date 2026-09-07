<?php

/* PUNTO 3 - TOTAL FACTURADO POR EMPLEADO */

function total_facturado($datos)
{
    $resultado = [];

    foreach ($datos["empleados"] as $empleado_id => $empleado) {

        $total = 0;

        foreach ($datos["citas"] as $cita) {

            if ($cita["empleado_id"] == $empleado_id) {
                $total += $cita["total"];
            }
        }

        $resultado[] = [
            "nombre" => $empleado["nombre"],
            "total" => $total
        ];
    }

    return $resultado;
}


/* PUNTO 4 - SERVICIO MÁS SOLICITADO */

function servicio_mas_solicitado($datos)
{
    $cantidad = [];

    foreach ($datos["citas"] as $cita) {

        foreach ($cita["servicios"] as $servicio_id) {

            if (!isset($cantidad[$servicio_id])) {
                $cantidad[$servicio_id] = 0;
            }

            $cantidad[$servicio_id]++;
        }
    }

    if (count($cantidad) == 0) {

        return [
            "nombre" => "No hay servicios registrados",
            "veces" => 0,
            "facturacion" => 0
        ];
    }

    $mayor = 0;
    $servicio_mas_solicitado = null;

    foreach ($cantidad as $servicio_id => $veces) {

        if ($veces > $mayor) {

            $mayor = $veces;
            $servicio_mas_solicitado = $servicio_id;
        }
    }

    $facturacion = 0;

    foreach ($datos["citas"] as $cita) {

        foreach ($cita["servicios"] as $servicio_id) {

            if ($servicio_id == $servicio_mas_solicitado) {

                $facturacion +=
                    $datos["servicios"][$servicio_id]["precio"];
            }
        }
    }

    return [
        "nombre" =>
            $datos["servicios"][$servicio_mas_solicitado]["nombre"],

        "veces" =>
            $mayor,

        "facturacion" =>
            $facturacion
    ];
}


/* PUNTO 5 - AGENDA DE UN DÍA */

function agenda_dia($datos, $dia)
{
    $agenda = [];

    foreach ($datos["citas"] as $cita) {

        if (
            strtolower(trim($cita["dia"])) ==
            strtolower(trim($dia))
        ) {

            $agenda[] = $cita;
        }
    }

    /*
    Ordenar la agenda por hora
    */

    for ($i = 0; $i < count($agenda) - 1; $i++) {

        for ($j = $i + 1; $j < count($agenda); $j++) {

            if ($agenda[$i]["hora"] > $agenda[$j]["hora"]) {

                $temp = $agenda[$i];

                $agenda[$i] = $agenda[$j];

                $agenda[$j] = $temp;
            }
        }
    }

    return $agenda;
}


/* PUNTO 6 - DETECCIÓN DE CONFLICTOS */

function detectar_conflictos($datos)
{
    $conflictos = [];

    /*
    Revisamos empleado por empleado
    */

    foreach ($datos["empleados"] as $empleado_id => $empleado) {

        $citas_empleado = [];

        /*
        Buscar todas las citas del empleado
        */

        foreach ($datos["citas"] as $cita) {

            if ($cita["empleado_id"] == $empleado_id) {

                $citas_empleado[] = $cita;
            }
        }

        /*
        Comparar las citas entre ellas
        */

        for (
            $i = 0;
            $i < count($citas_empleado) - 1;
            $i++
        ) {

            for (
                $j = $i + 1;
                $j < count($citas_empleado);
                $j++
            ) {

                /*
                Si son días diferentes,
                no existe conflicto.
                */

                if (
                    strtolower($citas_empleado[$i]["dia"]) !=
                    strtolower($citas_empleado[$j]["dia"])
                ) {

                    continue;
                }

                /*
                Hora de inicio y finalización
                de la primera cita.
                */

                $inicio1 =
                    $citas_empleado[$i]["hora"];

                $fin1 =
                    $inicio1 +
                    $citas_empleado[$i]["duracion"];

                /*
                Hora de inicio y finalización
                de la segunda cita.
                */

                $inicio2 =
                    $citas_empleado[$j]["hora"];

                $fin2 =
                    $inicio2 +
                    $citas_empleado[$j]["duracion"];

                /*
                Verificar si los horarios se cruzan.
                */

                if (
                    $inicio1 < $fin2 &&
                    $inicio2 < $fin1
                ) {

                    $conflictos[] = [

                        "empleado" =>
                            $empleado["nombre"],

                        "dia" =>
                            $citas_empleado[$i]["dia"],

                        "cita1" =>
                            $citas_empleado[$i],

                        "cita2" =>
                            $citas_empleado[$j]
                    ];
                }
            }
        }
    }

    return $conflictos;
}


/* PUNTO 7 - LIQUIDACIÓN DE COMISIONES */

function liquidar_comisiones($datos)
{
    $resultado = [];

    $mayor_facturacion = 0;

    $empleado_mayor = 0;

    foreach (
        $datos["empleados"]
        as $empleado_id => $empleado
    ) {

        $facturacion = 0;

        $cantidad_citas = 0;

        foreach ($datos["citas"] as $cita) {

            if ($cita["empleado_id"] == $empleado_id) {

                $facturacion += $cita["total"];

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
    Bono de $50.000 para quien
    tenga mayor facturación.
    */

    if ($empleado_mayor != 0) {

        $resultado[$empleado_mayor]["bono"] =
            50000;
    }

    return $resultado;
}


/* DATOS PRINCIPALES */

$datos = [

    "empleados" => [],

    "citas" => [],

    "servicios" => [

        1 => [
            "nombre" => "Limpieza facial",
            "precio" => 80000,
            "duracion" => 2
        ],

        2 => [
            "nombre" => "Manicure",
            "precio" => 35000,
            "duracion" => 1
        ],

        3 => [
            "nombre" => "Pedicure",
            "precio" => 40000,
            "duracion" => 1
        ],

        4 => [
            "nombre" => "Masaje relajante",
            "precio" => 90000,
            "duracion" => 1
        ],

        5 => [
            "nombre" => "Masaje descontracturante",
            "precio" => 100000,
            "duracion" => 1
        ],

        6 => [
            "nombre" => "Exfoliante corporal",
            "precio" => 60000,
            "duracion" => 1
        ],

        7 => [
            "nombre" => "Exfoliante antiedad",
            "precio" => 120000,
            "duracion" => 2
        ]
    ]
];


$datos_prueba = false;
$conflictos_detectados = [];




/* MENÚ PRINCIPAL*/

do {

    echo "\n";
    echo "=============================================\n";
    echo "                 ADSO - SPA\n";
    echo "=============================================\n";

    /*
    Opciones 1 y 2
    */

    if ($datos_prueba == false) {

        echo "1. Registrar empleado\n";
        echo "2. Registrar cita\n";

    } else {

        echo "1. Registrar empleado (DESHABILITADA)\n";
        echo "2. Registrar cita (DESHABILITADA)\n";
    }

    echo "3. Total facturado por empleado\n";
    echo "4. Servicio más solicitado\n";
    echo "5. Agenda de un día\n";
    echo "6. Detección de conflictos\n";
    echo "7. Liquidación de comisiones\n";
    echo "8. Salir\n";

 

    $opcion =
        strtolower(
            trim(
                readline("Seleccione una opción: ")
            )
        );


    /* PUNTO 1 - REGISTRAR EMPLEADO */

    if ($opcion == "1") {

        if ($datos_prueba == true) {

            echo "\n";
            echo "La opción 1 está deshabilitada.\n";
            echo "Los datos de prueba ya fueron cargados.\n";

        } else {

            echo "\n";
            echo "----- REGISTRAR EMPLEADO -----\n";

            $nombre =
                trim(
                    readline("Nombre: ")
                );

            $especialidad =
                trim(
                    readline("Especialidad: ")
                );

            if ($nombre == "") {

                echo "El nombre no puede estar vacío.\n";

            } elseif ($especialidad == "") {

                echo "La especialidad no puede estar vacía.\n";

            } else {

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
                echo "ID asignado: " . $id . "\n";
            }
        }
    }


    /* PUNTO 2 - REGISTRAR CITA */

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
            echo "              REGISTRAR CITA\n";
            


            /* ---------------------------------------------
               MOSTRAR EMPLEADOS
               --------------------------------------------- */

            echo "\n";
            echo "EMPLEADOS DISPONIBLES:\n";

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


            /*
            Seleccionar empleado por nombre
            */

            $empleado_nombre =
                trim(
                    readline("Seleccione empleado por nombre: ")
                );

            $empleado_id = null;

            foreach (
                $datos["empleados"]
                as $id => $empleado
            ) {

                if (
                    strtolower(
                        trim($empleado["nombre"])
                    ) ==
                    strtolower(
                        trim($empleado_nombre)
                    )
                ) {

                    $empleado_id = $id;

                    break;
                }
            }

            if ($empleado_id === null) {

                echo "\n";
                echo "Empleado no válido.\n";
                echo "Debe escribir exactamente el nombre de un empleado registrado.\n";

                continue;
            }


            /* ---------------------------------------------
               CLIENTE
               --------------------------------------------- */

            $cliente =
                trim(
                    readline("Cliente: ")
                );

            if ($cliente == "") {

                echo "El nombre del cliente no puede estar vacío.\n";

                continue;
            }


            /* ---------------------------------------------
               DÍA
               --------------------------------------------- */

            $dia =
                strtolower(
                    trim(
                        readline(
                            "Día (lunes a sábado): "
                        )
                    )
                );

            $dias_validos = [
                "lunes",
                "martes",
                "miércoles",
                "miercoles",
                "jueves",
                "viernes",
                "sábado",
                "sabado"
            ];

            if (!in_array($dia, $dias_validos)) {

                echo "\n";
                echo "Día no válido.\n";
                echo "Use un día de lunes a sábado.\n";

                continue;
            }

            /*
            Normalizar días
            */

            if ($dia == "miercoles") {
                $dia = "miércoles";
            }

            if ($dia == "sabado") {
                $dia = "sábado";
            }


            /* ---------------------------------------------
               HORA
               --------------------------------------------- */

            $hora =
                trim(
                    readline(
                        "Hora de inicio (ejemplo 14): "
                    )
                );

            if (
                !is_numeric($hora) ||
                $hora < 0 ||
                $hora > 23
            ) {

                echo "\n";
                echo "Hora no válida.\n";

                continue;
            }

            $hora = (int)$hora;


            /* ---------------------------------------------
               MOSTRAR SERVICIOS
               --------------------------------------------- */

            echo "\n";
            echo "SERVICIOS:\n";

            echo "╔════╦══════════════════════════════╦════════════╦════════════╗\n";
            echo "║ ID ║ SERVICIO                     ║ PRECIO     ║ DURACIÓN   ║\n";
            echo "╠════╬══════════════════════════════╬════════════╬════════════╣\n";

            foreach (
                $datos["servicios"]
                as $id => $servicio
            ) {

                echo "║ "
                    . str_pad($id, 2)
                    . " ║ "
                    . str_pad(
                        $servicio["nombre"],
                        28
                    )
                    . " ║ $"
                    . str_pad(
                        number_format(
                            $servicio["precio"],
                            0,
                            ",",
                            "."
                        ),
                        10
                    )
                    . " ║ "
                    . str_pad(
                        $servicio["duracion"] . " hora(s)",
                        10
                    )
                    . " ║\n";
            }

            echo "╚════╩══════════════════════════════╩════════════╩════════════╝\n";


            /* SELECCIONAR SERVICIOS */

            $servicios_seleccionados = [];

            do {

                $servicio_id =
                    trim(
                        readline(
                            "Seleccione servicio: "
                        )
                    );

                if (
                    isset(
                        $datos["servicios"][$servicio_id]
                    )
                ) {

                    if (
                        !in_array(
                            $servicio_id,
                            $servicios_seleccionados
                        )
                    ) {

                        $servicios_seleccionados[] =
                            $servicio_id;

                        echo "Servicio agregado correctamente.\n";

                    } else {

                        echo "Ese servicio ya fue seleccionado.\n";
                    }

                } else {

                    echo "Servicio no válido.\n";
                }

                $otro =strtolower(trim(readline("¿Agregar otro servicio? (s/n): ")));

            } while ($otro == "s");


            /*
            Verificar que haya al menos un servicio
            */

            if (
                count($servicios_seleccionados) == 0
            ) {

                echo "\n";
                echo "Debe seleccionar al menos un servicio.\n";

                continue;
            }


            /* CALCULAR DURACIÓN Y TOTAL */

            $duracion = 0;

            $total = 0;

            foreach (
                $servicios_seleccionados
                as $servicio_id
            ) {

                $duracion +=
                    $datos["servicios"][$servicio_id]["duracion"];

                $total +=
                    $datos["servicios"][$servicio_id]["precio"];
            }


            /* VERIFICAR CONFLICTO */

            $hora_ocupada = false;
            $cita_conflictiva = null;

            foreach ($datos["citas"] as $cita) {

                if (
                    $cita["empleado_id"] == $empleado_id &&
                    strtolower(trim($cita["dia"])) == strtolower(trim($dia))
                ) {

                    $inicio_existente = $cita["hora"];
                    $fin_existente =
                        $cita["hora"] + $cita["duracion"];

                    $inicio_nueva = $hora;
                    $fin_nueva =
                        $hora + $duracion;

                    /*
                    Verificar si los horarios se cruzan
                    */

                    if (
                        $inicio_nueva < $fin_existente &&
                        $fin_nueva > $inicio_existente
                    ) {

                        $hora_ocupada = true;

                        $cita_conflictiva = $cita;

                        break;
                    }
                }
            }


            /* SI HAY CONFLICTO */

            if ($hora_ocupada) {

                /*
                La cita NO se guarda en $datos["citas"].
                Pero guardamos temporalmente el conflicto.
                */

                $conflictos_detectados[] = [

                    "empleado" =>
                        $datos["empleados"][$empleado_id]["nombre"],

                    "dia" =>
                        $dia,

                    "cita1" => $cita_conflictiva,

                    "cita2" => [

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
                    ]
                ];

                echo "\n";
                echo "          CONFLICTO DETECTADO\n";
              

                echo "Empleado: "
                    . $datos["empleados"][$empleado_id]["nombre"]
                    . "\n";

                echo "Día: "
                    . strtoupper($dia)
                    . "\n";

                echo "Cita existente: "
                    . $cita_conflictiva["cliente"]
                    . " - "
                    . $cita_conflictiva["hora"]
                    . ":00\n";

                echo "Nueva cita: "
                    . $cliente
                    . " - "
                    . $hora
                    . ":00\n";

                echo "\n";
                echo "La cita NO fue registrada porque existe un conflicto.\n";
             

              

            } else {

                /*
                NO HAY CONFLICTO
                Entonces sí guardamos la cita.
                */

                $id_cita =
                    count($datos["citas"]) + 1;

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
                echo "       CITA REGISTRADA CORRECTAMENTE\n";
        

                echo "Empleado: "
                    . $datos["empleados"][$empleado_id]["nombre"]
                    . "\n";

                echo "Cliente: "
                    . $cliente
                    . "\n";

                echo "Día: "
                    . $dia
                    . "\n";

                echo "Hora: "
                    . $hora
                    . ":00\n";

                echo "Duración: "
                    . $duracion
                    . " hora(s)\n";

                echo "Total: $"
                    . number_format(
                        $total,
                        0,
                        ",",
                        "."
                    )
                    . "\n";

       
            }
        }
    }
    /*  PUNTO 3 - TOTAL FACTURADO*/

    elseif ($opcion == "3") {

        $resultado =
            total_facturado($datos);

        echo "\n";
        echo "       TOTAL FACTURADO POR EMPLEADO\n";


        if (
            count($resultado) == 0
        ) {

            echo "No hay empleados registrados.\n";

        } else {

            foreach (
                $resultado
                as $empleado
            ) {

                echo "\n";

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
            }
        }
    }


    /* PUNTO 4 - SERVICIO MÁS SOLICITADO */

    elseif ($opcion == "4") {

        $resultado =
            servicio_mas_solicitado($datos);

        echo "\n";
        echo "          SERVICIO MÁS SOLICITADO\n";


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


    /* PUNTO 5 - AGENDA*/

    elseif ($opcion == "5") {

        $dia =
            strtolower(
                trim(
                    readline(
                        "Ingrese el día (lunes a sábado): "
                    )
                )
            );

        if ($dia == "miercoles") {
            $dia = "miércoles";
        }

        if ($dia == "sabado") {
            $dia = "sábado";
        }

        $agenda =
            agenda_dia(
                $datos,
                $dia
            );

        echo "\n";
        echo "       AGENDA DEL DÍA: "
            . strtoupper($dia)
            . "\n";

        if (
            count($agenda) == 0
        ) {

            echo "No hay citas para este día.\n";

        } else {

            foreach (
                $agenda
                as $cita
            ) {

                $empleado =
                    $datos["empleados"]
                    [$cita["empleado_id"]]
                    ["nombre"];

                $fin =
                    $cita["hora"] +
                    $cita["duracion"];

                echo "\n";

                echo "Hora: "
                    . $cita["hora"]
                    . ":00 - "
                    . $fin
                    . ":00\n";

                echo "Empleado: "
                    . $empleado
                    . "\n";

                echo "Cliente: "
                    . $cita["cliente"]
                    . "\n";

                echo "Duración: "
                    . $cita["duracion"]
                    . " hora(s)\n";

                echo "Total: $"
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


    elseif ($opcion == "6") {

    echo "\n";
    echo "          DETECCIÓN DE CONFLICTOS\n";

    if (count($conflictos_detectados) == 0) {

        echo "No se encontraron conflictos.\n";

    } else {

        echo "Se encontraron "
            . count($conflictos_detectados)
            . " conflicto(s).\n\n";

        foreach (
            $conflictos_detectados
            as $numero => $conflicto
        ) {

            $cita1 =
                $conflicto["cita1"];

            $cita2 =
                $conflicto["cita2"];

            $fin1 =
                $cita1["hora"] +
                $cita1["duracion"];

            $fin2 =
                $cita2["hora"] +
                $cita2["duracion"];

            echo "CONFLICTO #"
                . ($numero + 1)
                . "\n";

            echo "Empleado: "
                . $conflicto["empleado"]
                . "\n";

            echo "Día: "
                . strtoupper($conflicto["dia"])
                . "\n";

            echo "\n";

            echo "CITA EXISTENTE\n";

            echo "Cliente: "
                . $cita1["cliente"]
                . "\n";

            echo "Hora: "
                . $cita1["hora"]
                . ":00 - "
                . $fin1
                . ":00\n";

            echo "\n";

            echo "CITA EN CONFLICTO\n";

            echo "Cliente: "
                . $cita2["cliente"]
                . "\n";

            echo "Hora: "
                . $cita2["hora"]
                . ":00 - "
                . $fin2
                . ":00\n";

            echo "\n";

            echo " Esta cita NO fue registrada.\n";

        }
    }
}

    /* PUNTO 7 - LIQUIDACIÓN DE COMISIONES */

    elseif ($opcion == "7") {

        $resultado =
            liquidar_comisiones($datos);

        echo "\n";
        echo "       LIQUIDACIÓN DE COMISIONES\n";

        if (
            count($resultado) == 0
        ) {

            echo "No hay empleados registrados.\n";

        } else {

            foreach (
                $resultado
                as $empleado
            ) {

                echo "\n";

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
                    . (
                        $empleado["porcentaje"] * 100
                    )
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
            }
        }
    }


    /* DP - CARGA DE DATOS DE PRUEBA */

    elseif ($opcion == "dp") {

        if ($datos_prueba == true) {

            echo "\n";
            echo "Los datos de prueba ya fueron cargados.\n";

        } else {

            /* ---------------------------------------------
               EMPLEADOS DE PRUEBA
               --------------------------------------------- */

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


            /* ---------------------------------------------
               CITAS DE PRUEBA
               --------------------------------------------- */

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
            Activar datos de prueba
            */

            $datos_prueba = true;


            echo "\n";
            echo "       DATOS DE PRUEBA CARGADOS\n";

            echo "\n";
            echo "EMPLEADOS REGISTRADOS:\n";

            foreach (
                $datos["empleados"]
                as $id => $empleado
            ) {

                echo "ID: "
                    . $id
                    . "\n";

                echo "Nombre: "
                    . $empleado["nombre"]
                    . "\n";

                echo "Especialidad: "
                    . $empleado["especialidad"]
                    . "\n";
            }


            echo "\n";
            echo "CITAS REGISTRADAS:\n";

            foreach (
                $datos["citas"]
                as $id => $cita
            ) {

                echo "ID Cita: "
                    . $id
                    . "\n";

                echo "Empleado ID: "
                    . $cita["empleado_id"]
                    . "\n";

                echo "Cliente: "
                    . $cita["cliente"]
                    . "\n";

                echo "Día: "
                    . $cita["dia"]
                    . "\n";

                echo "Hora: "
                    . $cita["hora"]
                    . ":00\n";

                echo "Duración: "
                    . $cita["duracion"]
                    . " hora(s)\n";

                echo "Total: $"
                    . number_format(
                        $cita["total"],
                        0,
                        ",",
                        "."
                    )
                    . "\n";

                echo "Servicios: "
                    . implode(
                        ", ",
                        $cita["servicios"]
                    )
                    . "\n";
            }

            echo "\n";
            echo "Los datos de prueba fueron cargados correctamente.\n";
        }
    }


    /* PUNTO 8 - SALIR */

    elseif ($opcion == "8") {

        echo "\n";
        echo "           PROGRAMA FINALIZADO\n";
    }


    /* OPCIÓN NO VÁLIDA */

    else {

        echo "\n";
        echo "Opción no válida.\n";
    }


} while ($opcion != "8");

?>