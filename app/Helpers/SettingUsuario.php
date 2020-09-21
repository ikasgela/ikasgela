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
            }
            setting()->save();
        } else {
            return DB::table('settings')->where('user_id', $usuario)->where('key', $key)->first()->value;
        }

        return null;
    }
}
