<?php
namespace Dorcas\ModulesFinanceTax\Http\Helpers;

use Carbon\Carbon;
use Stillat\Numeral\Languages\LanguageManager;
use Stillat\Numeral\Numeral;


class Helper {
    public static function MoneyConvert($cash, $type = null){
        $languageManager = new LanguageManager;
        // Create the Numeral instance.

        $formatter = new Numeral;
        // Now we need to tell our formatter about the language manager.

        $formatter->setLanguageManager($languageManager);

        if($type == "full"){
            $string = $formatter->format($cash, '0,0.00');
        }
        else {
            $string = $formatter->format($cash, '0,0a');
        }
        return $string;
    }
}
