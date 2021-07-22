# Wordpress : API REST

## Contenu de la démo

Dans cette démo, on explore :

- exposition via l'API Rest d'un custom post type `Foobar` avec ses champs ACF
- ajout de nouvelles routes et endpoint sur un autre namespace
- validation, sanitization callback pour les arguments envoyés avec la requete

## Exemples de requêtes

## Fonctions, Hooks et Objects Wordpress pour l'API REST

### Hooks

- [`rest_prepare_{$this->post_type}`](https://developer.wordpress.org/reference/hooks/rest_prepare_this-post_type/) : modifie la réponse. Utile pour attacher les champs ACF du post_type
- [`rest_{$this->post_type}_query`](https://developer.wordpress.org/reference/hooks/rest_this-post_type_query/) : filtre la query construite pour récupérer la ressource. Possibilité de mofifier les args de la WP_QUERY

- [`rest_api_init`](https://developer.wordpress.org/reference/hooks/rest_api_init/)

### Fonctions


### Objects

`WP_REST_Request` : l'objet envoyé à la callback inscrite sur le endpoint. Contient tous les arguments passés en paramètre de la requete


## Définitions et concepts de base

### Endpoints

Association d'une URI à une méthode

`{nom de domaine}/wp-json` : liste tous les endpoints

### Requetes

Une requête à l'API REST de WP est representée par l'instance d'une classe `WP_REST_Request`.

A WP_REST_Request object is automatically generated when you make an HTTP request to a registered API route. The data specified in this object (derived from the route URI or the JSON payload sent as a part of the request) determines what response you will get back out of the API.

### Réponses

Responses are the data you get back from the API. The `WP_REST_Response` class provides a way to interact with the response data returned by endpoints. Responses return the requested data, or can also be used to return errors if something goes wrong while fulfilling the request.

### API Schema

Each endpoint requires a particular structure of input data, and returns data using a defined and predictable structure. Those data structures are defined in the API Schema. The schema structures API data and provides a comprehensive list of all of the properties the API can return and which input parameters it can accept. Well-defined schema also provides one layer of security within the API, as it enables us to validate and sanitize the requests being made to the API. The Schema section explores this large topic further.

### Controller Classes

Controller classes unify and coordinate all these various moving parts within a REST API response cycle. With a controller class you can manage the registration of routes & endpoints, handle requests, utilize schema, and generate API responses. A single class usually contains all of the logic for a given route, and a given route usually represents a specific type of data object within your WordPress site (like a custom post type or taxonomy).

### Global Parameters

The API includes a number of global parameters (also called “meta-parameters”) which control how the API handles the request/response handling. These operate at a layer above the actual resources themselves, and are available on all resources.


### Différence entre Authentification et Authorization

#### Authentification 

who you are ? Verify you are who you say you are => Validating credentials

#### Authorization 

What you have access to ? Occurs after Authentification. Ok, you are that, let see what you can do in the system.

#### `_fields`

Get a subset of fields

`/wp/v2/posts?_fields=author,id,excerpt,title,link`

#### `_embed` et `_links`

Embed embedable related resources. Sous la clé `_links` on peut voir les ressources liées à la ressource demandée. `_links` envoient uniquement les URI des ressources liées, pas les ressources elles-mêmes. Si un item de `_links` a une clé `embeddable": true` alors il peut être embarqués dans la même requête. On peut alors faire la requête

`/wp/v2/posts?_embed`

Elle embarquera par défaut tout ce qui est embedable.

Note : pour les custom post types il faut explicitement déclarer sous la clé `supports` les champs autorisés à être exposés via l'API. Par exemple 

 `'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'author')`

Si on enlève `author` alors bien que author soit embedable, il ne le sera pas s'il n'est pas déclaré sous cette clef.

On peut aussi faire un usage restrictif de `_embed`. Par exemple

`/wp/v2/posts?_embed=author`

ne va embarquer dans la réponse que l'auteur et pas les autres ressources embedables


## Extending the REST API

### On Modifying Responses

You may only need a small amount of data, but it’s important to keep in mind that the API is about exposing an interface to all clients, not just the feature you’re working on. **Changing responses is dangerous**.

**Adding fields is not dangerous**, so if you need to modify data, it’s much better to duplicate the field instead with your modified data. **Removing fields is never encouraged**; if you need to get back a smaller subset of data, use the `_fields` parameter or work with contexts instead.


### Adding Custom Endpoints Doc

See [doc](https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/)


### Exemple
```
<?php
add_action( 'rest_api_init', function () {
  register_rest_route( 'myplugin/v1', '/author/(?P<id>\d+)', array(
    'methods' => 'GET',
    'callback' => 'my_awesome_func',
    'args' => array(
      'id' => array(
        'validate_callback' => function($param, $request, $key) {
          return is_numeric( $param );
        },
         'sanitize_callback' => function( $value, $request, $param ) {
                // It is as simple as returning the sanitized value.
                return sanitize_text_field( $value );
        },

      ),
    ),
  ) );
} );
```


### Path Variable

Quand on ajoute une route, on peut mettre un pattern pour match un path. Par exemple

`/author/(?P<id>\d+)`

Ici `(?P<id>\d+)` est une *[path variable](https://developer.wordpress.org/rest-api/extending-the-rest-api/routes-and-endpoints/#path-variables)*, elle permet de créer des routes dynamiquement. Path variable est une expression régulière. Ici `\d+` indique qu'on attend une valeur numérique avec au moins un chiffre. `<id>` indique ici le nom que l'on retrouve dans l'objet `WP_REST_REQUEST` (passé à notre callback), dans les paramètres d'url (`$request->get_url_params()`). Le nom définit la clé sous laquelle la valeur est enregistrée.

Format : `?P<{name}>{regex pattern}`


### Validation callback

Inspect data and validate. **A lieu avant la sanitization si elle existe.**

### Sanitize callback

Remove infos. Appliquée après validation, si la donnée est valide.

If we did not have strict validation (eg an enum of accepted values) and accepted any string as a parameter, we would **definitely need to register a sanitize_callback**.


### Return response

