<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\CreateUserProfileJob;
use App\Jobs\CreateUserSettingJob;
use App\Jobs\NotifyAdminJob;
use App\Jobs\SendEmailJob;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Throwable;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        // SendEmailJob::dispatch($user);
        // SendEmailJob::dispatch($user)->onQueue('email');
        // SendEmailJob::dispatch($user)->delay(now()->addSeconds(10));
        // SendEmailJob::dispatchIf(false, $user);
        // SendEmailJob::dispatchUnless(true, $user);

        // //   ONBOARDING FLOW START

        // //CREATE USER PROFILE
        // CreateUserProfileJob::dispatch($user);
        // //CREATE USER SETTING
        // CreateUserSettingJob::dispatch($user);
        // //SEND EMAIL NOTIFICATION
        // SendEmailJob::dispatch($user);
        // //NOTIFY ADMIN
        // NotifyAdminJob::dispatch($user)->delay(now()->addSeconds(30));

        Bus::chain([

            //CREATE USER PROFILE
            new CreateUserProfileJob($user),
            //CREATE USER SETTING
            new CreateUserSettingJob($user),
            //SEND EMAIL NOTIFICATION
            new SendEmailJob($user),
            //NOTIFY ADMIN
            new NotifyAdminJob($user)->delay(now()->addSeconds(30)),
        ])->catch(function (Throwable $message) use ($user) {
            logger($message, [
                'user_id' => $user->id,
                'message' => $message
            ]);
        })
        ->dispatch();

        return redirect(route('dashboard', absolute: false));
    }
}
