<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Tightenco\Ziggy\Ziggy;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Arr;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request)
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request)
    {
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user(),
            ],
            'ziggy' => function () use ($request) {
                return array_merge((new Ziggy)->toArray(), [
                    'location' => $request->url(),
                ]);
            },
            'lang' => function() {
                $locale = App::getLocale();
                $phpTransations = [];
                $jsonTransations = [];
                if(File::exists(base_path("lang/{$locale}"))) {
                    $phpTransations = collect(File::allFiles(base_path("lang/{$locale}")))
                        ->filter(function($file) {
                            return $file->getExtension() === "php";
                    })->flatMap(function($file) {
                        return Arr::dot(File::getRequire($file->getRealPath()));
                    })->toArray();
                }
                if(File::exists(base_path("lang/{$locale}.json"))) {
                    $jsonTransations = json_decode(File::get(base_path("lang/{$locale}.json")),true);
                }
                $translation = array_merge($phpTransations,$jsonTransations);
                return $translation;    
            },
            'flash' => [
                'message' => session('message')
                // 'message' => fn () => $request->session()->get('message')
            ]
        ]);
    }
}
