<?php

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
            if (!is_null(array_values($key)[0])) {
                setting($key);
            } else {
                setting()->forget(array_key_first($key));
                Cache::forget('setting_' . array_key_first($key));
            }
            setting()->save();
        } else {
            if (!is_null($usuario)) {

                $cache_key = 'setting_' . $key . '_' . $usuario;

                $result = Cache::remember($cache_key, 60, function () use ($key, $usuario) {
                    return DB::table('settings')->where('user_id', $usuario)->where('key', $key)->first();
                });

                return !is_null($result) ? $result->value : null;
            } else if (!is_null(Auth::user())) {

                $cache_key = 'setting_' . $key;

                return Cache::remember($cache_key, 60, function () use ($key) {
                    return DB::table('settings')->where('key', $key)->first()->value;
                });
            }
        }

        return null;
    }
}
