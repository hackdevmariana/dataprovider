<?php

namespace App\Http\Controllers;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="DataProvider API",
 *     description="API completa para el sistema DataProvider con gestión de datos geográficos, energéticos, culturales y sociales."
 * )
 * 
 * @OA\Server(
 *     url="/api/v1",
 *     description="Servidor API V1"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Autenticación mediante Laravel Sanctum"
 * )
 */
abstract class Controller
{
    //
}