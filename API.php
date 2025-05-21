<?php
declare(strict_types=1);


$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$parts = explode('/', $_SERVER['REQUEST_URI']);

// ------------------------------------
// 1) PSR-4 Autoloader for SmartKey\
// ------------------------------------
spl_autoload_register(function (string $class): void {
    require __DIR__ . '/src/' . $class . '.php';
    
});

// --------- here we handle the errors;
set_error_handler("ErrorHandler::handleError");
// --------- here we handle exceptions;
set_exception_handler("ErrorHandler::handleException");
header("Content-Type: application/json; charset=UTF-8");

// Find “/api/”
$pos = strpos($uri, '/api/');
if ($pos === false) {
    http_response_code(404);
    echo json_encode(['error' => 'Invalid API path']);
    exit;
}


// Grab everything after “/api/”
$endpoint = substr($uri, $pos + 5);
$endpoint = explode('/', trim($endpoint, '/'))[0] ?? '';

if ($endpoint === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Missing endpoint']);
    exit;
}

// // Now point to src/{endpoint}.php
// $target = __DIR__ . "/src/{$endpoint}.php";
// if (file_exists($target)) {
//     include $target;
// } else {
//     http_response_code(404);
//     echo json_encode(['error' => 'Endpoint not found']);
//     exit;
// }



$database = new Database(
    host: 'localhost',
    name: 'smart_key',
    user: 'root',  
    password: ''
);

// Check if the connection is successful
$connection = $database->getConnection();
if ($connection === null) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}


switch ($endpoint) {
    case 'users':
        UsersProcess($database,$parts);
        break;

    case 'customers':
        CustomersProcess($database,$parts);
        break;

    case 'vehicles':
        VehiclesProcess($database,$parts);
        break;

    case 'rentalbooking':
        RentalBookingProcess($database,$parts);
        break;

    case 'fueltype':
        FuleTypeProcess($database,$parts);
        break;

    case 'vehiclecategories':
        VehicleCategoriesProcess($database,$parts);
        break;

    default:
        http_response_code(404);
        echo json_encode(['error' => 'Invalid endpoint']);
        exit;
}   


function CustomersProcess($database,$parts){
    $gatway = new CustomersGatway($database);
    $Customers = new Customers($gatway);
    $Customers->processRequest($_SERVER["REQUEST_METHOD"], $parts[5]);
}

function UsersProcess($database,$parts){
    $gatway = new UsersGatway($database);
    $Users = new Users($gatway);
    $Users->processRequest($_SERVER["REQUEST_METHOD"], $parts[5]);
}

function VehiclesProcess($database,$parts){
    $gatway = new VehiclesGatway($database);
    $Vehicles = new Vehicles($gatway);
    $Vehicles->processRequest($_SERVER["REQUEST_METHOD"], $parts[5]);
   
}

function RentalBookingProcess($database,$parts){
    $gatway = new RentalbookingGatway($database);
    $RentalBooking = new RentalBooking($gatway);
    $RentalBooking->processRequest($_SERVER["REQUEST_METHOD"], $parts[5]);
}

function FuleTypeProcess($database,$parts){
    $gatway = new FuelTypeGatway($database);
    $FuelType = new FuelType($gatway);
    $FuelType->processRequest($_SERVER["REQUEST_METHOD"], $parts[5]);
}

function VehicleCategoriesProcess($database,$parts){
    $gatway = new VehicleCategoriesGatway($database);
    $VehicleCategories = new VehicleCategories($gatway);
    $VehicleCategories->processRequest($_SERVER["REQUEST_METHOD"], $parts[5]);
}

// $UsersControl = new Users;
// $UsersControl->processRequest($_SERVER["REQUEST_METHOD"], $parts[5]);




// var_dump($parts[4]);