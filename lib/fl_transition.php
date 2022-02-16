<?php

class fl_Transition
{
    // Define Constants
    const H_LEFT = "L";
    const H_RIGHT = "R";
    // Define Attributes
    private ?int $id;
    private fl_State $from;
    private fl_State $to;
    private fl_Symbol $with_head_symbol;
    private ?fl_Symbol $with_memory_symbol;
    private ?fl_Symbol $write_symbol;
    private ?string $head_direction;
    // Define Constructor
    public function __construct(
        fl_State $from,
        fl_State $to,
        fl_Symbol $with_head_symbol,
        ?fl_Symbol $with_memory_symbol = null,
        ?fl_Symbol $write_symbol = null,
        ?string $head_direction = null
    ) {
        $this->from = $from;
        $this->to = $to;
        $this->with_head_symbol = $with_head_symbol;
        $this->with_memory_symbol = $with_memory_symbol;
        $this->write_symbol = $write_symbol;
        $this->head_direction = $head_direction;
    }
    public function get_state_from() {
        return $this->from;
    }
    public function get_state_to() {
        return $this->to;
    }
    public function get_with_head_symbol() {
        return $this->with_head_symbol;
    }
    public function get_with_memory_symbol() {
        return $this->with_memory_symbol;
    }
    public function get_write_symbol() {
        return $this->write_symbol;
    }
    public function get_head_direction() {
        return $this->head_direction;
    }
}
