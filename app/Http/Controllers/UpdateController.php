<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class UpdateController extends Controller
{
    /**
     * @param Request $request
     */
    public function updateAll(Request $request)
    {
        $time1 = time();

        try {
            DB::beginTransaction();
            Schema::disableForeignKeyConstraints();

            DB::table('users')->truncate();
            DB::table('groups')->truncate();
            DB::table('websites')->truncate();
            DB::table('environments')->truncate();
            DB::table('website_user_groups')->truncate();

            $body = json_decode($request->getContent(), true);

            DB::table('users')->insert($body['users']);
            DB::table('websites')->insert($body['websites']);
            DB::table('groups')->insert($body['groups']);
            DB::table('environments')->insert($body['environments']);
            DB::table('website_user_groups')->insert($body['website_user_groups']);

            Schema::enableForeignKeyConstraints();
            DB::commit();

        } catch(\Exception $e) {
            Schema::enableForeignKeyConstraints();
            DB::rollBack();
            Log::channel('api_requests')->error('updateAll error', ['message' => $e->getMessage()]);
        }

        $time2 = time();
        Log::channel('api_requests')->info('New request success', ['time_start' => $time1, 'time_end' => $time2]);
        return response()->json(['Information updated successfully'], 200);
    }
}
