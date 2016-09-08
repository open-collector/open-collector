<?php
/**
 * Settings class.
 */

namespace Collector;

/**
 * Controls the getting, setting, and writing of settings.
 */
class Settings
{
    /**
     * The default password. It is stored in Password.php
     *
     * @var string
     */
    protected $default_pass = 'weakpassword';

    /**
     * The default settings in "Common Settings.json".
     * If "Common Settings.json" is not present these settings will be used
     *
     * @var array
     */
    protected $def_common = array(
        'force_experiment' => '',
        'experimenter_email' => 'youremail@yourdomain.com',
        'check_all_files' => true,
        'check_current_files' => true,
        'debug_name' => '',
        'debug_time' => 1,
        'trial_diagnostics' => false,
        'stop_at_login' => false,
        'stop_for_errors' => true,
    );

    /**
     * The default settings in the "Settings.json" for all new experiments.
     * If "Settings.json" is not present these settings will be used
     * @var array
     */
    protected $def_exp = array(
        'experiment_name' => 'Collector',
        'debug_mode' => false,
        'lenient_criteria' => 75,
        'welcome' => 'Welcome to the experiment!',
        'exp_description' => '<p>This experiment will run for about 25 minutes. Your goal will be to learn some information.</p>',
        'ask_for_login' => 'Participant ID',
        'show_condition_selector' => true,
        'use_condition_names' => true,
        'show_condition_info' => false,
        'hide_flagged_conditions' => true,
        'verification' => '',
        'check_elig' => false,
        'blacklist' => false,
        'whitelist' => array(
            '::1', 'localhost',
        ),
    );

    protected $files = null;

    /**
     * Constructor.
     *
     * @param string $commonLoc The path to the common settings file.
     * @param string $expLoc    The path to the experiment specific settings file.
     * @param string $passLoc   The path to the password.
     */
    public function __construct(\FileSystem $_files)
    {
        $this->files      = $_files;
        $this->common     = $this->load('Common Settings', $_files);
        $this->experiment = $this->load('Experiment Settings', $_files);
    }

    /**
     * Magic getter.
     * Attempts to retrieve the requested settings key from a prioritized list
     * of locations, returning the first found instance. Triggers an error if
     * the setting cannot be found.
     *
     * @param string $var The setting to retrieve.
     *
     * @return mixed The value of the key.
     */
    public function __get($var)
    {
        $key = trim(strtolower($var));
        if ($key === 'password') {
            $pass = $this->get_password();

            return $pass;
        }

        // return values depending on their priority
        // if no value is found then trigger error and return null
        if (isset($this->experiment[$key])) {
            return $this->experiment[$key];
        } elseif (isset($this->common[$key])) {
            return $this->common[$key];
        } elseif (isset($this->def_exp[$key])) {
            return $this->def_exp[$key];
        } elseif (isset($this->def_common[$key])) {
            return $this->def_common[$key];
        } else {
            // @todo perhaps this should throw an \InvalidArgumentException instead?
            trigger_error('You have attempted to use the setting value of '
                ."{$key} when that value is not specified", E_WARNING);
            return null;
        }
    }

    private function load($system_data_label)
    {
        $data_source = ($system_data_label == 'Common Settings'
                     || $system_data_label == 'Experiment Settings') ?
                     $system_data_label : null;
        try {
            return $this->files->read($data_source);
        } catch (Exception $e) {
            return array();
        }
    }


    /**
     * Sets a new password to the password file.
     *
     * @param string $input The password to store.
     */
    public function set_password($input = null)
    {
        $input = ($input === null) ? $this->default_pass : $input;

        if (!isset($input[2])) {
            return;
            // @TODO: this should probably be an exception
        }

        $json_safe_pass = json_encode($input);
        $php_string = "<?php return json_decode($json_safe_pass) ?>";

        $this->files->overwrite("Password", $php_string);
    }

    /**
     * Retrieves the stored password.
     *
     * @return string|null The password if it could be found, else null.
     */
    protected function get_password()
    {
        $pass_path = $this->files->get_path('Password', array(), false);
        if (!is_file($pass_path)) return null;

        $password = require $pass_path;
        if ($password == $this->default_pass) return null;

        return $password;
    }

    /**
     * Overwrites a setting.
     * If this setting should be used only temporarily then simply set it as a
     * property of the settings instance you are working with. New values set
     * using Settings::set() will persist.
     *
     * @param string $var The settings key to set.
     * @param mixed  $val The value to set to the property.
     */
    public function set($var, $val)
    {
        $key = trim(strtolower($var));
        $location = (isset($this->def_common[$key])) ? 'common' : 'experiment';
        if ($location === 'common') {
            $source = &$this->common;
        } else {
            $source = &$this->experiment;
        }

        $current = $this->$key;
        $type = gettype($current);

        // only allow bools to replace bools
        if (is_bool($current)) {
            // toggling the posted strings of true/false to boolean true/false
            if ($val === 'true' || $val === 'false') {
                $val = ($val === 'true') ? true : false;
                $source[$key] = $val;
            } elseif ($val === true || $val === false) {
                $source[$key] = $val;
            } else {
                trigger_error("Your setting '{$key}' should be a boolean but it is, {$val}, a ({$type}):", E_WARNING);
            }

        // save number values as numbers
        } elseif (is_numeric($val)) {
            $int = (int)$val;
            $float = (float)$val;
            $val = ($int == $float) ? $int : $float;
            $source[$key] = $val;
        // allow all other types to be juggled
        } else {
            $source[$key] = $val;
        }
    }

    /**
     * Updates the actual settings files.
     * Will add and overwrite with any values set using Settings::set().
     * Temporary settings set as properties (using the magic __set()) will not
     * be written by Settings::write_settings().
     */
    public function write_settings()
    {
        // write common settings
        $common = array_merge($this->def_common, $this->common);
        $this->files->overwrite('Common Settings', $common);

        // write experiment settings if we actually have a path to write to
        try {
            $experiemnt = array_merge($this->def_exp, $this->experiment);
            $this->files->overwrite('Experiment Settings', $experiemnt);
        } catch (Exception $e) {
            echo "Error: You cannot write experiment settings until you select "
            . "an active experiment.<br>error_code:{$e}<br>";
        }
    }
}
