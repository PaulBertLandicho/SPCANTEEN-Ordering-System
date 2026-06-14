<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class IntroController extends Controller
{
    public function afterIntro()
    {
        Cookie::queue('seenIntro', true, 2628000);
        return redirect('/');
    }
}
