<?php
/**
 * BMBtnSkillEcho: Code specific to Echo
 *
 * @author: james
 */

/**
 * This class currently supports the special skills of Echo
 */
class BMBtnSkillEcho extends BMBtnSkill {
    public static $hooked_methods = array('specify_recipes');

    public static function specify_recipes(array $args) {
        $areAllArgsPresent =
            array_key_exists('button', $args) &&
            array_key_exists('oppbutton', $args);

        if (!$areAllArgsPresent) {
            throw new LogicException('specify_recipes die hook is missing required input arguments');
        }

        $button = $args['button'];
        $oppbutton = $args['oppbutton'];

        if (is_null($oppbutton)) {
            return;
        }

        if (!($button instanceof BMButton) ||
            !($oppbutton instanceof BMButton)) {
            throw new LogicException('specify_recipes requires two BMButton input arguments');
        }

        if (empty($button->name)) {
            throw new LogicException('Button name may not be empty.');
        }

        // only copy recipe if the opponent button exists
        if (empty($oppbutton->name)) {
            return;
        }

        $newRecipe = $button->recipe;

        // copy opponent's recipe only if Echo hasn't yet got a recipe
        if ('' == $button->recipe) {
            $newRecipe = $oppbutton->recipe;

            if ('' == $newRecipe &&
                (
                    ('Echo' == $oppbutton->name) ||
                    ('Zero' == $oppbutton->name)
                )
               ) {
                // choose a default recipe for Echo/Zero vs Echo/Zero games
                $newRecipe = '(4) (4) (10) (12) (X)';
            }
        }

        $button->recipe = $newRecipe;
        $button->hasAlteredRecipe = TRUE;
    }
}
