<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Mail\BorrowNotification;
use App\Models\Book;
use App\Models\Borrow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class BorrowController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $borrow = Borrow::with(['book','user'])->get();
        if(!$borrow)
        {
            return response()->json(['message'=>"don't find them"]);
        }
        return response()->json(['data'=>$borrow,'status'=>201]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'book_id'=>'required|exists:books,id',
            'borrow_date'=>'required|date'
        ]);
        $book = Book::findOrFail($request->book_id);
        if($book->quantity <= 0)
        {
            return response()->json(['messaeg'=>'Book is out of stock','status'=>400]);
        }
        $borrow = Borrow::create([
            'user_id'=>Auth::id(),
            'book_id'=>$request->book_id,
            'borrow_date'=>$request->borrow_date,
            'status'=>'borrowed'
        ]);
        $namebook = Book::where('id',$request->book_id)->select('title')->get();
        $borrow->title = $namebook;
        $book->decrement('quantity');
        Mail::to(Auth::user()->email)->queue(new BorrowNotification($borrow));
        return response()->json(['data'=>$borrow,'status'=>201]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Borrow $borrow)
    {
        return response()->json($borrow->load(['user','book']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,Borrow $borrow)
    {
        $request->validate([
            "return_date"=>"required|date|after:borrow_date"
        ]);
        if($borrow->status ==="returned"){
            return response()->json(["message"=>"Book already returned",'status'=>400]);
        }
        //Thêm logic quá hạn ngày trả sách sẽ thêm tiền phat (fines)
        $borrow->update([
            'return_date'=>$request->return_date,
            'status'=>'returned'
        ]);
        $borrow->book()->increment('quantity');
        return response()->json(['data'=>$borrow,'status'=>201]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Borrow $borrow)
    {
        if($borrow->status === 'borrowed'){
            $borrow->book()->increment('quantity');
        }
        $borrow->delete();
        return response()->json(null,204);
    }

    public function statistics(Request $request){
        $borrowsByMonth = Borrow::selectRaw('MONTH(borrow_date) as month,COUNT(*) as total')
                                ->groupBy('month')
                                ->get();
        $popularBooks = Book::withCount('borrows')
                            ->orderBy('borrows_count','desc')
                            ->take(5)
                            ->get();
        return response()->json([
            'borrows_by_month'=>$borrowsByMonth,
            'popular_books'=>$popularBooks
        ]);
    }
}
