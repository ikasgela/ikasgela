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

        if (is_array($key)) {
            setting($key);
            setting()->save();
        } else {
            return setting($key);
        }

        return null;
    }
}
