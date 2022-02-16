<?php

class fl_State
{
    // Define Attributes
    private ?int $id;
    private string $label;
    private bool $is_entry;
    private bool $is_exit;
    // Define Constructor
    public function __construct(string $label, bool $is_entry, bool $is_exit) {
        $this->label = $label;
        $this->is_entry = $is_entry;
        $this->is_exit = $is_exit;
    }
    // Define Functions
    public function set_id(int $id) {
        $this->id = $id;
    }
    public function get_id() {
        return $this->id;
    }
    public function set_as_entry(bool $is_entry) {
        $this->is_entry = $is_entry;
    }
    public function set_as_exit(bool $is_exit) {
        $this->is_exit = $is_exit;
    }
    public function is_entry() {
        return $this->is_entry;
    }
    public function is_exit() {
        return $this->is_exit;
    }

}
