<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;
use Intervention\Image\Facades\Image;


class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Book::query()->with('genres'); // Eager load genres

        // Tìm kiếm theo từ khóa
        if ($keyword = $request->query('search')) {
            $query->search($keyword);
        }

        // Lọc theo genre_id
        if ($genreId = $request->query('genre_id')) {
            $request->validate(['genre_id' => 'integer|exists:genres,id']);
            $query->whereHas('genres', function ($q) use ($genreId) {
                $q->where('genres.id', $genreId);
            });
        }

        // Lọc theo các tham số khác
        $filters = $request->only(['published_year', 'available']);
        $query->filter($filters);

        // Xử lý số lượng sách mỗi trang
        $perPage = $request->query('per_page', 5);
         /** @var LengthAwarePaginator $books */
        $books = $query->paginate($perPage);

        // Chuyển đổi đường dẫn ảnh
        $books->setCollection(
            $books->getCollection()->transform(function ($book) {
                if ($book->cover_image) {
                    $book->cover_image = Storage::url($book->cover_image);
                }
                return $book;
            })
        );

        // Trả về JSON
        return response()->json($books);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'=>'required|string|max:255',
            'author'=>'required|string|max:255',
            'published_year'=>'required|integer|min:1000|max:9999',
            'isbn'=>'required|string|unique:books,isbn',
            'quantity'=>'required|integer|min:1',
            'description'=>'nullable|string',
            'cover_image'=>'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'genre_ids'=>'nullable|array',
            'genre_ids.*'=>'exists:genres,id'
        ]);

        $converImagePath = null;
        if($request->hasFile('cover_image')){
            $image= Image::make($request->file('cover_image'))->resize(300,400);
            $filename = uniqid().'.'.$request->file('cover_image')->extension();
            $image->save(storage_path('app/public/books/covers/'.$filename));
            $converImagePath = 'book/covers'.$filename;
        }


        $books = Book::create([
            'title'=>$request->title,
            'author'=>$request->author,
            'published_year'=>$request->published_year,
            'isbn'=>$request->isbn,
            'quantity'=>$request->quantity,
            'description'=>$request->description,
            'cover_image'=> $converImagePath
        ]);
        if($request->has('genre_ids')){
            $books->genres()->sync($request->genre_ids);
        }
        $books->load('genres');
        return response()->json(['data'=>$books,'status' =>201]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $book = Book::find($id);
        if(!$book){
            return response()->json(['error'=>'Book not found',"status"=>401]);
        }
        return response()->json($book);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,Book $book)
    {
        $request->validate([
            "title"=>'string|max:255',
            'author'=>'string|max:255',
            'published_year'=>'integer|min:1000|max:9999',
            'isbn'=>'string|unique:books,isbn,'. $book->id,
            'quantity' => 'integer|min:1',
            'description'=>'nullable|string',
            'cover_image'=>'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'genre_ids'=>'nullable|array',
            'genre_ids.*'=>"exists:genres,id"
        ]);

        if($request->hasFile('cover_image')){
            if($book->cover_image){
                Storage::disk('public')->delete($book->cover_image);
            }
            $book->cover_image = $request->file('cover_image')->store('books/covers','public');
        }

        $book->update($request->except('cover_image','genre_ids'));
        if($request->has('genre_ids')){
            $book->genres()->sync($request->genres_ids);
        }

        if(!$book){
            return response()->json(['error'=>'Book not found',"status"=>401]);
        }
        $book->load('genres');
        return response()->json(['data'=>$book,'status'=>201]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Book $book)
    {
        if($book->cover_image){
            Storage::disk('public')->delete($book->cover_image);
        }
        $book->delete();
        return response()->json(['message'=>"delete successfully",'status'=>201]);
    }
    public function attachGenres(Request $request,Book $book){
        $request->validate([
            'genre_ids'=>"required|array",
            'genre_ids.*'=> 'exists:genres,id'
        ]);
        $book->genres()->sync($request->genre_ids);
        return response()->json($book->load('genres'));
    }
}
