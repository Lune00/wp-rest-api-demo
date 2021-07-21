# Wordpress : API REST

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