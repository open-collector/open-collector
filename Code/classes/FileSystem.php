<?php

class FileSystem
{
    private $root = '.';
    private $map;
    private $defaults = array();
    private $validated_data_types = array();

    public function __construct()
    {
        $this->map = $this->get_system_map();
    }

    /**
     * Return systemMap data with all keys lowercased
     * @return array systemMap.php data structure
     */
    private function get_system_map()
    {
        // change key case defaults to return lowercased keys
        return array_change_key_case(
            require __DIR__ . '/systemMap.php'
        );
    }


    private function get_root() {
        $root  = $this->root;
        $count = 0;

        while (!is_file("$root/Code/classes/FileSystem.php")) {
            if (++$count === 1) {
                $root = '.';
            } else {
                $root .= '/..';
            }

            if ($count > 10) throw new Exception(
                'Could not find the FileSystem.php file expected in '
                . '/Code/classes/');
        }

        $this->root = $root;
        return $root;
    }

    /**
     * Return the path to a file with all variables and defaults replaced
     * Sources based on the keys from /Code/classes/systemMap.php
     * @param  [type]     $source    name of specific data source as defined in systemMap.php
     * @param  array|bool $variables list of replacements for [keys] in path,
                                     can be false to return raw path
     * @return string                relative path to source
     */
    public function get_path($source, $variables = array(), $throw_exception_when_path_is_incomplete = true)
    {
        $source = strtolower(trim($source));

        if (!isset($this->map[$source])) {
            throw new Exception("Specified source, '$source', does not exist in the system map", 1);
        } elseif ($variables === false) {
            // force the raw string from SystemMap.php
            return $this->map[$source][1];
        } else {
            if (!is_array($variables)) {
                $variables = array($variables);
            }
            $variables = array_merge($this->defaults, $variables);

            $var_replaced = fill_template(
                $this->map[$source][1], $variables, $throw_exception_when_path_is_incomplete
            );
            
            if ($var_replaced === false) return false;
            
            return $this->get_root() . "/$var_replaced";
        }
    }

    public function get_type($source)
    {
        $source = strtolower(trim($source));

        if (!isset($this->map[$source])) {
            throw new Exception("Specified source, '$source', does not exist in the system map", 1);
        }
        return $this->map[$source][0];
    }

    public function set_default($key, $val)
    {
        if ($key == "var") throw new Exception("You cannot set a default [var]", 1);

        $this->defaults[$key] = $val;
    }

    public function get_default($key)
    {
        return isset($this->defaults[$key]) ? $this->defaults[$key] : null;
    }

    private function access(
        $source,
        $command,
        $data = null,
        $index = null,
        $path_vars = array(),
        $throw_exception_when_path_is_incomplete = true
    ) {
        $path = $this->get_path($source, $path_vars, $throw_exception_when_path_is_incomplete);
        
        if ($path === false) return false;
        
        $class_name = 'fsDataType_' . $this->get_type($source);
        $this->validate_date_type($class_name);

        if ($command == "query") {
            return $class_name::$command($path, $index);
        }

        return $class_name::$command($path, $data, $index);
    }

    private function validate_date_type($dataType) {
        if (isset($this->validated_data_types[$dataType])) {
            return true;
        }

        $interfaces = class_implements($dataType);

        if (isset($interfaces['fsDataType_Interface'])) {
            $this->validated_data_types[$dataType] = true;
            return true;
        }

        throw new Exception("System map cannot use '$dataType', as it does not "
            . "implement 'fsDataType_Interface'.");
    }

    public function __sleep() {
        return array('root', 'defaults');
    }

    public function __wakeup() {
        $this->map = $this->get_system_map();
    }

/**
 * These are the functions that any data implementation need to have
 * to add another access method, add the method to ioAbstractDataType
 * and it will enforce that all datatypes implement that method
 * read, write, writeMany, overwrite, query
 */
    public function read(
        $source,
        $path_vars = array(),
        $throw_exception_when_path_is_incomplete = true
    ) {
        return $this->access(
            $source,
            'read',
            null,
            null,
            $path_vars,
            $throw_exception_when_path_is_incomplete
        );
    }

    public function write(
        $source,
        $data,
        $index = null,
        $path_vars = array(),
        $throw_exception_when_path_is_incomplete = true
    ) {
        return $this->access(
            $source,
            'write',
            $data,
            $index,
            $path_vars,
            $throw_exception_when_path_is_incomplete
        );
    }

    public function write_many(
        $source,
        $data,
        $path_vars = array(),
        $throw_exception_when_path_is_incomplete = true
    ) {
        return $this->access(
            $source,
            'write_many',
            $data,
            null,
            $path_vars,
            $throw_exception_when_path_is_incomplete
        );
    }

    public function overwrite(
        $source,
        $data,
        $index = null,
        $path_vars = array(),
        $throw_exception_when_path_is_incomplete = true
    ) {
        return $this->access(
            $source,
            'overwrite',
            $data,
            $index,
            $path_vars,
            $throw_exception_when_path_is_incomplete
        );
    }

    public function query(
        $source,
        $index,
        $path_vars = array(),
        $throw_exception_when_path_is_incomplete = true
    ) {
        return $this->access(
            $source,
            'query',
            null,
            $index,
            $path_vars,
            $throw_exception_when_path_is_incomplete
        );
    }
}