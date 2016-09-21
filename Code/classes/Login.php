<?php

class Login
{
    public static function run($username_raw, Collector\Settings $settings) {
        $login = array();

        $_files = $login['_FILES'] = new FileSystem();

        $username     = self::clean_and_validate_username($username_raw);
        $debug_mode   = self::determine_debug_mode($settings, $username);
        $data_sub_dir = $debug_mode ? '/Debug' : '';
        $id           = rand_string(10);

        $login['Username']   = $username;
        $login['ID']         = $id;
        $login['Debug Mode'] = $debug_mode;

        $_files->set_default('Username',     $username);
        $_files->set_default('ID',           $id);
        $_files->set_default('Data Sub Dir', $data_sub_dir);

        return $login;
    }

    private static function clean_and_validate_username($username_raw) {
        $username = preg_replace('([^ !#$%&\'()+,\\-.0-9;=@A-Z[\\]^_a-z~])', '', $username_raw);
        if (strlen($username) < 4) throw new Exception('Username too short');

        return $username;
    }

    private static function determine_debug_mode(Collector\Settings $settings, $username) {
        if ($settings->debug_mode) return true;

        $debug_name = $settings->debug_name;

        return substr($username, 0, strlen($debug_name)) === $debug_name;
    }
}