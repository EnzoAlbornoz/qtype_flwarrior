<?php
// This file is part of FLWarrior
//
// FLWarrior is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// FLWarrior is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with FLWarrior.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Moodle formal language question definition class.
 *
 * @package   qtype_flwarrior
 * @copyright 2021 Enzo Coelho Albornoz <enzocoelhoalbornoz@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Verify running on Moodle.
defined('MOODLE_INTERNAL') || die();
// Define plugin name.
$plugin->component = 'qtype_flwarrior';
// Define plugin release name
$plugin->release = 'v0.0.1';
// Define current version.
$plugin->version  = 2021060101;
// Define earliest version supported (Moodle 3.11).
$plugin->requires = 2021051703;
// Define the maturity of this plugin.
$plugin->maturity = MATURITY_ALPHA;
// Define dependencies.
$plugin->dependencies = [];
