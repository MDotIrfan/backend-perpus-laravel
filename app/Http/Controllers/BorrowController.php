<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use App\Models\Borrow;
use Illuminate\Support\Facades\Validator;

// use App\Http\Requests\GenreRequest;

class BorrowController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'isAdmin'])->only(['index']);
        $this->middleware(['auth:api'])->only(['store']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $borrows = Borrow::with([
            'book',
            'user',
        ])->orderBy('borrow_date', 'asc')->get();

        //jika tak ada data
        if (!$borrows) {
            return response()->json([
                'message' => 'Data Tidak Ditemukan',
            ], 404);
            ;
        }

        //jika berhasil
        return response()->json([
            'message' => 'Tampil Data Berhasil',
            'data' => $borrows
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'load_date' => 'required|date',
            'borrow_date' => 'required|date',
            'book_id' => 'required|exists:books,id',
            // 'user_id' => 'required|exists:users,id',
        ]);

        // Mengirim validasi error jika ada kesalahan input
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $currentUser = auth()->user();

        // Cek stok buku terlebih dahulu
        $book = Book::find($request->book_id);

        if ($book->stok < 1) {
            return response()->json([
                'message' => 'Stok buku tidak cukup',
            ], 400);
        }

        // Menyimpan ke database
        $borrows = Borrow::updateOrCreate(
            ['user_id' => $currentUser->id],
            [
                'load_date' => $request->load_date,
                'borrow_date' => $request->borrow_date,
                'user_id' => $currentUser->id,
                'book_id' => $request->book_id,
            ]
        );

        // Jika berhasil menyimpan ke database
        if ($borrows) {
            // Mengurangi stok buku
            $book->decrement('stok', 1);

            return response()->json([
                'message' => 'Peminjaman berhasil ditambah / dibuat',
                'data' => $borrows
            ], 201);
        }

        // Jika gagal menyimpan ke database
        return response()->json([
            'message' => 'Peminjaman tidak berhasil disimpan',
        ], 409);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
