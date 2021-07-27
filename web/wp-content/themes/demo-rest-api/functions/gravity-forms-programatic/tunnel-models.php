<?php

/**
 * Contiendra toutes les donnée métier
 */
class ElectricalEquipment{

    /**
     * Correspond a l'input_name dans le Form Gravity Form
     */
    public string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }
}


