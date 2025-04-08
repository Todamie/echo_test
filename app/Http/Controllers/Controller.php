<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="API для статей",
 *     description="API для статей, авторов и категорий",
 * )
 *
 * @OA\Server(
 *     url="/api",
 * ),
 * @OA\Tag(
 *     name="Articles",
 *     description="API Статей"
 * ),
 * @OA\Tag(
 *     name="Categories",
 *     description="API Категорий"
 * ),
 * @OA\Tag(
 *     name="Authors",
 *     description="API Авторов"
 * ),
 * @OA\Tag(
 *     name="Image",
 *     description="API Изображений"
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    //
}
