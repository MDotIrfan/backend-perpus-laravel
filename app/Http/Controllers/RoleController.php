<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //ambil data terbaru dari tabel role
        $roles = Role::orderBy('created_at', 'desc')->get();

        //jika tak ada data
        if (!$roles) {
            return response()->json([
                'message' => 'Data Tidak Ditemukan',
            ], 404);
            ;
        }

        //jika berhasil
        return response()->json([
            'message' => 'Data Berhasil Ditampilkan',
            'data' => $roles
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
        $roles = Role::create([
            'name' => $request->name,
        ]);

        //jika berhasil menyimpan ke database
        if ($roles) {

            return response()->json([
                'message' => 'Data Berhasil Ditambahkan',
            ], 201);

        }

        //jika gagal menyimpan ke database
        return response()->json([
            'message' => 'Roles tidak Berhasil disimpan',
        ], 409);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //mencari roles berdasarkan ID
        $roles = Role::findOrfail($id);

        //jika tak ada data
        if (!$roles) {
            return response()->json([
                'message' => 'Data Tidak Ditemukan',
            ], 404);
            ;
        }

        //mengirim response dalam bentuk JSON
        return response()->json([
            'message' => 'Detail Data Roles',
            'data' => $roles
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

        //mencari roles berdasarkan ID
        $roles = Role::findOrFail($id);

        if ($roles) {

            //mengupdate data roles
            $roles->update([
                'name' => $request->name,
            ]);

            return response()->json([
                'message' => 'Data Berhasil Diupdate',
            ], 200);

        }

        //data roles tidak ditemukan
        return response()->json([
            'message' => 'Roles Tidak Ditemukan',
        ], 404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //mencari roles berdasarkan ID
        $roles = Role::findOrfail($id);

        if ($roles) {

            //menghapus roles
            $roles->delete();

            return response()->json([
                'message' => 'Data detail berhasil dihapus',
            ], 200);

        }

        //jika data roles tidak ditemukan
        return response()->json([
            'message' => 'Roles Tidak Ditemukan',
        ], 404);
    }
}
