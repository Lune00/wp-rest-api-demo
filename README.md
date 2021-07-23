# Wordpress : API REST


## Monter le projet

- installer et configurer Traefik 
- A la racine du projet lancer

  `docker-compose up -d`
- Le projet est accessible sur `wp-rest-api.test`
- installer les plugins ACF, JWT Authentication for WP-API

## Contenu de la démo

Dans cette démo, on explore :

- exposition via l'API Rest d'un custom post type `Foobar` avec ses champs ACF
- ajout de nouvelles routes et endpoint sur un autre namespace
- validation, sanitization callback pour les arguments envoyés avec la requete
- mise en place de permissions sur les endpoints customs
- mise en place de l'authentification sur l'API REST par JWT Token
- mise en place du pattern Controller

## Exemples de requêtes

## Fonctions, Hooks et Objects Wordpress pour l'API REST

### Hooks

- [`rest_prepare_{$this->post_type}`](https://developer.wordpress.org/reference/hooks/rest_prepare_this-post_type/) : modifie la réponse. Utile pour attacher les champs ACF du post_type
- [`rest_{$this->post_type}_query`](https://developer.wordpress.org/reference/hooks/rest_this-post_type_query/) : filtre la query construite pour récupérer la ressource. Possibilité de mofifier les args de la WP_QUERY

- [`rest_api_init`](https://developer.wordpress.org/reference/hooks/rest_api_init/) : hook sur lequel enregistrer les routes avec `rest_register_route`. Permet de charger en mémoire que si l'API REST est chargée et utilisée.

### Fonctions


### Objects

- `WP_REST_Request` : l'objet envoyé à la callback inscrite sur le endpoint. Contient tous les arguments passés en paramètre de la requete

- `WP_REST_Response` : l'objet retourné. Converti par WP API REST en JSON, retourné par le client.


## Définitions et concepts de base

### Endpoints

Association **d'une URI et d'une méthode**. A ce couple est mappé une fonction à éxecuter, la `callback`.

`{nom de domaine}/wp-json` : liste tous les endpoints

### Requetes

Une requête à l'API REST de WP est representée par l'instance d'une classe `WP_REST_Request`.

>A `WP_REST_Request` object is automatically generated when you make an HTTP request to a registered API route. The data specified in this object (derived from the route URI or the JSON payload sent as a part of the request) determines what response you will get back out of the API.

### Réponses

>Responses are the data you get back from the API. The `WP_REST_Response` class provides a way to interact with the response data returned by endpoints. Responses return the requested data, or can also be used to return errors if something goes wrong while fulfilling the request.

### API Schema

>Each endpoint requires a particular structure of input data, and returns data using a defined and predictable structure. Those data structures are defined in the API Schema. The schema structures API data and provides a comprehensive list of all of the properties the API can return and which input parameters it can accept. Well-defined schema also provides one layer of security within the API, as it enables us to validate and sanitize the requests being made to the API. The Schema section explores this large topic further.

### Controller Classes

>Controller classes unify and coordinate all these various moving parts within a REST API response cycle. With a controller class you can manage the registration of routes & endpoints, handle requests, utilize schema, and generate API responses. A single class usually contains all of the logic for a given route, and a given route usually represents a specific type of data object within your WordPress site (like a custom post type or taxonomy).

## Global Parameters

>The API includes a number of global parameters (also called “meta-parameters”) which control how the API handles the request/response handling. These operate at a layer above the actual resources themselves, and are available on all resources.

### `_fields`

Get a subset of fields

`/wp/v2/posts?_fields=author,id,excerpt,title,link`

### `_embed` et `_links`

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

>You may only need a small amount of data, but it’s important to keep in mind that the API is about exposing an interface to all clients, not just the feature you’re working on. **Changing responses is dangerous**.

>**Adding fields is not dangerous**, so if you need to modify data, it’s much better to duplicate the field instead with your modified data. **Removing fields is never encouraged**; if you need to get back a smaller subset of data, use the `_fields` parameter or work with contexts instead.


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

Remove infos. **Appliquée après validation**, si la donnée est valide.

>If we did not have strict validation (eg an enum of accepted values) and accepted any string as a parameter, we would **definitely need to register a sanitize_callback**.


### Permission Callback

>You must also register a permissions callback for the endpoint. This is a function that checks if the user can perform the action (reading, updating, etc) before the real callback is called. This allows the API to tell the client what actions they can perform on a given URL without needing to attempt the request first.

```
<?php
add_action( 'rest_api_init', function () {
  register_rest_route( 'myplugin/v1', '/author/(?P<id>\d+)', array(
    'methods' => 'GET',
    'callback' => 'my_awesome_func',
    'args' => array(
      'id' => array(
        'validate_callback' => 'is_numeric'
      ),
    ),
    'permission_callback' => function () {
      return current_user_can( 'edit_others_posts' );
    }
```

>**The permissions callback is run after remote authentication, which sets the current user.** This means you can use `current_user_can` to check if the user that has been authenticated has the appropriate capability for the action, or any other check based on current user ID. Where possible, you should always use `current_user_can`; **instead of checking if the user is logged in (authentication)**, check whether they can perform the action (authorization).


>This callback should return **a boolean or a WP_Error instance**. 

Fonctionne comme un filtre :

- si retourne vrai : la requete est executée (callback appelée)
- si retourne faux : acces a la ressource refusé (pas d'appel callback), retourne un message d'erreur par défaut au client
- si retourne `WP_Error` : erreur retournée au client

Comme la vérificiation de l'autorization a comme condition préalable l'authentification, si on renseigne une callback de permission il **faut authentifier l'auteur de la requete avant**. Sinon on reçoit une erreur `rest_forbidden`.


### Return Response

La validation et la permission sont le premier niveau de défense : une requete peut être rejetée si les paramètres ne sont pas valides, si la méthode n'est pas autorisée etc. Les code status sont alors générés par l'API REST de WP.

On peut retourner :
- une valeur, automatiquement convertie au format JSON
- une `WP_Error`, si erreur. Par défaut renvoie un status code 500 (internal server 
error, generic). On peut préciser le status et le message

`return new WP_Error( 'no_author', 'Invalid author', array( 'status' => 404 ) );`

- une `WP_REST_Response` construite nous même (plus avancé)

```
<?php
$data = array( 'some', 'response', 'data' );
 
// Create the response object
$response = new WP_REST_Response( $data );
 
// Add a custom status code
$response->set_status( 201 );
 
// Add a custom header
$response->header( 'Location', 'http://example.com/' );
```

Le plus souvent, si aucune erreur à renvoyer, utiliser `rest_ensure_response($data)`. Code 200 par défaut

```
function say_hello(WP_REST_Request $request)
{
    $data = 'Hi ' . $request['name'];
    return rest_ensure_response($data);
}
```

>When wrapping existing callbacks, you should always use `rest_ensure_response()` on the return value. This will take raw data returned from an endpoint and **automatically turn it into a WP_REST_Response for you**. (Note that WP_Error is not converted to a WP_REST_Response to allow proper error handling.)

**Ne jamais appeler `wp_die(json_encode($data))` ou `wp_send_json($data)`**.

#### Status code

Quelques status codes utiles pour bien comprendre les réponses renvoyées par l'API.Réference [ici](https://restfulapi.net/http-status-codes/)

- 400 : bad input data. Si un paramètre ne passe pas la validation
- 403 : Forbiden, pas l'authorization
- 404 : endpoint n'existe pas, ressource non trouvable. Par exemple, si on appelle une route qui existe mais qui n'accepte que GET et qu'on la requete avec la méthode POST.


## Sécurité : protéger les endpoints

### Différence entre *Authentification* et *Authorization*

#### Authentification 

*Who you are ?* Verify you are who you say you are => Validating credentials (login)

#### Authorization 

What you have access to ? **Occurs after Authentification**. *Ok, you are that, let see what you can do in the system*.


### Authentifier la reqûete : système natif WP

Sous WP, l'authentification se fait sur la base de cookie. Quand on s'authentifie, on recoit un cookie et celui-ci est envoyé dans chaque requete pour authentifier l'utilisateur. Après on peut donc utiliser l'user connecté pour faire nos tests d'autorisations.

L'API REST embarque une technique de nonces (protection contre les CSRF).

Pour authentifier une requete AJAX envoyée par le Front, il va falloir passer à chaque requete 

- soit un paramètre `_wpnonce` (soit dans le body d'un POST soit dans l'url pour un GET)
- soit dans le header sous la clef `X-WP-Nonce`

>Note: Until recently, most software had spotty support for DELETE requests. For instance, PHP doesn’t transform the request body of a DELETE request into a super global. **As such, supplying the nonce as a header is the most reliable approach**.

>**It is important to keep in mind that this authentication method relies on WordPress cookies. As a result this method is only applicable when the REST API is used inside of WordPress and the current user is logged in**. In addition, the current user must have the appropriate capability to perform the action being performed.

En clair, on ne peut pas utiliser ce système que lorsqu'on est authentifié grâce à un cookie (le cookie sert a authentifier, le nonce **sert uniquement** à verifier que la requête est envoyée depuis un document servi par le serveur, et éviter les attaques CSRF. En gros elle enforce l'origin de la requête à être identique à celle du serveur). Utile pour développer du front JS servi par WP, ou des plugins. Mais dans le cas d'un Wordpress utilisé seulement comme une API consommé par un projet *Single Page App* on ne pourra pas s'en servir (car on ne se log pas sur le WP, on ne va jamais visiter son domaine directement).

Solution : pas de solution native pour le moment, utiliser un mode d'authentification implémenté par un plugin. Par exemple le mode *JWT Token*

### Authentification par JWT TOKEN

Solution sécuriée : utiliser le plugin [JSON Web Tokens](https://wordpress.org/plugins/jwt-authentication-for-wp-rest-api/)

#### Utilisation

Le plugin ajoute un nouveau namespace `/jwt-auth/v1` et deux endpoints :

- `/wp-json/jwt-auth/v1/token (POST)` : point d'entrée pour l'authentification. Permet de récupérer son token en échange de ses credentials (passé dans le body de la requete)

Exemple en Curl (à copier/coller directement dans le terminal, verifier que curl est installé)
```
curl -X POST \
  'http://wp-rest-api.test/wp-json/jwt-auth/v1/token' \
  -H 'Accept: */*' \
  -H 'User-Agent: Thunder Client (https://www.thunderclient.io)' \
  -H 'Content-Type: application/json' \
  -d '{"username": "username",
"password": "password"}'
```

- `/wp-json/jwt-auth/v1/token/validate (POST)` : endpoint pour valider un token manuellement


>The `wp-api-jwt-auth` will intercept every call to the server and will look for the Authorization Header, **if the Authorization header is present will try to decode the token and will set the user according with the data stored in it**. If the token is valid, the API call flow will continue as always.

Utiliser ce token pour toutes les requetes. À mettre dans chaque requete (header Authentification type Bearer Token).

Si le Token est invalide, JWT Plugin renvoie une erreur 403 : "Invalid Credentials" ou "Expired Token"

#### Durée de vie du token

Pour changer la durée de vie du Token, le plugin met à dispo un hook filter `jwt_auth_expire`. Valable 7 jours par défaut. L'expiration est défini par un timestamp. Si le timestamp courant est plus grand que le timestamp définit pour le token alors le token a expiré et n'est plus valide.


### Stratégie pour couvrir tous les endpoints

Si le plugin JWT ne voit pas le header Authorization avec le Token alors il laisse passer la requête. Si l'Authorization est présente avec le Token alors il vérifie et valide le token. Si valide, il authentifie l'user et le charge en mémoire dans l'user connecté de wordpress (et on peut faire notre travail).


Questions :

- Comment couvrir les endpoints par une necessite authentification ? Dois je le faire manuellement en déclarant une permission (avec la `permission_callback` pour implicitement demander une authentification) sur chaque endpoint?

Solutions possibles trouvées pour l'instant :

- ajouter a chaque endpoint une `permission_callback` avec `user_can(wp_get_current_user(), {capability})`. L'authentification est auto demandée

- mettre les customs endpoints dans le namespace du plugin JWT Token `jwt-auth/v1`?? Tous les endpoints de ce namespace sont protégés par le plugin. Si le Token n'est pas présent ou invalide il rejette la requête pour nous


Il semblerait qu'on ne puisse pas modifier (facilement) les politiques sur les endpoints fournies par wordpress. Il faudrait aller modifier, override les controleurs built-in qui gèrent ces endpoints (pour les posts, pages, custom post types etc...)


### CORS Policy

Comme on développe une API qui n'est pas publique et destinée seulement à être consommée par le front (origin du projet front), on ferait bien de configurer une CORS policy pour n'autoriser que l'origin du front à requêter l'API depuis le navigateur. Cette couche ajoute de la sécurité dans le navigateur, mais le SOP(Same Origin Policy) et le CORS (Cross Origin Ressource Sharing) **sont des spec implémentées par le navigateur uniquement**. Depuis un serveur ou un simple script il n'y en a pas. C'est fait pour sécuriser le web, dans le navigateur, uniquement. On ne peut pas uniquement compter dessus pour protéger les endpoints.

Par défaut **[Wordpress autorise toutes les origines à consommer son API REST](https://developer.wordpress.org/rest-api/frequently-asked-questions/#require-authentication-for-all-requests)**. L'API REST de Wordpress est utilisée par tout son front (ses écrans d'admin) mais la sécurité (éviter les CSRF) est gérée en utilisant une politique de [nonce](https://developer.wordpress.org/plugins/security/nonces/).

Il faut donc venir modifier la CORS Policy de l'API REST pour y mettre une whitelist (et y inclure l'origin du front).


## Adding REST API Support For Custom Content Types