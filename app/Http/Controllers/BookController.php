<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
{
    /////////////////////////////////////////////////////////////////////////////////
    /////////<------------------- CREATE A NEW BOOK ------------------>//////////////
    /////////////////////////////////////////////////////////////////////////////////

    public function createBook(Request $request)
    {
        try {
            Log::info("Creating a book");

            $validator = Validator::make($request->all(), [
                'title' => ['required', 'string', 'unique:books'],
                'synopsis' => ['required', 'string'],
                'series' => ['required', 'string'],
                'author' => ['required', 'string'],
                'genre' => ['required', 'string'],
                'year' => ['required', 'string'],
                'book_cover' => ['required', 'string'],
                'author_wiki_url' => ['required', 'string'],
                'shop_url' => ['required', 'string']
            ]);

            if ($validator->fails()) {
                return response()->json(
                    [
                        "success" => false,
                        "message" => $validator->errors()
                    ],
                    400
                );
            };

            $userId = auth()->user()->id;

            $title = $request->input('title');
            $synopsis = $request->input('synopsis');
            $series = $request->input('series');
            $author = $request->input('author');
            $genre = $request->input('genre');
            $year = $request->input('year');
            $bookCover = $request->input('book_cover');
            $authorWikiUrl = $request->input('author_wiki_url');
            $shopUrl = $request->input('shop_url');

            $book = new Book();

            $book->user_id = $userId;
            $book->title = $title;
            $book->synopsis = $synopsis;
            $book->series = $series;
            $book->author = $author;
            $book->genre = $genre;
            $book->year = $year;
            $book->book_cover = $bookCover;
            $book->author_wiki_url = $authorWikiUrl;
            $book->shop_url = $shopUrl;

            $book->save();

            return response()->json(
                [
                    'success' => true,
                    'message' => "book " . $title . " created"
                ],
                200
            );
        } catch (\Exception $exception) {
            Log::error("Error creating " . $title . ", " . $exception->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => $exception->getMessage()
                ],
                500
            );
        }
    }

    /////////////////////////////////////////////////////////////////////////////////
    ////////////<------------------- SHOW ALL BOOKS ------------------>//////////////
    /////////////////////////////////////////////////////////////////////////////////

    public function showAllBooks()
    {
        try {
            Log::info("Getting all books");

            $books = Book::query()
                ->join('users', 'books.user_id', '=', 'users.id')
                ->select(
                    'books.id',
                    'users.name',
                    'books.title',
                    'books.synopsis',
                    'books.series',
                    'books.author',
                    'books.genre',
                    'books.year',
                    'books.book_cover',
                    'books.author_wiki_url',
                    'books.shop_url'
                )
                ->orderBy('title', 'asc')
                ->get()
                ->toArray();

            return response()->json(
                [
                    'success' => true,
                    'message' => 'books retrieved successfully',
                    'data' => $books
                ],
                200
            );
        } catch (\Exception $exception) {

            Log::error("Error getting books: " . $exception->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => $exception->getMessage()
                ],
                500
            );
        }
    }

    /////////////////////////////////////////////////////////////////////////////////
    /////////<------------------- EDIT BOOK BY ID------------------>//////////////
    /////////////////////////////////////////////////////////////////////////////////

    public function editBookById(Request $request, $id)
    {
        try {
            Log::info('Updating Book');

            $validator = Validator::make($request->all(), [
                'title' => ['string', 'unique:books'],
                'synopsis' => 'string',
                'series' => 'string',
                'author' => 'string',
                'genre' => 'string',
                'year' => 'string',
                'book_cover' => 'string',
                'author_wiki_url' => 'string',
                'shop_url' => 'string'
            ]);

            if ($validator->fails()) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => $validator->errors()
                    ],
                    400
                );
            }

            $userId = auth()->user()->id;

            $book = Book::query()->where('user_id', '=', $userId)->find($id);

            if (!$book) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => "Book doesn't exists"
                    ],
                    404
                );
            }

            $title = $request->input('title');
            $synopsis = $request->input('synopsis');
            $series = $request->input('series');
            $author = $request->input('author');
            $genre = $request->input('genre');
            $year = $request->input('year');
            $bookCover = $request->input('book_cover');
            $authorWikiUrl = $request->input('author_wiki_url');
            $shopUrl = $request->input('shop_url');

            if (isset($title)) {
                $book->title = $title;
            };
            if (isset($synopsis)) {
                $book->synopsis = $synopsis;
            };
            if (isset($series)) {
                $book->series = $series;
            };
            if (isset($author)) {
                $book->author = $author;
            };
            if (isset($genre)) {
                $book->genre = $genre;
            };
            if (isset($year)) {
                $book->year = $year;
            };
            if (isset($bookCover)) {
                $book->book_cover = $bookCover;
            };
            if (isset($authorWikiUrl)) {
                $book->author_wiki_url = $authorWikiUrl;
            };
            if (isset($shopUrl)) {
                $book->shop_url = $shopUrl;
            };

            $book->save();

            return response()->json(
                [
                    'success' => true,
                    'message' => "Book " . $id . " changed"
                ],
                200
            );
        } catch (\Exception $exception) {

            Log::error("Error modifing the book: " . $exception->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => $exception->getMessage()
                ],
                500
            );
        }
    }

    /////////////////////////////////////////////////////////////////////////////////
    //////////////<------------------- DELETE BOOK ------------------>///////////////
    /////////////////////////////////////////////////////////////////////////////////

    public function deleteBook($id)
    {
        try {
            Log::info('Deleting book');

            $userId = auth()->user()->id;

            $book = Book::query()
                ->where('user_id', '=', $userId)
                ->find($id);

            if (!$book) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => "You can't delete this book"
                    ],
                    404
                );
            }

            $book->delete($id);

            return response()->json(
                [
                    'success' => true,
                    'message' => "Book " . $id . " deleted"
                ],
                200
            );
        } catch (\Exception $exception) {

            Log::error("Error deleting the book: " . $exception->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => $exception->getMessage()
                ],
                500
            );
        }
    }

    /////////////////////////////////////////////////////////////////////////////////
    ////////<------------------- SEARCH BOOK BY TITLE ------------------>///////////
    /////////////////////////////////////////////////////////////////////////////////

    public function searchBookByTitle($title)
    {
        try {
            Log::info("Getting filtered books by title");

            $book = Book::query()
                ->join('users', 'books.user_id', '=', 'users.id')
                ->select(
                    'books.id',
                    'users.name',
                    'books.title',
                    'books.synopsis',
                    'books.series',
                    'books.author',
                    'books.genre',
                    'books.year',
                    'books.book_cover',
                    'books.author_wiki_url',
                    'books.shop_url'
                )
                ->where('title', '=', $title)
                ->get()
                ->toArray();

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Books retrieved successfully',
                    'data' => $book
                ],
                200
            );
        } catch (\Exception $exception) {

            Log::error("Error getting books: " . $exception->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => $exception->getMessage()
                ],
                500
            );
        }
    }

    /////////////////////////////////////////////////////////////////////////////////
    ////////<------------------- SEARCH BOOKS BY AUTHOR ------------------>//////////
    /////////////////////////////////////////////////////////////////////////////////

    public function searchBooksByAuthor($author)
    {
        try {
            Log::info("Getting filtered books by author");

            $book = Book::query()
                ->join('users', 'books.user_id', '=', 'users.id')
                ->select(
                    'books.id',
                    'users.name',
                    'books.title',
                    'books.synopsis',
                    'books.series',
                    'books.author',
                    'books.genre',
                    'books.year',
                    'books.book_cover',
                    'books.author_wiki_url',
                    'books.shop_url'
                )
                ->where('author', '=', $author)
                ->orderBy('title', 'asc')
                ->get()
                ->toArray();

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Books retrieved successfully',
                    'data' => $book
                ],
                200
            );
        } catch (\Exception $exception) {

            Log::error("Error getting books: " . $exception->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => $exception->getMessage()
                ],
                500
            );
        }
    }

    /////////////////////////////////////////////////////////////////////////////////
    ////////<------------------- SEARCH BOOKS BY SERIES ------------------>//////////
    /////////////////////////////////////////////////////////////////////////////////

    public function searchBooksBySeries($series)
    {
        try {
            Log::info("Getting filtered books by series");

            $book = Book::query()
                ->join('users', 'books.user_id', '=', 'users.id')
                ->select(
                    'books.id',
                    'users.name',
                    'books.title',
                    'books.synopsis',
                    'books.series',
                    'books.author',
                    'books.genre',
                    'books.year',
                    'books.book_cover',
                    'books.author_wiki_url',
                    'books.shop_url'
                )
                ->where('series', '=', $series)
                ->orderBy('title', 'asc')
                ->get()
                ->toArray();

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Books retrieved successfully',
                    'data' => $book
                ],
                200
            );
        } catch (\Exception $exception) {

            Log::error("Error getting books: " . $exception->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => $exception->getMessage()
                ],
                500
            );
        }
    }

    /////////////////////////////////////////////////////////////////////////////////
    ////////<------------------- SEARCH BOOKS BY GENRE ------------------>///////////
    /////////////////////////////////////////////////////////////////////////////////

    public function searchBooksByGenre($genre)
    {
        try {
            Log::info("Getting filtered books by genre");

            $book = Book::query()
                ->join('users', 'books.user_id', '=', 'users.id')
                ->select(
                    'books.id',
                    'users.name',
                    'books.title',
                    'books.synopsis',
                    'books.series',
                    'books.author',
                    'books.genre',
                    'books.year',
                    'books.book_cover',
                    'books.author_wiki_url',
                    'books.shop_url'
                )
                ->where('genre', '=', $genre)
                ->orderBy('author', 'asc')
                ->get()
                ->toArray();

            return response()->json(
                [
                    'success' => true,
                    'message' => 'books retrieved successfully',
                    'data' => $book
                ],
                200
            );
        } catch (\Exception $exception) {

            Log::error("Error getting books: " . $exception->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => $exception->getMessage()
                ],
                500
            );
        }
    }

    /////////////////////////////////////////////////////////////////////////////////
    //////////<------------------- SEARCH BOOK BY YEAR ------------------>///////////
    /////////////////////////////////////////////////////////////////////////////////

    public function searchBookByYear($year)
    {
        try {
            Log::info("Getting filtered books by year");

            $book = Book::query()
                ->join('users', 'books.user_id', '=', 'users.id')
                ->select(
                    'books.id',
                    'users.name',
                    'books.title',
                    'books.synopsis',
                    'books.series',
                    'books.author',
                    'books.genre',
                    'books.year',
                    'books.book_cover',
                    'books.author_wiki_url',
                    'books.shop_url'
                )
                ->where('year', '=', $year)
                ->orderBy('title', 'asc')
                ->get()
                ->toArray();

            return response()->json(
                [
                    'success' => true,
                    'message' => 'books retrieved successfully',
                    'data' => $book
                ],
                200
            );
        } catch (\Exception $exception) {

            Log::error("Error getting books: " . $exception->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => $exception->getMessage()
                ],
                500
            );
        }
    }
}
