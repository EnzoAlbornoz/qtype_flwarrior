<?php

class fl_Alphabet {
    // Define Attributes
    private ?int $id = null;
    /* @type fl_Symbol[] $symbols */
    private array $symbols = array();
    private bool $symbols_sorted = false;
    // Define Constructor
    public function __construct(array $symbols = array()) {
        $this->symbols = $symbols;
    }
    public static function new_empty() {
        return new fl_Alphabet(array());
    }
    // Define Methods
    private function sort_symbols() {
        if (!$this->symbols_sorted) {
            usort($this->symbols, function(fl_Symbol $a, fl_Symbol $b) {
                return $a->cmp($b);
            });
        }
    }
    public function set_id(int $id) {
        $this->id = $id;
    }
    public function get_id(): ?int {
        return $this->id;
    }
    public function is_equal_to(fl_Alphabet $other): bool {
        if (count($other->symbols) != count($this->symbols)) {
            return false;
        }

        $this->sort_symbols();
        $other->sort_symbols();

        for ($idx = 0; $idx < count($this->symbols); $idx++) {
            if(!($this->symbols[$idx]->is_equal_to($other->symbols[$idx]))) {
                return false;
            }
        }
        return true;
    }

    /**
     * @return fl_Symbol[]
     */
    public function get_symbols() {
        return $this->symbols;
    }

    public function add_symbol(fl_Symbol $symbol) {
        foreach ($this->symbols as $self_symbol) {
            if ($self_symbol->is_equal_to($symbol)) {
                return;
            }
        }

        $this->symbols[] = $symbol;
        $this->symbols_sorted = true;
    }

    public function remove_symbol(fl_Symbol $symbol) {
        for ($idx = 0; $idx < count($this->symbols); $idx++) {
            if ($this->symbols[$idx]->is_equal_to($symbol)) {
                $this->symbols = array_slice($this->symbols, $idx, 1);
            }
        }
    }

}
