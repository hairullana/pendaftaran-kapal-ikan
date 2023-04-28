<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function list_users()
    {
        $new_user = User::all();
        return response(['status' => true, 'data' => $new_user]);
    }

    public function detail_users($id)
    {
        $new_user = User::with('kapal_ikan')->find($id);
        return response(['status' => true, 'data' => $new_user]);
    }
}
