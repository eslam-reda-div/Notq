<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(title="API Documentation", version="1.0.0")
 *
 * @OA\Response(
 *     response="UnauthorizedResponse",
 *     description="Unauthenticated",
 *
 *     @OA\JsonContent(
 *
 *         @OA\Property(property="message", type="string", example="Unauthenticated")
 *     )
 * )
 */
abstract class Controller
{
    //
}
