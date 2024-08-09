<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class BooksController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'isAdmin'])->only(['store', 'update', 'destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $books = Book::with(['category'])
            ->orderBy('created_at', 'desc')
            ->get();


        //jika tak ada data
        if (!$books) {
            return response()->json([
                'message' => 'Data Tidak Ditemukan',
            ], 404);
            ;
        }

        //jika berhasil
        return response()->json([
            'message' => 'Data Berhasil ditampilkan',
            'data' => $books
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //membuat error validation required
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'summary' => 'required',
            'stok' => 'required|integer',
            'category_id' => 'required|exists:categories,id',
            'image' => 'required|mimes:jpg,jpeg,png'
        ]);

        // Mengirim validasi error jika ada kesalahan input
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $category = Category::find($request->category_id);

        // Jika tidak ada data kategori
        if (!$category) {
            return response()->json([
                'message' => 'Data Category Tidak Ditemukan',
            ], 404);
        }

        $data = $validator->validated();

        // Jika file gambar diinput
        if ($request->hasFile('image')) {
            // Membuat unique name pada gambar yang diinput
            $imageName = time() . '.' . $request->image->extension();

            // Simpan gambar pada file storage
            $request->image->storeAs('public/images', $imageName);

            $uploadedFileUrl = Cloudinary::upload($request->file('image')->getRealPath())->getSecurePath();

            // Mengganti request nilai request poster menjadi $imageName yang baru bukan berdasarkan request
            $data['image'] = $uploadedFileUrl;
        }

        // Menyimpan ke database
        $books = Book::create($data);

        // Jika berhasil menyimpan ke database
        if ($books) {
            return response()->json([
                'message' => 'Data berhasil ditambahkan',
            ], 201);
        }

        // Jika gagal menyimpan ke database
        return response()->json([
            'message' => 'Buku tidak Berhasil disimpan',
        ], 409);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //mencari movie berdasarkan ID
        $buku = Book::with([
            'category',
            'list_borrows'
        ])->find($id);

        if (!$buku) {
            return response()->json([
                'message' => 'Buku Tidak Ditemukan',
            ], 404);
        }

        //mengirim response dalam bentuk JSON
        return response()->json([
            'message' => 'Data Detail ditampilkan',
            'data' => $buku
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //membuat error validation required
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'summary' => 'required',
            'stok' => 'required|integer',
            'category_id' => 'required|exists:categories,id',
            'image' => 'mimes:jpg,jpeg,png'
        ]);

        //mengirim validasi error jika ada kesalahan input
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $category = Category::find($request->category_id);

        //jika tak ada data genre
        if (!$category) {
            return response()->json([
                'message' => 'Data Category Tidak Ditemukan',
            ], 404);
            ;
        }

        //mencari movie berdasarkan ID
        $books = Book::find($id);

        if (!$books) {
            return response()->json([
                'message' => 'Buku Tidak Ditemukan',
            ], 404);
        }

        $data = $validator->validated();

        if ($request->hasFile('image')) {

            // Hapus gambar lama jika ada

            if ($books->image) {

                Storage::delete('public/images/' . $books->image);

            }

            $imageName = time() . '.' . $request->image->extension();

            $request->image->storeAs('public/images', $imageName);

            $uploadedFileUrl = Cloudinary::upload($request->file('image')->getRealPath())->getSecurePath();

            $data['image'] = $uploadedFileUrl;

        }

        $books->update($data);

        return response()->json([
            'message' => 'Data Berhasil diupdate',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $books = Book::find($id);

        if (!$books) {
            return response()->json([
                'message' => 'Buku Tidak Ditemukan',
            ], 404);
        }

        if ($books->poster) {

            Storage::delete('public/images/' . $books->poster);

        }

        if ($books) {

            //menghapus movie
            $books->delete();

            return response()->json([
                'message' => 'Data Berhasil dihapus',
            ], 200);

        }

        //jika data movie tidak ditemukan
        return response()->json([
            'message' => 'Buku Tidak Ditemukan',
        ], 404);
    }
}
