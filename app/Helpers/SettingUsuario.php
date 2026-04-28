<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

if (!function_exists('setting_usuario')) {

    function setting_usuario($key, $user = null)
    {
        $usuario = null;
        if (!is_null($user)) {
            $usuario = $user->id;
        } else if (!is_null(Auth::user())) {
            $usuario = Auth::user()->id;
        }

        setting()->setExtraColumns(['user_id' => $usuario]);

        if (is_array($key)) {
            $settingKey = array_key_first($key);
            $settingValue = array_values($key)[0];

            if (!is_null($settingValue)) {
                setting($key);
            } else {
                setting()->forget($settingKey);
            }
            setting()->save();

            // Flush all cached settings for this user and immediately re-populate
            // the changed key so concurrent reads don't re-cache a stale value.
            Cache::tags('user_' . $usuario)->flush();
            if (!is_null($settingValue)) {
                Cache::tags('user_' . $usuario)
                    ->put($settingKey, $settingValue, config('ikasgela.eloquent_cache_time'));
            }
        } else {
            if (!is_null($usuario)) {
                return Cache::tags('user_' . $usuario)
                    ->remember($key, config('ikasgela.eloquent_cache_time'), function () use ($key, $usuario) {
                        $result = DB::table('settings')->where('user_id', $usuario)->where('key', $key)->first();
                        return !is_null($result) ? $result->value : null;
                    });
            } else if (!is_null(Auth::user())) {
                return Cache::tags('user_' . $usuario)
                    ->remember($key, config('ikasgela.eloquent_cache_time'), function () use ($key, $usuario) {
                        return DB::table('settings')->where('key', $key)->first()->value;
                    });
            }
        }

        return null;
    }
}
