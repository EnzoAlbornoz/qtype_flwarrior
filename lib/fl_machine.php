<?php

global $CFG;
require_once($CFG->dirroot . '/question/type/flwarrior/lib/fl_machine_test.php');

class fl_Machine
{
    // Define Constants
    const FINITE_STATE_MACHINE = "fsm";
    const PUSH_DOWN_MACHINE = "pdm";
    const TURING_MACHINE = "tm";
    // Define Attributes
    private ?int $id;
    private string $type;
    private array $states;
    private array $transitions;
    private fl_Alphabet $alphabet;
    // Define Constructor
    public function __construct(string $type, fl_Alphabet $alphabet , $states = array(), $transitions = array()) {
        $this->type = $type;
        $this->alphabet = $alphabet;
        $this->states = $states;
        $this->transitions = $transitions;
    }
    // Define Helpers
    /** @return fl_State|null */
    public function get_initial_state(): ?fl_State {
        $idx = flu_array_lambda_find_index($this->states, fn(fl_State $state) => $state->is_entry());
        if ($idx < 0) {
            return null;
        }
        return $this->states[$idx];
    }
    /** @return fl_State[] */
    public function get_exit_states(): array {
        return array_filter($this->states, fn(fl_State $state) => $state->is_exit());
    }
    // Define Execution Functions
    public function matches(fl_machine_test $test): bool {
        // Prepare Data
        $max_iterations = $test->max_iterations;
        $input_list = str_split($test->word);
        $should_match = $test->should_match;

        switch ($this->type) {
            case "fsm":
                return $this->fsm_matches($input_list, $max_iterations, $should_match);
            case "pdm":
                return $this->pdm_matches($input_list, $max_iterations, $should_match);
            case "tm":
                return $this->tm_matches($input_list, $max_iterations, $should_match);
            default:
                return false;
        }
    }

    private function fsm_matches(array $input, int $max_iterations, bool $should_match): bool {
        // Define execution threads
        $threads = array(
            array(
                "input" => $input,
                "state" => $this->get_initial_state()
            )
        );

        // Iterate
        for ($iter = 0; $iter < $max_iterations; $iter++) {
            // Check Done
            $done_thread_idx = flu_array_lambda_find_index($threads, fn($el) => empty($el['input']) && $el['state']->is_exit());
            if ($done_thread_idx >= 0) {
                return $should_match === true;
            }
            // From current execution threads, match what states can be reached
            $computed_thread_states = array_map(function($thread_state) {
                // Struct Thread State
                // \-> input = (string[]) pending input
                // \-> state = flState
                // Entry -> Thread State
                // Output -> Thread State[]

                // Verify transitions that match
                $filtered_transitions = array_filter(
                    $this->transitions,
                    function (fl_Transition $transition) use ($thread_state) {
                        $matches_from = $transition->get_state_from() === $thread_state['state'];
                        $matches_with = array_key_exists(0, $thread_state['input']) && $transition->get_with_head_symbol()->is_equal_to(fl_Symbol::symbol_for($thread_state['input'][0]));
                        $matches_with_lambda = $transition->get_with_head_symbol()->is_equal_to(fl_Symbol::EPSILON());
                        return $matches_from && ($matches_with || $matches_with_lambda);
                    }
                );
                // Compute Thread state and return it
                return array_map(function (fl_Transition $transition) use ($thread_state) {
                    return array(
                        "input" => $transition->get_with_head_symbol()->is_equal_to(fl_Symbol::EPSILON())
                            ? $thread_state['input']
                            : array_slice($thread_state['input'], 1),
                        "state" => $transition->get_state_to(),
                    );
                }, $filtered_transitions);
            }, $threads);
            // Store flatten iteration threads
            $threads = array_unique(array_merge(...$computed_thread_states), SORT_REGULAR);
        }
        // Max iterations reached
        return $should_match === false;
    }
    private function pdm_matches(array $input, int $max_iterations, bool $should_match): bool {
        // Define execution threads
        $threads = array(
            array(
                "input" => $input,
                "state" => $this->get_initial_state(),
                "stack" => array()
            )
        );
        // Iterate
        for ($iter = 0; $iter < $max_iterations; $iter++) {
            // Check Done
            $done_thread_idx = flu_array_lambda_find_index($threads, fn($el) => empty($el['input']) && $el['state']->is_exit());
            if ($done_thread_idx >= 0) {
                return $should_match === true;
            }
            // From current execution threads, match what states can be reached
            $computed_thread_states = array_map(function($thread_state) {
                // Struct Thread State
                // \-> input = (string[]) pending input
                // \-> state = flState
                // \-> stack = flSymbol[]
                // Entry -> Thread State
                // Output -> Thread State[]

                // Verify transitions that match
                $filtered_transitions = array_filter(
                    $this->transitions,
                    function (fl_Transition $transition) use ($thread_state) {
                        $matches_from = $transition->get_state_from() === $thread_state['state'];
                        $matches_with = array_key_exists(0, $thread_state['input']) && $transition->get_with_head_symbol()->is_equal_to(fl_Symbol::symbol_for($thread_state['input'][0]));
                        $matches_with_lambda = $transition->get_with_head_symbol()->is_equal_to(fl_Symbol::EPSILON());

                        $stack_values = array_values($thread_state['stack']);
                        $matches_stack = $transition->get_with_memory_symbol()->is_equal_to(fl_Symbol::EPSILON()) || $transition->get_with_memory_symbol()->is_equal_to(end($stack_values));
                        return $matches_from && $matches_stack && ($matches_with || $matches_with_lambda);
                    }
                );
                // Compute Thread state and return it
                return array_map(function (fl_Transition $transition) use ($thread_state) {
                    // Compute Stack
                    /** @var fl_Symbol[] $stack */
                    $stack = $thread_state['stack'];
                    if (!$transition->get_with_head_symbol()->is_equal_to(fl_Symbol::EPSILON())) {
                        array_pop($stack);
                    }
                    if (!$transition->get_write_symbol()->is_equal_to(fl_Symbol::EPSILON())) {
                        $stack[] = $transition->get_write_symbol();
                    }
                    // Return Thread State
                    return array(
                        "input" => $transition->get_with_head_symbol()->is_equal_to(fl_Symbol::EPSILON())
                            ? $thread_state['input']
                            : array_slice($thread_state['input'], 1),
                        "state" => $transition->get_state_to(),
                        "stack" => $stack,
                    );
                }, $filtered_transitions);
            }, $threads);
            // Store flatten iteration threads
            $threads = array_unique(array_merge(...$computed_thread_states), SORT_REGULAR);
        }
        // Max iterations reached
        return $should_match === false;
    }
    private function tm_matches(array $input, int $max_iterations, bool $should_match): bool {
        // Define execution threads
        $threads = array(
            array(
                "memory" => array_map(fn($el) => fl_Symbol::symbol_for($el), $input),
                "state" => $this->get_initial_state(),
                "mem_idx" => 0
            )
        );
        // Iterate
        for ($iter = 0; $iter < $max_iterations; $iter++) {
            // Check Done
            $done_thread_idx = flu_array_lambda_find_index($threads, fn($el) => $el['state']->is_exit());
            if ($done_thread_idx >= 0) {
                return $should_match === true;
            }
            // From current execution threads, match what states can be reached
            $computed_thread_states = array_map(function($thread_state) {
                // Struct Thread State
                // \-> memory = (string[]) pending input
                // \-> state = flState
                // \-> mem_idx = flSymbol[]
                // Entry -> Thread State
                // Output -> Thread State[]

                // Verify transitions that match
                $filtered_transitions = array_filter(
                    $this->transitions,
                    function (fl_Transition $transition) use ($thread_state) {
                        $matches_from = $transition->get_state_from() === $thread_state['state'];
                        $matches_with = array_key_exists($thread_state["mem_idx"], $thread_state['memory']) && $transition->get_with_head_symbol()->is_equal_to($thread_state['memory'][$thread_state["mem_idx"]]);
                        $matches_with_lambda = $transition->get_with_head_symbol()->is_equal_to(fl_Symbol::EPSILON());

                        return $matches_from && ($matches_with || $matches_with_lambda);
                    }
                );
                // Compute Thread state and return it
                return array_map(function (fl_Transition $transition) use ($thread_state) {
                    // Compute New Memory
                    $memory = $thread_state['memory'];
                    $mem_idx = $thread_state['mem_idx'];
                    // Update Write Symbol
                    $memory[$mem_idx] = $transition->get_write_symbol();
                    // Move Head
                    if ($mem_idx == 0 && $transition->get_head_direction() == fl_Transition::H_LEFT) {
                        array_unshift($memory, fl_Symbol::EPSILON());
                    } else if ($mem_idx == (count($thread_state['memory']) - 1) && $transition->get_head_direction() == fl_Transition::H_RIGHT) {
                        $memory[] = fl_Symbol::EPSILON();
                    } else if ($transition->get_head_direction() == fl_Transition::H_LEFT) {
                        $mem_idx--;
                    } else if ($transition->get_head_direction() == fl_Transition::H_RIGHT) {
                        $mem_idx++;
                    }
                    // Return Thread State
                    return array(
                        "memory" => $transition->get_with_head_symbol()->is_equal_to(fl_Symbol::EPSILON())
                            ? $thread_state['memory']
                            : array_slice($thread_state['memory'], 1),
                        "state" => $transition->get_state_to(),
                        "mem_idx" => $mem_idx,
                    );
                }, $filtered_transitions);
            }, $threads);
            // Store flatten iteration threads
            $threads = array_unique(array_merge(...$computed_thread_states), SORT_REGULAR);
        }
        // Max iterations reached
        return $should_match === false;
    }
}
