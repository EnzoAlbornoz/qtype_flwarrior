<?php

global $CFG;

require_once($CFG->dirroot . '/question/type/flwarrior/lib/utils.php');
require_once($CFG->dirroot . '/question/type/flwarrior/lib/fl_symbol.php');
require_once($CFG->dirroot . '/question/type/flwarrior/lib/fl_state.php');
require_once($CFG->dirroot . '/question/type/flwarrior/lib/fl_transition.php');
require_once($CFG->dirroot . '/question/type/flwarrior/lib/fl_alphabet.php');
require_once($CFG->dirroot . '/question/type/flwarrior/lib/fl_machine.php');

class JFFParser {
    private static function compute_head_direction(string $move) {
        if ($move === "L") {
            return fl_Transition::H_LEFT;
        } else if ($move === "R") {
            return fl_Transition::H_RIGHT;
        }
    }

    private static function compute_machine_type(string $type) {
        if ($type === "turing") {
            return fl_Machine::TURING_MACHINE;
        } else if ($type === "pda") {
            return fl_Machine::PUSH_DOWN_MACHINE;
        } else if ($type === "fa") {
            return fl_Machine::FINITE_STATE_MACHINE;
        }
        return null;
    }

    public static function parse_string(string $str) {
        // Instantiate Document
        $xml = new DOMDocument();
        // Parse XML
        $xml->loadXML($str);
        // Build Objects Based on JFF file format
        // Read Machine Type
        $xml_machine_type = $xml->getElementsByTagName("type")->item(0);
        if ($xml_machine_type == null) {
            return null;
        }
        /** @var string $machine_type */
        $machine_type = $xml_machine_type->textContent;
        // Read Machine States
        $xml_states = $xml->getElementsByTagName("state");
        $states = array_map(function(DOMElement $el) {
            $is_init = $el->getElementsByTagName("initial")->length > 0;
            $is_exit = $el->getElementsByTagName("final")->length > 0;
            return new fl_State($el->getAttribute("id"), $is_init, $is_exit);
        }, iterator_to_array($xml_states));
        $states_obj = array_reduce($states, function($acc, fl_State $state) {
            $acc[$state->get_label()] = $state;
            return $acc;
        } , array());
        // Read Symbols
        $xml_symbols = array_merge(
            iterator_to_array($xml->getElementsByTagName("read")),
            iterator_to_array($xml->getElementsByTagName("write")),
            iterator_to_array($xml->getElementsByTagName("pop")),
            iterator_to_array($xml->getElementsByTagName("push")),
        );
        print_r($xml_symbols);
        $symbols = array_map(function(DOMElement $el) {
            if ($el->textContent) {
                return fl_Symbol::symbol_for($el->textContent);
            }
            return null;
        }, $xml_symbols);
        $symbols = array_unique(
            array_filter(
                array_merge(
                    array(fl_Symbol::EPSILON()),
                    $symbols
                ),
                fn($el) => $el !== null
            )
        );
        $alphabet = new fl_Alphabet($symbols);
        // Read Machine Transitions
        $xml_transitions = $xml->getElementsByTagName("transition");
        $transitions = array_map(function(DOMElement $el) use($machine_type, $states_obj) {
            /** @var DOMElement $el_from */
            $el_from = $el->getElementsByTagName("from")->item(0);
            /** @var DOMElement $el_to */
            $el_to = $el->getElementsByTagName("to")->item(0);
            /** @var DOMElement|null $el_pop */
            $el_pop = $el->getElementsByTagName("pop")->item(0);
            /** @var DOMElement|null $el_pop */
            $el_push = $el->getElementsByTagName("push")->item(0);
            /** @var DOMElement $el_pop */
            $el_read = $el->getElementsByTagName("read")->item(0);
            /** @var DOMElement|null $el_pop */
            $el_write = $el->getElementsByTagName("write")->item(0);
            /** @var DOMElement|null $el_pop */
            $el_move = $el->getElementsByTagName("move")->item(0);

            $from = $states_obj[$el_from->textContent];
            $to = $states_obj[$el_to->textContent];
            $with_head_symbol = $el_read->textContent === ""
                ? fl_Symbol::EPSILON()
                : fl_Symbol::symbol_for($el_read->textContent);
            $with_memory_symbol = $el_pop != null
                ? $el_pop->textContent === ""
                    ? fl_Symbol::EPSILON()
                    : fl_Symbol::symbol_for($el_pop->textContent)
                : null;
            if ($machine_type === "turing") {
                $write_symbol = $el_write->textContent !== ""
                    ? fl_Symbol::symbol_for($el_write->textContent)
                    : fl_Symbol::EPSILON();
            } else if ($machine_type === "pda") {
                $write_symbol = $el_push->textContent !== ""
                    ? fl_Symbol::symbol_for($el_push->textContent)
                    : fl_Symbol::EPSILON();
            } else {
                $write_symbol = null;
            }
            $head_direction = $el_move !== null && $el_move->textContent !== ""
                ? JFFParser::compute_head_direction($el_move->textContent)
                : null;

            return new fl_Transition($from, $to, $with_head_symbol, $with_memory_symbol, $write_symbol, $head_direction);
        }, iterator_to_array($xml_transitions));
        // Create Machine
        $machine_type = JFFParser::compute_machine_type($machine_type);
        if ($machine_type === null) {
            return null;
        }
        return new fl_Machine($machine_type, $alphabet, $states, $transitions);
    }
}
