<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
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
        $category = Category::orderBy('name', 'asc')->get();

        //jika tak ada data
        if (!$category) {
            return response()->json([
                'message' => 'Data Tidak Ditemukan',
            ], 404);
            ;
        }

        //jika berhasil
        return response()->json([
            'message' => 'Berhasil Tampil Semua Category',
            'data' => $category
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //membuat error validation required
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        //mengirim validasi error jika ada kesalahan input
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        //menyimpan ke database
        $category = Category::create([
            'name' => $request->name,
        ]);

        //jika berhasil menyimpan ke database
        if ($category) {

            return response()->json([
                'message' => 'Berhasil Tambah Category ',
            ], 201);

        }

        //jika gagal menyimpan ke database
        return response()->json([
            'message' => 'Category tidak Berhasil disimpan',
        ], 409);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //mencari genre berdasarkan ID
        $category = Category::with('list_books')->find($id);

        //jika tak ada data
        if (!$category) {
            return response()->json([
                'message' => 'Data Tidak Ditemukan',
            ], 404);
            ;
        }

        //mengirim response dalam bentuk JSON
        return response()->json([
            'message' => 'Berhasil Detail data dengan id ' . $id,
            'data' => $category
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //membuat error validation required
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        //mengirim validasi error jika ada kesalahan input
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        //mencari genre berdasarkan ID
        $category = Category::findOrFail($id);

        if ($category) {

            //mengupdate data genre
            $category->update([
                'name' => $request->name,
            ]);

            return response()->json([
                'message' => 'Berhasil melakukan update Category id : ' . $id,
            ], 200);

        }

        //data genre tidak ditemukan
        return response()->json([
            'message' => 'Category Tidak Ditemukan',
        ], 404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //mencari genre berdasarkan ID
        $category = Category::findOrfail($id);

        if ($category) {

            //menghapus genre
            $category->delete();

            return response()->json([
                'message' => 'data dengan id : ' . $id . ' berhasil terhapus',
            ], 200);

        }

        //jika data genre tidak ditemukan
        return response()->json([
            'message' => 'Category Tidak Ditemukan',
        ], 404);
    }
}
