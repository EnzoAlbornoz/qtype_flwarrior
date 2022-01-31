<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Question type class for the flwarrior question type.
 *
 * @package   qtype_flwarrior
 * @copyright 2021 Enzo Coelho Albornoz <enzocoelhoalbornoz@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;

class fl_machine_test
{
    public ?int $id;
    public string $word;
    public bool $should_match;
    public int $max_iterations;
    public ?int $question_id;

    public function __construct(
        ?int $id,
        string $word,
        bool $should_match = true,
        int $max_iterations = 1000,
        ?int $question_id = null
    ) {
        $this->id = $id;
        $this->word = $word;
        $this->should_match = $should_match;
        $this->max_iterations = $max_iterations;
        $this->question_id = $question_id;
    }

    static function from_db_entry($db_entry): fl_machine_test {
        return new fl_machine_test(
            $db_entry->id,
            $db_entry->word,
            boolval($db_entry->should_match),
            $db_entry->max_iterations,
            $db_entry->question_id
        );
    }
}
