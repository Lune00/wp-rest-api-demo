<?php

/**
 * Interface pour les équipements.
 */
interface IEquipment
{
    /**
     * Retourne la valeur du coût d'un équipement
     * @param int $nb : Nombre d'appareils
     * @param Indicator : Indicateur pour lequel le coût est demandé
     */
    public function value(int $nb, Indicator $indicator);
}

/**
 * Contiendra toutes les données métier d'un équipement
 */
class ElectricalEquipment implements IEquipment
{
    private string $id;
    private string $label;

    /**
     * Tableau d'IndicatorValues
     * @var IndicatorValue[]
     */
    private array $indicatorValues;

    public function __construct(string $id, string $label, array $indicatorValues)
    {
        $this->id = $id;
        $this->label = $label;

        //Todo, Parse Json avant
        $this->indicatorValues = array(
            new IndicatorValue(5, 7.4, new Indicator('changement_climatique', 'Changement Climatique', 'kg-eq-co2')),
            new IndicatorValue(5, 100.677902, new Indicator('ressources_fossiles', 'Ressources Fossiles', 'MJ')),
            new IndicatorValue(5, 0.000879116372425, new Indicator('ressources_minerales', 'Ressources minérales', 'kg-eq-sb'))
        );
    }

    private function getIndicatorValueById(Indicator $indicator)
    {

        foreach ($this->indicatorValues as $indicatorValue) {
            if ($indicatorValue->indicator->id === $indicator->id)
                return $indicatorValue;
        }
        return null;
    }

    public function value(int $nb, Indicator $indicator)
    {
        //Recuperer l'indicateur demandé
        $indcatorValue = $this->getIndicatorValueById($indicator);

        if (!isset($indcatorValue))
            throw new Exception('Indicateur Inconnu demandé pour equipement ' . $this->label);

        return $nb * $indcatorValue->value();
    }
}


/**
 * Un Indicateur de coût énergetique. Une simple structure qui regroupe un identifiant, un label et une unité. 
 * Chaque ressource, equipement etc... a un coût' energetique lié à indicateur
 */
class Indicator
{
    public string $id;
    public string $label;
    public string $unit;

    public function __construct(string $id, string $label, string $unit)
    {
        $this->id = $id;
        $this->label = $label;
        $this->unit = $unit;
    }
}

/**
 * Valeur d'un indicateur pour un item donné pour
 * une durée de vie $timeLife (en années)
 */
class IndicatorValue
{
    public int $baseTimeLife;
    private float $baseValue;
    public Indicator $indicator;

    public function __construct(int $baseTimeLife, float $baseValue, Indicator $indicator)
    {
        $this->baseTimeLife = $baseTimeLife;
        $this->baseValue = $baseValue;
        $this->indicator = $indicator;
    }

    /**
     * Réévalue l'indicateur sur une base d'une durée de vie différente
     * @param int $timeLife
     */
    public function evaluate(int $timeLife): float
    {
        return $this->baseValue * $timeLife / $this->baseTimeLife;
    }

    public function value()
    {
        if (!isset($this->baseValue))
            throw new Exception('Pas de valeur pour l\'indicateur ' . $this->indicator->label);
        return $this->baseValue;
    }
}
