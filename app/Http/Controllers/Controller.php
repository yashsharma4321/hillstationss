<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(title: "Property Portal API", version: "1.0.0", description: "API documentation for the Property Portal backend")]
#[OA\SecurityScheme(securityScheme: "bearerAuth", type: "http", scheme: "bearer", bearerFormat: "JWT")]
abstract class Controller
{
    //
}
