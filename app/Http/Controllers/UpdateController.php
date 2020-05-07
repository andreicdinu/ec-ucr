<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UpdateController extends Controller
{
    /**
     * @param Request $request
     */
    public function updateAll(Request $request)
    {
        return response()->json(['Information updated successfully'], 200);
    }
}
