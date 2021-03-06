<?php

class fl_Symbol
{
    // Define attributes
    private static ?fl_Symbol $EPSILON_CONST = null;
    private static ?fl_Symbol $END_OF_STACK_CONST = null;
    private ?int $id = null;
    private string $symbol_raw;

    // Define constructor
    private bool $rep_epsilon = false;
    private bool $rep_end_of_stack = false;

    // Define Symbol public constructor

    private function __construct(string $symbol_raw, bool $epsilon = false, bool $end_of_stack = false)
    {
        $this->symbol_raw = $symbol_raw;

        if ($epsilon) {
            $this->rep_epsilon = $epsilon;
        }
        if ($end_of_stack) {
            $this->rep_end_of_stack = $end_of_stack;
        }
    }

    // Define methods

    public static function symbol_for($symbol_raw): fl_Symbol
    {
        return new fl_Symbol($symbol_raw);
    }

    public static function EPSILON(): fl_Symbol
    {
        if (!fl_Symbol::$EPSILON_CONST) {
            fl_Symbol::$EPSILON_CONST = new fl_Symbol("ε", true, false);
        }
        return fl_Symbol::$EPSILON_CONST;
    }

    public static function END_OF_STACK(): fl_Symbol
    {
        if (!fl_Symbol::$END_OF_STACK_CONST) {
            fl_Symbol::$END_OF_STACK_CONST = new fl_Symbol("$", true, false);
        }
        return fl_Symbol::$END_OF_STACK_CONST;
    }

    public function __toString()
    {
        return $this->symbol_raw . $this->rep_epsilon . $this->rep_end_of_stack;
    }

    // Define constants

    public function get_id(): ?int
    {
        return $this->id;
    }

    public function set_id(int $id)
    {
        $this->id = $id;
    }

    public function is_equal_to(fl_Symbol $other): bool
    {
        if ($this->rep_epsilon) {
            return $this->rep_epsilon == $other->rep_epsilon;
        }
        if ($this->rep_end_of_stack) {
            return $this->rep_end_of_stack == $other->rep_end_of_stack;
        }
        return !strcmp($this->symbol_raw, $other->symbol_raw);
    }

    public function cmp(fl_Symbol $other)
    {
        if ($this->rep_epsilon) {
            return $this->rep_epsilon == $other->rep_epsilon;
        }
        if ($this->rep_end_of_stack) {
            return $this->rep_end_of_stack == $other->rep_end_of_stack;
        }
        return strcmp($this->symbol_raw, $other->symbol_raw);
    }
}
