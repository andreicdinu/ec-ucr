<?php

namespace App\Http\Controllers;

use App\Environment;
use App\Group;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RetrieveController extends Controller
{
    /**
     * @param Request $request
     * @queryParam website_url required The website url for identifying the website.
     * @queryParam user_eulogin required The user eulogin for identifying the user.
     * @queryParam user_email required The user email for identifying the user (for users that do not have a eulogin).
     * @headerParam UCR-TOKEN required for authentication
     * @return array[] groups
     */
    public function getUserGroups(Request $request)
    {
        try {
            $result = [];

            $environment = $this->getEnvironment($request->get('website_url'));
            $requestUserEuLogin = $request->get('user_eulogin');
            $requestUserEmail = $request->get('user_email');

            if($requestUserEuLogin) {
                $user = User::where('eulogin', $requestUserEuLogin)->first();
            } elseif($requestUserEmail) {
                $user = User::where('email', $requestUserEmail)->first();
            }

            if(!isset($user)) {
                throw new \Exception('could not load user eulogin:' . $requestUserEuLogin . ' email:' . $requestUserEmail);
            }

            $result['user']['eulogin'] = $user->getAttribute('eulogin');
            $result['user']['email'] = $user->getAttribute('email');

            $website = $environment->website;

            $result['website']['url'] = $environment->getAttribute('url');
            $result['website']['identifier'] = $website->getAttribute('name');
            $result['groups'] = [];

            $userGroups = $website->userGroups->where('id', $user->getKey());
            foreach($userGroups as $singleUserGroup) {
                $groupId = $singleUserGroup->pivot_website_user_groups->getAttribute('group_id');
                $groupMachineName = $this->getGroupMachineNameById($groupId);
                $result['groups'][] = $groupMachineName;
            }
            return response()->json($result, 200);

        } catch (\Exception $e) {
            Log::channel('api_requests')->error('getUserGroups error.', ['message' => $e->getMessage()]);
            return response()->json(['response' => 'Something went wrong, please check logs'], 400);
        }
    }

    protected function getEnvironment($websiteUrl)
    {
        if(substr($websiteUrl, -1) == '/') {
            $websiteUrl = substr($websiteUrl, 0, -1);
        }
        $environment = Environment::where('url', $websiteUrl)
            ->orWhere('url', $websiteUrl . '/')
            ->first();

        return $environment;
    }

    protected function getGroupMachineNameById($groupId) {
        $resultArray =  Group::where('id', $groupId)->pluck('machine_name');
        return $resultArray[0];
    }
}
