<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public $perPage = 10;

    /**
     * @OA\Get(
     *     path="/categories",
     *     tags={"Categories"},
     *     summary="Получить список категорий и параметрами для дерева",
     *     @OA\Response(
     *         response=200,
     *         description="Успешный ответ",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Category")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $categories = Category::query()
            ->defaultOrder()
            ->get();

        if ($categories->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Категории не найдены',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $categories,
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/categories/{idOrSlug}",
     *     tags={"Categories"},
     *     summary="Получить категорию по id или slug с потомками и предками",
     *     @OA\Parameter(
     *         name="idOrSlug",
     *         in="path",
     *         required=true,
     *         description="ID или slug категории",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Успешный ответ",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Category")
     *         )
     *     )
     * )
     */
    public function show($idOrSlug)
    {
        $category = Category::with('ancestors', 'children')
            ->where('id', trim($idOrSlug))
            ->orWhere('slug', trim($idOrSlug))
            ->first();

        if (empty($category)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Категория не найдена',
            ], 404);
        }

        // Информация о детях и потомках
        $data = [
            'category' => $category->makeHidden(['ancestors', 'children']),
            'ancestors' => $category->ancestors,
            'children' => $category->children,
        ];

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ], 200);
    }
}
