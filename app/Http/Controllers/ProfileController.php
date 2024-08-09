<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Profile;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //membuat error validation required
        $validator = Validator::make($request->all(), [
            'age' => 'required|integer',
            'bio' => 'required',
        ]);

        //mengirim validasi error jika ada kesalahan input
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $currentUser = auth()->user();

        $profile = Profile::updateOrCreate(
            ['user_id' => $currentUser->id],
            [
                'age' => $request['age'],
                'bio' => $request['bio'],
                'user_id' => $currentUser->id
            ]
        );

        if ($profile) {

            return response()->json([
                'message' => 'Profile Berhasil ditambah / diubah',
                'data' => $profile,
            ], 201);

        }

        //jika gagal menyimpan ke database
        return response()->json([
            'message' => 'Profile tidak Berhasil disimpan / diubah',
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
