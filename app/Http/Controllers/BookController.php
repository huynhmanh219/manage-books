<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(['data'=>Book::all(),'status'=>201]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validate= $request->validate([
            'title'=>'required',
            'author'=>'required',
            'published_year'=>'required|numeric'
        ]);
        $books = Book::create($request->all());
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
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $book = Book::find($id);
        if(!$book){
            return response()->json(['error'=>'Book not found',"status"=>401]);
        }
        $validate = $request->validate([
            'title'=>'required',
            'author'=>'required',
            'published_year'=>'required|numeric'
        ]);

        $updated = $book->update($validate);
        return response()->json(['data'=>$updated,'status'=>201]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $book = Book::find($id);
        if(!$book){
            return response()->json(['error'=>'Book not found',"status"=>401]);
        }
        $book->delete();
        return response()->json(['message'=>"delete successfully",'status'=>201]);
    }
}
