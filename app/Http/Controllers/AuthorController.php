<?php

namespace App\Http\Controllers;

use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthorController extends Controller
{

    /**
     * @OA\Get(
     *     path="/authors",
     *     tags={"Authors"},
     *     summary="Получить список авторов",
     *     @OA\Response(
     *         response=200,
     *         description="Успешный ответ",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Author")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $authors = Author::query()
            ->orderBy('last_name', 'asc')
            ->orderBy('first_name', 'asc')
            ->orderBy('middle_name', 'asc')
            ->paginate(20);

        if ($authors->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Авторы не найдены',
            ], 404);
        } else {
            return response()->json([
                'status' => 'success',
                'data' => $authors,
            ], 200);
        }
    }

    /**
     * @OA\Post(
     *     path="/authors/search",
     *     tags={"Authors"},
     *     summary="Поиск авторов по фамилии",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 required={"search_author"},
     *                 @OA\Property(
     *                     property="search_author",
     *                     type="string",
     *                     description="Запрос для поиска",
     *                     example="test"
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Успешный ответ",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Author")
     *         )
     *     )
     * )
     */
    public function search(Request $request)
    {
        if (!$request->has('search_author') || empty($request->search_author)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Поисковый запрос не может быть пустым',
            ], 400);
        }

        $searchTerm = trim($request->search_author);

        $exactMatch = Author::query()
            ->where('last_name', $searchTerm)
            ->select('*', DB::raw("1 as priority"));

        $startWithMatch = Author::query()
            ->where('last_name', 'like', $searchTerm . '%')
            ->whereNotIn('id', $exactMatch->pluck('id'))
            ->select('*', DB::raw("2 as priority"));

        $anywhereMatch = Author::query()
            ->where('last_name', 'like', '%' . $searchTerm . '%')
            ->whereNotIn('id', $exactMatch->pluck('id'))
            ->whereNotIn('id', $startWithMatch->pluck('id'))
            ->select('*', DB::raw("3 as priority"));

        $authors = $exactMatch
            ->union($startWithMatch)
            ->union($anywhereMatch)
            ->orderBy('priority')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->orderBy('middle_name')
            ->paginate(20);

        if ($authors->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Автор не найден',
            ], 404);
        } else {
            return response()->json([
                'status' => 'success',
                'data' => $authors,
            ], 200);
        }
    }

    /**
     * @OA\Get(
     *     path="/authors/{idOrSlug}",
     *     tags={"Authors"},
     *     summary="Получить автора по id или slug",
     *     @OA\Parameter(
     *         name="idOrSlug",
     *         in="path",
     *         required=true,
     *         description="ID или slug автора",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Успешный ответ",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Author")
     *         )
     *     )
     * )
     */
    public function show($idOrSlug)
    {
        $author = Author::where('id', trim($idOrSlug))
            ->orWhere('slug', trim($idOrSlug))
            ->first();

        if (empty($author)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Автор не найден',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $author,
        ], 200);
    }
}
