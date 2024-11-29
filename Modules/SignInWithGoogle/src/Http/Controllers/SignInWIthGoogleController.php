<?php

namespace Workdo\SignInWithGoogle\Http\Controllers;

use App\Models\EmailTemplate;
use App\Models\LoginDetail;
use App\Models\Plan;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use App\Models\WorkSpace;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Hash;

class SignInWIthGoogleController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function setting(Request $request)
    {
        if (Auth::user()->isAbleTo('google manage')) {
            if ($request->has('google_signin_setting_enabled' && $request->google_signin_setting_enabled =='on')) {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'google_client_id' => 'required|string',
                        'google_client_secret_key' => 'required|string',
                        'google_authorized_url' => 'required|string',
                    ]
                );
                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }
            }

            $post = $request->all();
            if($request->google_sign_in_image)
            {
                $filenameWithExt  = $request->File('google_sign_in_image')->getClientOriginalName();
                $filename         = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension        = $request->file('google_sign_in_image')->getClientOriginalExtension();
                $fileNameToStores = $filename . '_' . time() . '.' . $extension;

                $path = upload_file($request,'google_sign_in_image',$fileNameToStores,'google_login');
                if($path['flag'] == 1){
                    $url = $path['url'];
                    $post['google_sign_in_image'] = $url;

                }else{
                    return redirect()->back()->with('error', __($path['msg']));
                }
            }
            unset($post['_token']);
            unset($post['_method']);
                if ($request->has('google_signin_setting_enabled')) {
                foreach ($post as $key => $value) {
                    // Define the data to be updated or inserted
                    $data = [
                        'key' => $key,
                        'workspace' => getActiveWorkSpace(),
                        'created_by' => creatorId(),
                    ];

                    // Check if the record exists, and update or insert accordingly
                    Setting::updateOrInsert($data, ['value' => $value]);
                }
            } else {
                // Define the data to be updated or inserted
                $data = [
                    'key' => 'google_signin_setting_enabled',
                    'workspace' => getActiveWorkSpace(),
                    'created_by' => creatorId(),
                ];

                // Check if the record exists, and update or insert accordingly
                Setting::updateOrInsert($data, ['value' => 'off']);
            }

            // Settings Cache forget
            AdminSettingCacheForget();
            return redirect()->back()->with('success', __('Google Setting save successfully'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function redirectToGoogle()
    {
        $settings = getAdminAllSetting();

        config(
            [
                'services.google.client_id' => $settings['google_client_id'],
                'services.google.client_secret' => $settings['google_client_secret_key'],
                'services.google.redirect' => route('google.callback'),
            ]
        );

        return Socialite::driver('google')->redirect();
    }

    public function GoogleCallback(Request $request)
    {
        try {
            $settings = getAdminAllSetting();

            config([
                'services.google.client_id' => $settings['google_client_id'],
                    'services.google.client_secret' => $settings['google_client_secret_key'],
                    'services.google.redirect' => route('google.callback'),
                    ]);
            $user = Socialite::driver('google')->user();
            $finduser = User::where('social_id', $user->id)->first();
            if ($finduser) {
                Auth::login($finduser);
                $ip = $_SERVER['REMOTE_ADDR']; // your ip address here
                // $ip = '49.36.83.199'; // This is static ip address

                $query = @unserialize(file_get_contents('http://ip-api.com/php/' . $ip));
                if(isset($query['status']) && $query['status'] == 'success')
                {
                    $whichbrowser = new \WhichBrowser\Parser($_SERVER['HTTP_USER_AGENT']);
                    if ($whichbrowser->device->type == 'bot')
                    {
                        return;
                    }

                    $referrer = isset($_SERVER['HTTP_REFERER']) ? parse_url($_SERVER['HTTP_REFERER']) : null;

                    /* Detect extra details about the user */
                    $query['browser_name'] = $whichbrowser->browser->name ?? null;
                    $query['os_name'] = $whichbrowser->os->name ?? null;
                    $query['browser_language'] = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? mb_substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) : null;
                    $query['device_type'] = GetDeviceType($_SERVER['HTTP_USER_AGENT']);
                    $query['referrer_host'] = !empty($referrer['host']);
                    $query['referrer_path'] = !empty($referrer['path']);
                    $json = json_encode($query);

                    $login_detail = new LoginDetail();
                    $login_detail->user_id = $finduser->id;
                    $login_detail->ip = $ip;
                    $login_detail->date = date('Y-m-d H:i:s');
                    $login_detail->Details = $json;
                    $login_detail->type = $finduser->type;
                    $login_detail->created_by = creatorId();
                    $login_detail->workspace = getActiveWorkSpace();
                    $login_detail->save();

                    // custom domain code
                    if(Auth::user()->type != 'super admin')
                    {
                        $uri = url()->full();
                        $segments = explode('/', str_replace(''.url('').'', '', $uri));
                        $segments = $segments[1] ?? null;

                        $local = parse_url(config('app.url'))['host'];
                        // Get the request host
                        $remote = request()->getHost();
                        if($local != $remote)
                        {
                            $remote = str_replace('www.', '', $remote);
                            $workSpace = WorkSpace::where('domain',$remote)->orwhere('subdomain',$remote)->where('created_by',creatorId())->first();
                            if($workSpace && ($workSpace->enable_domain == 'on'))
                            {
                                $redirect = true;
                                $user = User::find(Auth::user()->id);
                                $user->active_workspace = $workSpace->id;
                                $user->save();
                            }
                        }
                    }
                }
                return redirect()->route('home')->with('success', 'You have successfully logged in.');
            } else {
                $newUser = new User();
                $newUser->name = $user->name;
                $newUser->email = $user->email;
                $newUser->social_id = $user->id;
                $newUser->social_type = 'Google';
                $newUser->password = Hash::make(1234);
                // $newUser->mobile_no = $user->mobile;
                $newUser->type = 'company';
                $newUser->avatar = 'uploads/users-avatar/avatar.png';
                $newUser->save();
                Auth::login($newUser);
                if(!empty($newUser))
                {
                    do {
                        $code = rand(100000, 999999);
                    } while (User::where('referral_code', $code)->exists());

                    $workspace = new WorkSpace();
                    $workspace->name       = $user->nickname;
                    $workspace->created_by = $newUser->id;
                    $workspace->save();

                    $company = User::find($newUser->id);
                    $company->referral_code  = $code;
                    $company->active_workspace = $workspace->id;
                    $company->workspace_id = $workspace->id;
                    $company->save();

                    $role_r = Role::where('name','company')->first();
                    $newUser->addRole($role_r);

                    User::CompanySetting($newUser->id);
                    $newUser->MakeRole();

                    $plan = Plan::where('is_free_plan',1)->first();
                    if($plan)
                    {
                        $newUser->assignPlan($plan->id,'Month',$plan->modules,0,$newUser->id);
                    }

                    // Email Verification
                    if ( admin_setting('email_verification') == 'on')
                    {
                        try
                        {
                            $uArr = [
                                'email'=> $newUser->email,
                                'password'=> $newUser->password,
                                'company_name'=>$newUser->name,
                            ];

                            $admin_user = User::where('type','super admin')->first();
                            SetConfigEmail(!empty($admin_user->id) ? $admin_user->id : null);
                            $resp = EmailTemplate::sendEmailTemplate('New User', [$newUser->email], $uArr, $admin_user->id);
                            event(new Registered($newUser));
                        }
                        catch(\Exception $e)
                        {
                            $smtp_error = __('E-Mail has been not sent due to SMTP configuration');
                        }
                    }
                    else
                    {
                        $user_work = User::find($newUser->id);
                        $user_work->email_verified_at = date('Y-m-d h:i:s');
                        $user_work->save();
                    }
                }
                return redirect()->route('plans.index',['type'=>'subscription']);

            }
        } catch (\Exception $e) {
            \Log::error('Google Sign-In Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Login canceled Please try again.');
        }
    }
}
