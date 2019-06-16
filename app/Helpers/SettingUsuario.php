<?php

if (!function_exists('setting_usuario')) {

    /**
     * description
     *
     * @param
     * @return
     */
    function setting_usuario($key)
    {
        if (!is_null(Auth::user())) {
            setting()->setExtraColumns(['user_id' => Auth::user()->id]);
        }

        return setting_sitio($key);
    }

    function setting_sitio($key)
    {
        if (is_array($key)) {
            if (!is_null(array_values($key)[0])) {
                setting($key);
            } else {
                setting()->forget(array_key_first($key));
            }
            setting()->save();
        } else {
            return setting($key);
        }

        return null;
    }
}
