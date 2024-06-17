<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function index()
    {
        return view('lanpage.index');
    }

    public function documentation()
    {
        return view('lanpage.documentation');
    }
}