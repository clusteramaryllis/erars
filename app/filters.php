<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request)
{
	//
});


App::after(function($request, $response)
{
	//
});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth', function()
{
	if (Auth::guest()) return Redirect::guest('login');
});

Route::filter('auth.basic', function()
{
	return Auth::basic();
});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (Auth::check()) return Redirect::to('cpanel');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
	if (Session::token() != Input::get('_token'))
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
});

/*
|--------------------------------------------------------------------------
| Ajax Request Only Filter
|--------------------------------------------------------------------------
|
|
*/
Route::filter('ajax', function()
{
	if (! Request::ajax() ) 
	{
		return Response::error('404');	
	}
});

/*
|--------------------------------------------------------------------------
| Validator Filter
|--------------------------------------------------------------------------
|
|
*/
Validator::extend('phone', function($attribute, $value, $parameters){
	return preg_match('/^([0-9-+])+$/i', $value);
});

Validator::extend('alpha_space', function($attribute, $value, $parameters){
	return preg_match('/^([a-z \.])+$/i', $value);
});

Validator::extend('coordinate', function($attribute, $value, $parameters){
	return preg_match('/^([0-9-\.])+$/i', $value);
});

/*
|--------------------------------------------------------------------------
| View Filter
|--------------------------------------------------------------------------
|
|
*/

// Let Pagination Handle the search query also
View::composer(Paginator::getViewName(), function($view) {
	$query = array_except( Input::query(), Paginator::getPageName() );
	$view->paginator->appends($query);
});

// Helpers
if (!function_exists('rand_without'))
{
    function rand_without($from, $to, array $exceptions) 
    {
        sort($exceptions); // lets us use break; in the foreach reliably
        $number = rand($from, $to - count($exceptions)); // or mt_rand()
        foreach ($exceptions as $exception) {
            if ($number >= $exception) {
                $number++; // make up for the gap
            } else /*if ($number < $exception)*/ {
                break;
            }
        }
        return $number;
    }    
}

function formatOffset($offset) {
    $hours = $offset / 3600;
    $remainder = $offset % 3600;
    $sign = $hours > 0 ? '+' : '-';
    $hour = (int) abs($hours);
    $minutes = (int) abs($remainder / 60);

    if ($hour == 0 AND $minutes == 0) {
        $sign = ' ';
    }
    return 'GMT' . $sign . str_pad($hour, 2, '0', STR_PAD_LEFT) 
            .':'. str_pad($minutes,2, '0');
}