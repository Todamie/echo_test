<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArticleController extends Controller
{
    /**
     * @OA\Get(
     *     path="/articles",
     *     tags={"Articles"},
     *     summary="Получить список статей с их категориями и автором",
     *     @OA\Response(
     *         response=200,
     *         description="Успешный ответ",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Article")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $articles = Article::with('categories', 'author')
            ->latest()
            ->orderBy('title', 'asc')
            ->paginate(20);

        if ($articles->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Статьи не найдены',
            ], 404);
        } else {
            return response()->json([
                'status' => 'success',
                'data' => $articles,
            ], 200);
        }
    }

    /**
     * @OA\Post(
     *     path="/articles/search",
     *     tags={"Articles"},
     *     summary="Поиск статей по названию/категории/автору",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 required={"search_article"},
     *                 @OA\Property(
     *                     property="search_article",
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
     *             @OA\Items(ref="#/components/schemas/Article")
     *         )
     *     )
     * )
     */
    public function search(Request $request)
    {
        if (!$request->has('search_article') || empty($request->search_article)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Поисковый запрос не может быть пустым',
            ], 400);
        }

        $searchTerm = $request->search_article;

        $searchTerm = trim($request->search_article);

        //запрос
        $exactMatch = Article::with('categories', 'author')
            ->where('title', $searchTerm)
            ->orWhereHas('categories', function ($query) use ($searchTerm) {
                $query->where('name', $searchTerm);
            })
            ->orWhereHas('author', function ($query) use ($searchTerm) {
                $query->where('last_name', $searchTerm)
                ->orWhere('first_name', $searchTerm)
                ->orWhere('middle_name', $searchTerm);
            })
            ->select('*', DB::raw("1 as priority"));

        //запрос*
        $startWithMatch = Article::with('categories', 'author')
            ->where('title', 'like', $searchTerm . '%')
            ->whereNotIn('id', $exactMatch->pluck('id'))
            ->orWhereHas('categories', function ($query) use ($searchTerm) {
                $query->where('name', 'like', $searchTerm . '%');
            })
            ->orWhereHas('author', function ($query) use ($searchTerm) {
                $query->where('last_name', 'like', $searchTerm . '%')
                ->orWhere('first_name', 'like', $searchTerm . '%')
                ->orWhere('middle_name', 'like', $searchTerm . '%');
            })
            ->select('*', DB::raw("2 as priority"));

        //*запрос*
        $anywhereMatch = Article::with('categories', 'author')
            ->where('title', 'like', '%' . $searchTerm . '%')
            ->whereNotIn('id', $exactMatch->pluck('id'))
            ->whereNotIn('id', $startWithMatch->pluck('id'))
            ->orWhereHas('categories', function ($query) use ($searchTerm) {
                $query->where('name', 'like', '%' . $searchTerm . '%');
            })
            ->orWhereHas('author', function ($query) use ($searchTerm) {
                $query->where('last_name', 'like', '%' . $searchTerm . '%')
                ->orWhere('first_name', 'like', '%' . $searchTerm . '%')
                ->orWhere('middle_name', 'like', '%' . $searchTerm . '%');
            })
            ->select('*', DB::raw("3 as priority"));

        $articles = $exactMatch
            ->union($startWithMatch)
            ->union($anywhereMatch)
            ->orderBy('priority')
            ->latest()
            ->orderBy('title')
            ->paginate(20);

        if ($articles->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Статья не найдена',
            ], 404);
        } else {
            return response()->json([
                'status' => 'success',
                'data' => $articles,
            ], 200);
        }
    }

    /**
     * @OA\Get(
     *     path="/articles/{idOrSlug}",
     *     tags={"Articles"},
     *     summary="Выводит статью по id или slug",
     *     @OA\Parameter(
     *         name="idOrSlug",
     *         in="path",
     *         required=true,
     *         description="ID или slug статьи",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Успешный ответ",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Article")
     *         ),
     *     )
     * )
     */
    public function show($idOrSlug)
    {
        $article = Article::with('categories', 'author')
            ->where('id', trim($idOrSlug))
            ->orWhere('slug', trim($idOrSlug))
            ->first();

        if (empty($article)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Статья не найдена',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $article,
        ], 200);
    }
}
