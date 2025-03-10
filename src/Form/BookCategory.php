<?php

namespace App\Form;

enum BookCategory: string
{
    case DC_CLASSIQUE = 'dc_classique';
    case DC_ESSENTIELS = 'dc_essentiels';
    case DC_RENAISSANCE = 'dc_renaissance';
    case DC_REBIRTH = 'dc_rebirth';
    case DC_BLACK_LABEL = 'dc_black_label';
    case DC_DELUXE = 'dc_deluxe';
    case DC_NEMESIS = 'dc_nemesis';

    public function label(): string
    {
        return match ($this) {
            self::DC_CLASSIQUE => 'DC Classique',
            self::DC_ESSENTIELS => 'DC Essentiels',
            self::DC_RENAISSANCE => 'DC Renaissance',
            self::DC_REBIRTH => 'DC Rebirth',
            self::DC_BLACK_LABEL => 'DC Black Label',
            self::DC_DELUXE => 'DC Deluxe',
            self::DC_NEMESIS => 'DC Nemesis',
        };
    }
}
