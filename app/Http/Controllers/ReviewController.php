<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    /////////////////////////////////////////////////////////////////////////////////
    ////////<------------------- CREATE A NEW REVIEW ------------------>/////////////
    /////////////////////////////////////////////////////////////////////////////////

    public function createReview(Request $request)
    {
        try {
            Log::info("Creating a review");

            $validator = Validator::make($request->all(), [

                'book_id' => ['required', 'integer'],
                'review_title' => ['required', 'string'],
                'score' => ['required', 'integer', 'min:1', 'max:10'],
                'message' => ['required', 'string']
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

            $bookId = $request->input('book_id');
            $reviewTitle = $request->input('review_title');
            $score = $request->input('score');
            $message = $request->input('message');


            $review = new Review();

            $review->user_id = $userId;
            $review->book_id = $bookId;
            $review->review_title = $reviewTitle;
            $review->score = $score;
            $review->message = $message;

            $review->save();

            return response()->json(
                [
                    'success' => true,
                    'message' => "Review " . $reviewTitle . " created"
                ],
                200
            );
        } catch (\Exception $exception) {
            Log::error("Error creating " . $reviewTitle . ", " . $exception->getMessage());

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
    ///////////<------------------- SHOW ALL REVIEWS ------------------>/////////////
    /////////////////////////////////////////////////////////////////////////////////

    public function showAllReviews()
    {
        try {
            Log::info("Getting all reviews");

            $reviews = Review::query()
                ->join('users', 'reviews.user_id', '=', 'users.id',)
                ->join('books', 'reviews.book_id', '=', 'books.id')
                ->select(
                    'reviews.id',
                    'users.name',
                    'books.title',
                    'books.synopsis',
                    'reviews.review_title',
                    'reviews.score',
                    'reviews.message',
                    'books.book_cover'
                )
                ->get()
                ->toArray();

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Reviews retrieved successfully',
                    'data' => $reviews
                ],
                200
            );
        } catch (\Exception $exception) {

            Log::error("Error getting the reviews: " . $exception->getMessage());

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
    //////////<------------------- EDIT REVIEW BY ID------------------>//////////////
    /////////////////////////////////////////////////////////////////////////////////

    public function editReviewById(Request $request, $id)
    {
        try {
            Log::info('Updating a review');

            $validator = Validator::make($request->all(), [
                'book_id' => 'integer',
                'review_title' => 'string',
                'score' => 'integer',
                'message' => 'string'
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

            $review = Review::query()->where('user_id', '=', $userId)->find($id);

            if (!$review) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => "Review doesn't exists"
                    ],
                    404
                );
            }

            $bookId = $request->input('book_id');
            $reviewTitle = $request->input('review_title');
            $score = $request->input('score');
            $message = $request->input('message');


            if (isset($bookId)) {
                $review->book_id = $bookId;
            };
            if (isset($synopsis)) {
                $review->review_title = $reviewTitle;
            };
            if (isset($score)) {
                $review->score = $score;
            };
            if (isset($message)) {
                $review->message = $message;
            };

            $review->save();

            return response()->json(
                [
                    'success' => true,
                    'message' => "Review " . $id . " changed"
                ],
                200
            );
        } catch (\Exception $exception) {

            Log::error("Error modifing the review: " . $exception->getMessage());

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
    /////////////<------------------- DELETE REVIEW ------------------>//////////////
    /////////////////////////////////////////////////////////////////////////////////

    public function deleteReview($id)
    {
        try {
            Log::info('Deleting a review');

            $userId = auth()->user()->id;

            $review = Review::query()
            ->where("user_id", "=", $userId)
            ->find($id);

            if (!$review) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => "You can't delete this review"
                    ],
                    404
                );
            }

            $review->delete($id);

            return response()->json(
                [
                    'success' => true,
                    'message' => "Review " . $id . " deleted"
                ],
                200
            );
        } catch (\Exception $exception) {

            Log::error("Error deleting the review: " . $exception->getMessage());

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
    //////////<---------------- SEARCH REVIEW BY USER ID ---------------->///////////
    /////////////////////////////////////////////////////////////////////////////////

    public function searchReviewByUserName($name)
    {
        try {
            Log::info("Getting filtered reviews by user name");

            $review = Review::query()
                ->join('users', 'reviews.user_id', '=', 'users.id',)
                ->join('books', 'reviews.book_id', '=', 'books.id')
                ->select(
                    'reviews.id',
                    'users.name',
                    'books.title',
                    'books.synopsis',
                    'reviews.review_title',
                    'reviews.score',
                    'reviews.message',
                    'books.book_cover'
                )
                ->where('name', '=', $name)
                ->get()
                ->toArray();

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Reviews retrieved successfully',
                    'data' => $review
                ],
                200
            );
        } catch (\Exception $exception) {

            Log::error("Error getting the reviews: " . $exception->getMessage());

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
    /////////<------------- SHOW REVIEWS BY DESCENDENT ORDER ------------->//////////
    /////////////////////////////////////////////////////////////////////////////////

    public function showReviewsOrderedByScoreDesc()
    {
        try {
            Log::info("Getting all reviews ordered descendent");

            $reviews = Review::query()
                ->join('users', 'reviews.user_id', '=', 'users.id',)
                ->join('books', 'reviews.book_id', '=', 'books.id')
                ->select(
                    'reviews.id',
                    'users.name',
                    'books.title',
                    'books.synopsis',
                    'reviews.review_title',
                    'reviews.score',
                    'reviews.message',
                    'books.book_cover'
                )
                ->orderBy('score', 'desc')
                ->get()
                ->toArray();

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Reviews retrieved successfully',
                    'data' => $reviews
                ],
                200
            );
        } catch (\Exception $exception) {

            Log::error("Error getting the reviews: " . $exception->getMessage());

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
    /////////<------------- SHOW REVIEWS BY ASCENDENT ORDER ------------->///////////
    /////////////////////////////////////////////////////////////////////////////////

    public function showReviewsOrderedByScoreAsc()
    {
        try {
            Log::info("Getting all reviews ordered ascendent");

            $reviews = Review::query()
                ->join('users', 'reviews.user_id', '=', 'users.id',)
                ->join('books', 'reviews.book_id', '=', 'books.id')
                ->select(
                    'reviews.id',
                    'users.name',
                    'books.title',
                    'books.synopsis',
                    'reviews.review_title',
                    'reviews.score',
                    'reviews.message',
                    'books.book_cover'
                )
                ->orderBy('score', 'asc')
                ->get()
                ->toArray();

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Reviews retrieved successfully',
                    'data' => $reviews
                ],
                200
            );
        } catch (\Exception $exception) {

            Log::error("Error getting the reviews: " . $exception->getMessage());

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
    //////////<---------------- SEARCH REVIEW BY BOOK ID ---------------->///////////
    /////////////////////////////////////////////////////////////////////////////////

    public function searchReviewByBookId($id)
    {
        try {
            Log::info("Getting filtered reviews by book title");

            $review = Review::query()
                ->join('users', 'reviews.user_id', '=', 'users.id',)
                ->join('books', 'reviews.book_id', '=', 'books.id')
                ->select(
                    'reviews.id',
                    'reviews.book_id',
                    'users.name',
                    'books.title',
                    'books.synopsis',
                    'reviews.review_title',
                    'reviews.score',
                    'reviews.message',
                    'books.book_cover'
                )
                ->where('book_id', '=', $id)
                ->get()
                ->toArray();

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Reviews retrieved successfully',
                    'data' => $review
                ],
                200
            );
        } catch (\Exception $exception) {

            Log::error("Error getting the reviews: " . $exception->getMessage());

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
