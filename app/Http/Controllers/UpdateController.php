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
        /**
         * DB start transaction
         * cleanup database
         * insert entities
         * DB commit
         * DB rollback
         */
        return response()->json(['Information updated successfully'], 200);
    }
}
