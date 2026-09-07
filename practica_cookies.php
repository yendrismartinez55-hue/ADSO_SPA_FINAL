<?php

interface Reportable
{
    public function reportarTrabajo();
}


// CLASE BASE

class Aprendiz implements Reportable
{
    // Encapsulamiento: propiedades protegidas
    protected string $nombre;
    protected int $edad;
    protected string $ficha;

    // Constructor
    public function __construct(string $nombre, int $edad, string $ficha)
    {
        // $this hace referencia al objeto actual
        $this->nombre = $nombre;
        $this->edad = $edad;
        $this->ficha = $ficha;
    }

    // Métodos para obtener la información
    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function getEdad(): int
    {
        return $this->edad;
    }

    public function getFicha(): string
    {
        return $this->ficha;
    }

    // Método de la interfaz
    public function reportarTrabajo()
    {
        return "Realiza actividades de aprendizaje.";
    }

    // Información general
    public function mostrarInformacion(): void
    {
        echo "Nombre: " . $this->nombre . "\n";
        echo "Edad: " . $this->edad . "\n";
        echo "Ficha: " . $this->ficha . "\n";
    }
}


// CLASE BACKEND

class Backend extends Aprendiz
{
    private string $lenguaje;

    public function __construct(
        string $nombre,
        int $edad,
        string $ficha,
        string $lenguaje
    ) {
        // Constructor de la clase padre
        parent::__construct($nombre, $edad, $ficha);

        $this->lenguaje = $lenguaje;
    }

    public function getLenguaje(): string
    {
        return $this->lenguaje;
    }

    // Polimorfismo:
    // Backend modifica el comportamiento del método
    public function reportarTrabajo()
    {
        return "Desarrolla la lógica del sistema utilizando " .
               $this->lenguaje . " y trabaja con bases de datos.";
    }

    public function mostrarInformacion(): void
    {
        parent::mostrarInformacion();

        echo "Área: Backend" . "\n";
        echo "Lenguaje: " . $this->lenguaje . "\n";
        echo "Trabajo: " . $this->reportarTrabajo() . "\n";
    }
}


// CLASE FRONTEND

class Frontend extends Aprendiz
{
    private string $tecnologia;

    public function __construct(
        string $nombre,
        int $edad,
        string $ficha,
        string $tecnologia
    ) {
        parent::__construct($nombre, $edad, $ficha);

        $this->tecnologia = $tecnologia;
    }

    public function getTecnologia(): string
    {
        return $this->tecnologia;
    }

    // Polimorfismo:
    // Frontend modifica el comportamiento del método
    public function reportarTrabajo()
    {
        return "Diseña y desarrolla la interfaz del sistema utilizando " .
               $this->tecnologia . ".";
    }

    public function mostrarInformacion(): void
    {
        parent::mostrarInformacion();

        echo "Área: Frontend" . "\n";
        echo "Tecnología: " . $this->tecnologia . "\n";
        echo "Trabajo: " . $this->reportarTrabajo() . "\n";
    }
}


// CLASE EQUIPO

class Equipo
{
    // Colección donde se almacenan los aprendices
    private array $integrantes = [];

    // AGREGAR INTEGRANTE

    public function agregarIntegrante(Aprendiz $aprendiz): void
    {
        $this->integrantes[] = $aprendiz;
    }

    // MOSTRAR TODOS LOS INTEGRANTES

    public function mostrarIntegrantes(): void
    {
        echo "\n";
        echo "           REPORTE DEL EQUIPO" . "\n";

        foreach ($this->integrantes as $integrante) {

            echo PHP_EOL;
            echo "--------------------------------------------" ."\n";

            // Polimorfismo:
            // PHP ejecuta reportarTrabajo() dependiendo
            // del tipo real del objeto.
            $integrante->mostrarInformacion();

            echo "--------------------------------------------" . "\n";
        }
    }

    // CONTAR INTEGRANTES

    public function contarIntegrantes(): int
    {
        return count($this->integrantes);
    }

    // FILTRAR POR EDAD

    public function filtrarPorEdad(int $edadMinima): array
    {
        $resultado = [];

        foreach ($this->integrantes as $integrante) {

            if ($integrante->getEdad() >= $edadMinima) {
                $resultado[] = $integrante;
            }
        }

        return $resultado;
    }

    // ORDENAR POR NOMBRE

    public function ordenarPorNombre(): void
    {
        usort(
            $this->integrantes,
            function ($a, $b) {
                return strcmp(
                    $a->getNombre(),
                    $b->getNombre()
                );
            }
        );
    }

    // MOSTRAR FILTRADOS

    public function mostrarLista(array $lista): void
    {
        foreach ($lista as $integrante) {

            echo "\n";
            echo "Nombre: " . $integrante->getNombre() . "\n";
            echo "Edad: " . $integrante->getEdad() . "\n";
            echo "Ficha: " . $integrante->getFicha() . "\n";
            echo "Trabajo: " . $integrante->reportarTrabajo() . "\n";
        }
    }
}


// CREACIÓN DE OBJETOS


// Objetos Backend
$aprendiz1 = new Backend(
    "Carlos",
    20,
    "ADSO-3174404",
    "PHP"
);

$aprendiz2 = new Backend(
    "Laura",
    22,
    "ADSO-3174404",
    "PHP"
);

// Objetos Frontend
$aprendiz3 = new Frontend(
    "Andrés",
    19,
    "ADSO-3174404",
    "HTML, CSS y JavaScript"
);

$aprendiz4 = new Frontend(
    "Mariana",
    21,
    "ADSO-3174404",
    "Blazor"
);


// CREAR EQUIPO

$equipo = new Equipo();


// AGREGAR APRENDICES A LA COLECCIÓN

$equipo->agregarIntegrante($aprendiz1);
$equipo->agregarIntegrante($aprendiz2);
$equipo->agregarIntegrante($aprendiz3);
$equipo->agregarIntegrante($aprendiz4);


// MOSTRAR REPORTE GENERAL

echo "\n";
echo "       SISTEMA DE EQUIPO ADSO\n";

$equipo->mostrarIntegrantes();


// CONTAR INTEGRANTES

echo "\n";
echo "       CANTIDAD DE INTEGRANTES\n";

echo "Total de integrantes: " .
     $equipo->contarIntegrantes() . PHP_EOL;


// FILTRAR INTEGRANTES


echo "\n";
echo "       FILTRO: MAYORES DE 20 AÑOS\n" ;


$filtrados = $equipo->filtrarPorEdad(20);

$equipo->mostrarLista($filtrados);


// ORDENAR INTEGRANTES


echo "\n";
echo "       EQUIPO ORDENADO POR NOMBRE\n";

$equipo->ordenarPorNombre();

$equipo->mostrarIntegrantes();


/*
Qué cumple este proyecto?
Requisito              Dónde está                                               
Clase base**            class Aprendiz                                       
Objetos                 $aprendiz1 $aprendiz2, $aprendiz3, $aprendiz4   
Constructor             __construct()                                         
$this                   $this->nombre, $this->edad, this->ficha           
Encapsulamiento         protected y private                                 
Herencia                Backend extends Aprendiz y Frontend extends Aprendiz 
Interfaz                interface Reportable                                 
Implementación          implements Reportable                                 
Polimorfismo            reportarTrabajo() en Backend y Frontend               
Colección               private array $integrantes = [];                     
Agregar                 agregarIntegrante()                                 
Mostrar                 mostrarIntegrantes()                                  
Contar                  contarIntegrantes()                                    
Filtrar                 filtrarPorEdad()                                    
Ordenar                 ordenarPorNombre()                                    
Recorrer colección      foreach                                         



El programa primero crea los aprendices, luego los guarda en la colección y finalmente muestra el reporte. También ejecuta las operaciones de ontar, filtrar y ordenar.

El polimorfismo está principalmente en reportarTrabajo(). Aunque todos los objetos están almacenados dentro de la misma colección como Aprendiz, cuando se llama a reportarTrabajo(), un objeto Backend responde como Backend y uno Frontend responde como Frontend.

*/
?>

