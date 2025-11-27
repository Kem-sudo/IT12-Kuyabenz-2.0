<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $menuItems = MenuItem::all();
        return response()->json($menuItems);
    }

    public function show(MenuItem $menuItem)
    {
        return response()->json($menuItem);
    }
}