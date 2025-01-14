<?php

if (!function_exists('isRole')) {
    function isRole($role)
    {
        return auth()->check() && auth()->user()->role === $role;
    }
}
