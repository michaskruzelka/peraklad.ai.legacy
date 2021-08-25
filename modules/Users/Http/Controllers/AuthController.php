<?php

namespace Modules\Users\Http\Controllers;

use Modules\Users\Http\Requests\LoginRequest;
use Modules\Users\Http\Requests\RecoverRequest;
use Modules\Users\Http\Requests\RegisterRequest;
use Pingpong\Modules\Routing\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Support\Facades\Auth;
use Illuminate\Cache\RateLimiter;
use Illuminate\Support\Facades\Lang;
use Rlima\Laravel5DoctrineODM\LaravelDocumentManager;
use Modules\Users\Entities\User;
use Mail;

class AuthController extends Controller
{
    use ThrottlesLogins;

    /**
     * @var \Doctrine\ODM\MongoDB\DocumentManager
     */
    protected $dm;

    public function __construct(LaravelDocumentManager $ldm)
    {
        $this->dm = $ldm->getDocumentManager();
        $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * @return \Illuminate\View\View
     */
    public function login()
    {
        return view('users::login');
    }

    /**
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function doLogin(LoginRequest $request)
    {
        if ($this->hasTooManyLoginAttempts($request)) {
            return $this->sendLockoutResponse($request);
        }
        $this->incrementLoginAttempts($request);
        $credentials = $request->getCredentials($request);
        if (Auth::attempt($credentials, $request->hasRemember())) {
            $this->clearLoginAttempts($request);
            $url = $request->getReferrer();
            $response = ['status' => 'ok', 'response' => $url];
            return response()->json($response);
        }
        return $this->failedResponse($this->getFailedLoginMessage());
    }

    /**
     * @return \Illuminate\View\View
     */
    public function register()
    {
        return view('users::register');
    }

    /**
     * @param RegisterRequest $request
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function doRegister(RegisterRequest $request, User $user)
    {
        $user->setId($request->getUsername())
            ->setEmail($request->getEmail())
            ->setAuthPassword($request->getPassword())
            ->setName($request->getName())
        ;
        $this->dm->persist($user);
        $this->dm->flush();
        Auth::login($user);
        $response = ['status' => 'ok', 'response' => route(config('users.redirects.login'))];
        return response()->json($response);
    }

    /**
     * @return \Illuminate\View\View
     */
    public function forgot()
    {
        return view('users::forgot');
    }

    /**
     * @param RecoverRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function recover(RecoverRequest $request)
    {
        $user = $request->getUser();
        $password = $user->getAuthPassword();
        $name = $user->getName();
        Mail::queue('users::emails.recover', compact('password', 'name'), function($message) use ($user) {
            $message->from(config('users.emails.default'))
                ->to($user->getEmail())
                ->subject($this->getRecoverTitle())
            ;
        });
        $response = ['status' => 'ok', 'response' => ''];
        return response()->json($response);
    }

    /**
     * Log the user out of the application.
     *
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        Auth::logout();
        return redirect(route(config('users.redirects.logout')));
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function loginUsername()
    {
        return 'email';
    }

    /**
     * Redirect the user after determining they are locked out.
     *
     * @param  LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendLockoutResponse(LoginRequest $request)
    {
        $seconds = app(RateLimiter::class)->availableIn(
            $this->getThrottleKey($request)
        );
        $message = $this->getLockoutErrorMessage($seconds);
        return $this->failedResponse($message);
    }

    /**
     * @return string
     */
    protected function getRecoverTitle()
    {
        return Lang::has('users::auth.recoverTitle')
            ? Lang::get('users::auth.recoverTitle')
            : 'Password recovery'
        ;
    }

    /**
     * Get the login lockout error message.
     *
     * @param  int  $seconds
     * @return string
     */
    protected function getLockoutErrorMessage($seconds)
    {
        return Lang::has('users::auth.throttle')
            ? Lang::get('users::auth.throttle', ['seconds' => $seconds])
            : 'Too many login attempts. Please try again in ' . $seconds . ' seconds.';
    }

    /**
     * Get the failed login message.
     *
     * @return string
     */
    protected function getFailedLoginMessage()
    {
        return Lang::has('users::auth.failed')
            ? Lang::get('users::auth.failed')
            : 'These credentials do not match our records.';
    }

    /**
     * @param $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function failedResponse($message)
    {
        $result = ['status' => 'fail', 'response' => $message];
        return response()->json($result);
    }
}