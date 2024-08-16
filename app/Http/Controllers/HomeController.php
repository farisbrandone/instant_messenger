<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function home()
    {
        return inertia('Home'); //we render Home.jsx file inside ressource/js/profile/page
    }
}
