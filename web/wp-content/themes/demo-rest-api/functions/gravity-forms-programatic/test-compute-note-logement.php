<?php


/**
 * Supposons qu'on a toutes les informations pour calculer la note dans un tableau
 */

require './tunnel-models.php';



/**
 * Fonction qui lit un fichier json sur le disque, et retourne le contenu parsé en array associative php,
 * leve une exception s'il y a un problème
 */
function load_json_ressource(string $path){

    //Exception si un pb à la lecture du fichier
    $file_path = file_get_contents(get_template_directory() . '/functions/gravity-forms-programatic/' . $path);
    $json_data = json_decode($file_path, true);

    if (!isset($entities))
    throw new Exception('Impossible de décoder le JSON ' . $path);

    return $json_data;
}

/**
 * Fonction Impure (lit un fichier)
 */
function load_equipments(string $ressource_json)
{

    $data = file_get_contents(get_template_directory() . '/functions/gravity-forms-programatic/' . $ressource_json);

    //On parse le JSON pour récupérer les entités
    //On map le JSON a un array
    $entities = json_decode($data, true);

    if (!isset($entities))
        throw new Exception('Impossible de décoder le JSON ' . $ressource_json);

    $used_entities = array_filter($entities, function ($entity) {
        return $entity['isUsed'];
    });

    //On valide les champs et les indicateurs

    //On recupere que ce qui nous interesse
    $used_equipments = array_map(function ($entity) {
        return array(
            'id' => $entity['id'],
            'label' => $entity['label'],
            'weight' => $entity['weight']
        );
    }, $used_entities);

    //On map aux entités
    $models = array_map(function ($equipment_data) {
        //Todo
        $indicator = new Indicator();
        $indicatorValue = new IndicatorValue();
        return new ElectricalEquipment($equipment_data['id'], $equipment_data['label'], $indicatorValue);
    }, $used_equipments);

    return $models;
}


function load_indicators(string $ressource_json){

    //Todo
    return array();
}

//Début du test

$input = array(
    'ElectricalEquipments' => array(
        'enceinte_connectee' => 1,
    )
);


//On charge toutes les données JSON et on crée les models. On ne charge que ceux utilisé pour le calcul (key 'isUsed')
$equipments = load_equipments('electrical-equipments.json');

//On charge tous les indicateurs
$indicators = load_indicators('indicateurs.json');


//A partir de là c'est testable et pur

try {

    $costs = array_map(function ($equipment) use ($input) {

        //On cherche la clef dans l'input
        if (!isset($input['ElectricalEquipments'][$equipment->id])) {
            throw new Exception('Impossible de trouver la donnée pour ' . $equipment->label);
        }

        $value = $input['ElectricalEquipments'][$equipment->id];

        //Todo
        //Une structure qui contient un cout pour chaque Indicateur (Todo Recuperer la liste des indicateurs demandés)
        //Pour chaque indicateur retourné le cout
        $indicator = array();

        return $equipment->value($value, $indicator);

    }, $equipments);
} catch (Exception $e) {
    var_dump($e->getMessage());
    return new WP_Error('400', 'Impossible de calculer la note, données manquantes');
}


//On a tous les couts

//A partir de la médiane, mapper le cout à la note subjective

//A partir de la note subjective, mapper la note a l'étiquette