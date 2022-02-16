<?php

class fl_Machine
{
    // Define Constants
    const FINITE_STATE_MACHINE = "fsm";
    const PUSH_DOWN_AUTOMATA = "pdm";
    const TURING_MACHINE = "tm";
    // Define Attributes
    private ?int $id;
    private string $type;
    private array $states = array();
    private array $transitions = array();
    private fl_Alphabet $alphabet;
    // Define Constructor
    public function __construct(string $type, fl_Alphabet $alphabet , $states = array(), $transitions = array()) {
        $this->type = $type;
        $this->alphabet = $alphabet;
        $this->states = $states;
        $this->transitions = $transitions;
    }
    // Define Execution Functions
}
