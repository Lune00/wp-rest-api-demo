<?php

/**
 * Ici on veut profiter du Controller par défaut de Wordpress sur le CRUD de notre custom post type. Mais on voudrait ajouter des champs au schéma défini par défaut par WP. Par exemple, on voudrait exposer dans le schéma des champs ACF. 
 * 
 * On va utiliser la fonction register_rest_field. A voir comment ça se lie avec les champs ACF que jajoute en plus à la réponse avec rest_prepare_foobar. Si ca se trouve je n'en aurai plus besoin ? La valeur sera auto exportée dans le schéma. Ou alors il faudra le faire quand même manuellement. 
 * 
 * Source : https://stackoverflow.com/questions/52709167/wordpress-rest-api-how-to-get-post-schema
 * https://developer.wordpress.org/reference/functions/register_rest_field/
 */