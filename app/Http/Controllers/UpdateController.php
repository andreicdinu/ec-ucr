<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Mockery\Exception;

class UpdateController extends Controller
{
    /**
     * @param Request $request
     */
    public function updateAll(Request $request)
    {
        try {

            $body = json_decode($request->getContent(), true);
            if(!isset($body['users']) || count($body['users']) < 1) {
                throw new \Exception('missing users');
            }

            if(!isset($body['websites']) || count($body['websites']) < 1) {
                throw new \Exception('missing websites');
            }

            if(!isset($body['groups']) || count($body['groups']) < 1) {
                throw new \Exception('missing groups');
            }

            if(!isset($body['website_user_groups'])) {
                throw new \Exception('missing website_user_groups');
            }

            DB::beginTransaction();
            Schema::disableForeignKeyConstraints();

            DB::table('users')->truncate();
            DB::table('groups')->truncate();
            DB::table('websites')->truncate();
            DB::table('environments')->truncate();
            DB::table('website_user_groups')->truncate();

            DB::table('users')->insert($body['users']);
            DB::table('websites')->insert($body['websites']);
            DB::table('groups')->insert($body['groups']);
            DB::table('environments')->insert($body['environments']);
            DB::table('website_user_groups')->insert($body['website_user_groups']);

            Schema::enableForeignKeyConstraints();
            DB::commit();

            Log::channel('api_requests')->info('updateAll success.');
            return response()->json(['response' => 'Information updated successfully'], 200);

        } catch(\Exception $e) {
            Schema::enableForeignKeyConstraints();
            DB::rollBack();
            Log::channel('api_requests')->error('updateAll error.', ['message' => $e->getMessage()]);
            return response()->json(['response' => 'Something went wrong, please check logs'], 400);
        }
    }
}
