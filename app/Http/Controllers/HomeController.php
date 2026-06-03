<?php

namespace App\Http\Controllers;

use App\Models\TypingChallenge;

class HomeController extends Controller
{
    public function index()
    {
        $activeChallenge = TypingChallenge::where('status', 'active')->latest()->first();

        return view('home', compact('activeChallenge'));
    }
}
