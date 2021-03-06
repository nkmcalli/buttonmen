<?php
/**
 * BMSkillMood: Code specific to the mood die skill
 *
 * @author james
 */

/**
 * This class contains code specific to the mood die skill
 */
class BMSkillMood extends BMSkill {
    public static $hooked_methods = array('pre_roll', 'add_skill');

    public static function pre_roll(&$args) {
        if (!($args['die'] instanceof BMDie)) {
            return FALSE;
        }

        if (empty($args['die']->value)) {
            return FALSE;
        }

        if (array_key_exists('isSubdie', $args) && $args['isSubdie']) {
            return FALSE;
        }

        // do nothing if the die is not a swing die or a
        // twin die with swing components
        $die = $args['die'];
        if (!static::can_have_mood($die)) {
            return FALSE;
        }

        $swingRange = BMDieSwing::swing_range($die->swingType);
        $validSwingValueArray = static::valid_die_sizes($swingRange);
        $randIdx = bm_rand(0, count($validSwingValueArray) - 1);
        $newSwingValue = $validSwingValueArray[$randIdx];

        if ($die instanceof BMDieSwing) {
            $die->max = $newSwingValue;
        } elseif ($die instanceof BMDieTwin) {
            foreach ($die->dice as $subdie) {
                if ($subdie instanceof BMDieSwing) {
                    $subdie->max = $newSwingValue;
                }
            }
            $die->recalc_max_min();
        } else {
            throw new LogicException('Mood applied to non-swing die.');
        }

        return TRUE;
    }

    public static function valid_die_sizes(array $swingRange) {
        $allDieSizeArray = range($swingRange[0], $swingRange[1]);
        $validDieSizeArray = array_intersect($allDieSizeArray, BMDie::standard_die_sizes());
        return array_values($validDieSizeArray);
    }

    public static function add_skill($args) {
        if (!is_array($args) ||
            !($args['die'] instanceof BMDie)) {
            return;
        }
    }

    public static function can_have_mood($obj) {
        // Mood can only be added to swing dice and twin swing dice
        return ($obj instanceof BMDieSwing) ||
               (($obj instanceof BMDieTwin) &&
                (($obj->dice[0] instanceof BMDieSwing) ||
                 ($obj->dice[1] instanceof BMDieSwing)));
    }

    public static function do_print_skill_preceding() {
        return FALSE;
    }

    protected static function get_description() {
        return 'These are a subcategory of Swing dice, whose size ' .
               'changes randomly when rerolled. At the very start of the ' .
               'game (and again after any round they lose, just as with ' .
               'normal Swing dice) the player sets the initial size of Mood ' .
               'Swing dice, but from then on whenever they are rolled their ' .
               'size is set randomly to that of a "real-world" die (i.e. ' .
               '1, 2, 4, 6, 8, 10, 12, 20, or 30 sides) within the range ' .
               'allowable for that Swing type.';
    }

    protected static function get_interaction_descriptions() {
        return array(
            'Ornery' => 'Dice with both Ornery and Mood Swing have their sizes randomized during ornery rerolls',
        );
    }

    public static function prevents_win_determination() {
        return TRUE;
    }
}
