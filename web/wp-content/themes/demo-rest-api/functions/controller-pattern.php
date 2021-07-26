<?php

/**
 * Une Interface d'un Controller pour des endpoints customs non dédiés à du CRUD (dans ce cas là on préferera utiliser les CPT et le Controller natif de WP pour les posts ou implémenter directement sa classe abstraite). Non dédié à du CRUD au sens où on ne va pas récupérer une collection d'items ou insérer dedans, mais récupérer des données spécifiques hors CRUD (par ex la page de login, une structure de donnée reconstruite etc...)
 * source : https://developer.wordpress.org/rest-api/extending-the-rest-api/controller-classes/
 */

/**
 * WIP (on le fera plus tard)
 */
 abstract class Controller{

    private string $namespace;
    private string $route;

    public function __construct($namespace, $route)
    {
        $this->namespace = $namespace;
        $this->route = $route;
    }

    private function init(){
        add_action('rest_api_init', array($this, 'register_routes'));
    }
    public abstract function register_routes();
    public abstract function permission_check();

 }