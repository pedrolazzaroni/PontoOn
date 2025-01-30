<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class HoraExtraController extends Controller
{
    public function overtime()
    {
        // Fetch all users with their overtime records
        $users = User::with(['overtimes' => function($query) {
            $query->where('horas_extras', '>', 0);
        }])->where('responsavel_id', auth()->id())->get();

        // Return the 'admin.hora-extra' view with the overtime data
        return view('admin.hora-extra', compact('users'));
    }
}
