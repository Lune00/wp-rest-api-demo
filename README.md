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
- custom endpoint avec validation JSON schéma (réponse retour) et validation arguments (PHP)

## Exemples de requêtes

Récupérer un post type avec seulement certains champs

`{{api-rest-uri}}/posts?_fields=author,id,title,link`

Récuperer un custom post-type foobar avec champs ACF et l'auteur (pour prendre un embed avec _fields il faut inclure les _links)

`{{api-rest-uri}}/foobar?_fields=acf,acf_all,_links&_embed=author`

Récuperer tous les titres des custom post types qui appartiennent au terme 'fantasy' (ID 4) de la custom taxo 'genre'

`{{api-rest-uri}}/foobar?genre=4&_fields=title`

Récuperer tous les titres des custom post types qui appartiennent au terme 'fantasy' (ID 4) OU au terme 'fiction' (ID 3) de la custom taxo 'genre'

`{{api-rest-uri}}/foobar?genre=4,3&_fields=title`

Récuperer l'id d'un term a partir de son slug

`{{api-rest-uri}}/genre?slug=fantasy&_fields=id`

## Fonctions, Hooks et Objects Wordpress pour l'API REST

### Hooks

- [`rest_prepare_{$this->post_type}`](https://developer.wordpress.org/reference/hooks/rest_prepare_this-post_type/) : modifie la réponse. Utile pour attacher les champs ACF du post_type
- [`rest_{$this->post_type}_query`](https://developer.wordpress.org/reference/hooks/rest_this-post_type_query/) : filtre la query construite pour récupérer la ressource. Possibilité de mofifier les args de la WP_QUERY
- [`rest_api_init`](https://developer.wordpress.org/reference/hooks/rest_api_init/) : hook sur lequel enregistrer les routes avec `rest_register_route`. Permet de charger en mémoire que si l'API REST est chargée et utilisée.
- `register_rest_field` : ajouter un champ sur un objet WP (post, taxo) a exposer via l'API (mettre a jour le schema)
- `register_rest_route` : ajouter un endpoint custom

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

### `_envelope`

Passe les paramètres de retour dans le body (incluant header et status code)

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

### Schémas : A utiliser !

>The schema for a resource indicates what fields are present for a particular object.

On peut définnir des schémas pour les arguments des endpoints (au lieu de définir un couple sanitize/validatation sur chaque champ). Ils définissent alors ce que l'API attend comme données en entrée (POST). Est ce que ça dit ce qu'elle renvoie aussi ? Il me semble que le schéma est validé à la fois en entrée et en sortie

>The WordPress REST API utilizes this system for describing the request and response formats for each endpoint.

>Schema provides machine readable data, so potentially anything that can read JSON can understand what kind of data it is looking at. When we look at the API index by making a GET request to https://ourawesomesite.com/wp-json/, we are returned the schema of our API, enabling others to write client libraries to interpret our data. This process of reading schema data is known as discovery. When we have provided schema for a resource we make that resource discoverable via OPTIONS requests to that route. Exposing resource schema is only one part of our schema puzzle. We also want to use schema for our registered arguments.

>As your codebase and endpoints grow, schema will help keep your code lightweight and maintainable. Without schema you can validate and sanitize, however it will be more difficult to keep track of which functions should be validating what. By adding schema to request arguments we can also expose our argument schema to clients, so validation libraries can be built client side which can help performance by preventing invalid requests from ever being sent to the API.

Les schémas évitent d'avoir à coder bcp de sanitize et validation custom. Si on passe par les schémas JSON, en place d'ête plus concis car Wordpress implemente un validateur de schéma JSON (validation et sanitization) de la spec [JSON Schema](https://json-schema.org/specification-links.html#draft-4), on documente notre API pour les humains et les machines (discovery facilité) et on la rend bcp plus maintenable !


### Déclarer un schéma de la ressource

Ajouter la clé `'schema' => 'callback'`, au moment de l'enregistrement du endpoint. La callback doit retourner un schéma (un array) avec des clés définies par la spec. Exemple de déclaration d'un endpoint avec un schéma

```
 // Register our routes.
function prefix_register_my_comment_route() {
    register_rest_route( 'my-namespace/v1', '/comments', array(
        // Notice how we are registering multiple endpoints the 'schema' equates to an OPTIONS request.
        array(
            'methods'  => 'GET',
            'callback' => 'prefix_get_comment_sample',
        ),
        // Register our schema callback.
        'schema' => 'prefix_get_comment_schema',
    ) );
}
```

Pour les custom post types, si on utilise le Controller par défaut de Wordpress, comme pour les posts, **alors le schéma est défini par Wordpress (il faudra regarder comment on peut le custommiser. [Ici](https://stackoverflow.com/questions/52709167/wordpress-rest-api-how-to-get-post-schema) peut être des idées)**. On peut voir le schéma si on reqûete la ressource avec la méthode `OPTIONS`

`http://wp-rest-api.test/wp-json/wp/v2/book, method : OPTIONS`


### 2 schémas : un schéma Ressource (sortie/retour), un shéma Arguments(inputs)

Soit on étend un Controller par défaut de Wordpress (par ex pour un custom post type), soit on fait un endpoint complètement custom sans Controller. Le 2eme cas peut se justifier si on a pas besoin d'exposer un CRUD mais juste une fonction. 

#### Route custom sans Pattern *Controller*

Le mieux est de :
- donner une clef `schema` avec un schéma JSON. Il sert a exposer la ressource (méthode OPTIONS, clé `schema` sur la route), donner de l'info sur l'API. Le schéma pourra aussi être utilisé pour servir à la validation => **Définit l'output, ce que va récuperer le client**
- définir le schéma des arguments avec la clef `args`. Définit le schéma des arguments attendus par le endpoint (peut être différent du schéma Discovery) => **Définit l'input, ce que doit envoyer le client** (méthode GET sur la route, clé `args`)

```
register_rest_route(
        'myplugin/v1',
        '/say-hello/',
        //On enregistre plusieurs routes pour mettre le schema,
        //le schema equivaut (implicite) à une route pour la méthode OPTIONS
        array(
            array(
                'methods'  =>  WP_REST_Server::READABLE,
                'callback' => 'answer_you',

                //Schema pour les arguments (body POST ou url GET)
                //Si on requete la route avec la méthode OPTIONS
                //on voit les arguements demandés
                //Permet au client de savoir ce qu'il doit envoyer (Entrée)
                'args' => array(
                    'name' => array(
                        'sanitize_callback' => function ($value, $request, $param) {          
                        },
                        'validate_callback' => function ($param, $request, $key) {
                        },
                        'description' => 'Argument Schema',
                        'required' => true
                    )
                )
            ),
            //Schema pour Discovery (donne une description de la ressource, visible avec 
            //requete OPTIONS)
            //Permet au client de savoir ce qu'il va récupérer (Sortie)
            'schema' => 'get_schema',
        )
    );


function get_schema()
{
    $schema = array(
        // This tells the spec of JSON Schema we are using which is draft 4.
        '$schema'              => 'http://json-schema.org/draft-04/schema#',
        // The title property marks the identity of the resource.
        'title'                => 'question',
        'type'                 => 'object',
        // In JSON Schema you can specify object properties in the properties attribute.
        'properties'           => array(
            'name' => array(
                'description'  => esc_html__('Votre nom', 'my-textdomain'),
                'type' => 'string',
                'required' => true
            ),
            'age' => array(
                'description'  => esc_html__('Votre âge', 'my-textdomain'),
                'type' => 'integer',
                'minimum' => 18,
                'maximum' => 2000,
                'required' => true
            )
        ),
    );

    return $schema;
}


```

Si on étend un Controller de WP pour faire du CRUD, alors il utilise automatiquement le schéma JSON pour valider/sanitize les arguments du endpoint. On peut ajouter quand même une fonction de sanitization custom comme ici avec la clef `arg_options`

```
'schema' => array(
            'arg_options' => array(
                'sanitize_callback' => function ($value) {
                    //On fait une sanitization nous même car JSON Format ne le fait pas
                    if (isset($value['custom_field_text']))
                        $value['custom_field_text'] = sanitize_text_field($value['custom_field_text']);
                    return $value;
                },
            ),
            'description' => __('Champs customs', 'demo'),
            'type' => 'object',
            'properties' => array(
                'custom_field_text' => array(
                    'type' => 'string',
                    'required' => true,
                    'description' => 'Qui êtes vous ?',
                ),
            )
)
...
```


**A noter que le Schéma de Ressource (pour le retour) n'enforce pas la valeur de retour ! C'est juste de l'information pour le client. Mais si on renvoit une réponse differente il n'y aura pas d'erreur de levée. C'est a nous de nous assurer que la valeur de retour correspond au schéma Ressoure !** Pour cela on peut valider le schéma dans la callback
```
   $valid_response = rest_validate_value_from_schema($response, $schema, '');
```

### Utiliser un schéma : validation et sanitazation

>The REST API defines two main functions for using JSON Schema: `rest_validate_value_from_schema` and `rest_sanitize_value_from_schema`. Both functions accept the request data as the first parameter, the parameter’s schema definition as the second parameter, and optionally the parameter’s name as the third parameter. The validate function returns either true or a WP_Error instance depending on if the data successfully validates against the schema. The sanitize function returns a sanitized form of the data passed to the function, or a WP_Error instance if the data cannot be safely sanitized.

>When calling these functions, **you should take care to always first validate the data using rest_validate_value_from_schema, and then if that function returns true, sanitize the data using rest_sanitize_value_from_schema**. Not using both can open up your endpoint to security vulnerabilities.

On valide, si c'est validé on sanitize (si on sanitize et que c'est pas valide alors on fait du travail en plus pour rien. Mais faire attention à la sanitization car en général en enleve de l'info et on peut rendre la donnée invalide).


### Etendre le schéma d'un Custom Post Type

On peut aussi étendre le schéma d'un custom post type (pour y inclure les champs ACF par ex). Dans ce cas là on bénéficie de tout le travail fait par le controller par défaut de WP [WP_REST_Posts_Controller](https://developer.wordpress.org/reference/classes/wp_rest_posts_controller/). 

Pour étendre le schéma on utilise la fonction `register_rest_field` sur le hook `rest_api_ini`.
On peut définir une callback de sanitization et de callback si l'on souhaite. Mais il vaut mieux laisser la validation JSON le faire (à travers le schéma). **Cependant, la validation/sanitization JSON ne fait pas tout à notre place**. Si on veut sanitize un champ texte (retirer les tags par exemple), **le formater JSON ne le fera pas!**. La sanitization du JSON s'applique sur le type de données et des regexs uniquement.

Le schéma va donc faire le gros du boulot : vérifier le type, validation par les enums, la présence du champ (si c'est obligatoire), un format de chaine de caracteres... On ajoutera une sanitization seulement pour sanitize les strings (enlever les balises et caractères spéciaux).

```
add_action('rest_api_init', 'foobar_extend_schema');

function foobar_extend_schema()
{
    //(nom du posttype, nom du champ, options)
    register_rest_field('foobar', 'my_custom_metas', array(
        'get_callback' => function ($post_arr) {
            return array(
                'custom_field_text' => get_field('custom_field_text', $post_arr['id']),
                'champ_custom_vraifaux' => get_field('champ_custom_vraifaux', $post_arr['id']),
            );
        },
        'update_callback' => function ($values, $post, $fieldname) {
            /*
             * (values, post, fieldname)
             * values : tableau cle/valeur des champs
             * post : l'objet post sur lequel on update les metas
             * fieldname :  le nom du champ enregistré avec register_rest_field
             */
            
            //Select only unchanged values
            $values_to_update = array_filter($values, function ($val, $field) use ($post) {
                $current_value = get_field($field, $post->ID, false);
                return $current_value !== $val;
            }, ARRAY_FILTER_USE_BOTH);

            $updates = array();

            foreach ($values_to_update as $field_name => $value) {
                $updates[$field_name] = update_field($field_name, $value, $post->ID);
            }

            $errors = array_filter($updates, fn ($update) => false === $update);

            if (empty($errors))
                return true;

            $fields_not_updated = implode(', ', array_keys($errors));

            return new WP_Error(
                'rest_foobar_incomplete_update',
                __("Failed to update the following fields: {$fields_not_updated}"),
                array('status' => 500)
            );
        },

        'schema' => array(
            'arg_options' => array(
                'sanitize_callback' => function ($value) {
                  //Si on veut faire de la sanitization personalisée
                    $value['custom_field_text'] = sanitize_text_field($value['custom_field_text']);
                    return $value;
                },
            ),
            'description' => __('Champs customs', 'demo'),
            'type' => 'object',
            'properties' => array(
                'custom_field_text' => array(
                    'type' => 'string',
                    'required' => true,
                    'description' => 'Qui êtes vous ?',
                ),
                'champ_custom_vraifaux' => array(
                    'type' => 'string',
                    //Valider parmi un enum de valeurs
                    'enum' => array(
                        'red',
                        'blue'
                    ),
                    'required' => true
                ),
            )
        )
    ));
}
```

Pour les permissions (est ce qu'un user peut update le post dont il n'est pas l'auteur etc) on hérité des permissions natives de WP en fonction des roles. On peut donc adapter les roles si besoin et leurs capacités. On a pas à gérer ça dans l'extension du schéma.

**Comme ici on bénéficie du Controller de WP, le schéma est utilisé à la fois pour les arguments (entrée) et pour la sortie**. Ca fait sens vu que c'est du CRUD (l'entrée correspond à la sortie)

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


### Stratégie pour authentifier tous les endpoints

Si le plugin JWT ne voit pas le header Authorization avec le Token alors il laisse passer la requête. Si l'Authorization est présente avec le Token alors il vérifie et valide le token. Si valide, il authentifie l'user et le charge en mémoire dans l'user connecté de wordpress (et on peut faire notre travail).


Questions :

- Comment couvrir les endpoints par une necessite authentification ? Dois je le faire manuellement en déclarant une permission (avec la `permission_callback` pour implicitement demander une authentification) sur chaque endpoint?

Solutions possibles trouvées pour l'instant :

- ajouter a chaque endpoint une `permission_callback` avec `user_can(wp_get_current_user(), {capability})`. L'authentification est implicitement demandée. **Semble bien et recommandé**

- mettre les customs endpoints dans le namespace du plugin JWT Token `jwt-auth/v1`?? Tous les endpoints de ce namespace sont protégés par le plugin. Si le Token n'est pas présent ou invalide il rejette la requête pour nous ? Pas sûr que ce soit une bonne idée (c'est une suggestion lue  par un utilisateur). **J'ai testé, ça marche pas. C'est comme les autres routes, si pas de Token présent dans la requête, pas de vérification**


### Authentifier tous les endpoints par défaut (natif wp + nos custom endpoint)

La piste est donnée [ici](https://developer.wordpress.org/rest-api/frequently-asked-questions/#require-authentication-for-all-requests).

Il faut utiliser le hook `rest_authentication_errors`. C'est notre chance de glisser des erreurs customs et renvoyer une erreur (et tester l'authentification par ex).

Ca marche bien, le problème c'est que ça **bloque tous les endpoints**, y compris le endpoint pour récuperer le token. 

L'idée serait de mettre par défaut tous les endpoints en authentification JWT, et d'autoriser une whitelist de quelques endpoints (au moins la page d'accueil, endpoint pour récuperer le token, woocommerce token)

Solution : je suis allé voir le code source du plugin [API Bearer Auth](https://www.wordpresspluginfinder.com/api-bearer-auth/). J'avais lu qu'il y avait une whitelist d'implémentée. Je l'ai reprise et m'en suis inspirée. Voir la fonction `is_unauthentificated_endpoint()`.

**On a donc tous les endpoints authentifiés par défaut, et on ajoutera chaque url publique dans une whitelist.** Ce check de la whitelist est fait avant le check d'authentification. 

**Un problème : whitelist d'url dynamiques ?** Différentes solutions possibles : soit mettre une regex explicitement sur chaque route de la whitelist, soit match que a partir du début et toutes les urls dynamiques sont autorisées si la base est autorisés. Par exemple, l'url `/wp/v2/posts` dans la whitelist rend publique les urls `/wp/v2/posts` et `wp/v2/posts/1`. Ca me parait ok, sachant que derrière on a aussi les permissions (qui demandent l'authentification).

### CORS Policy

Comme on développe une API qui n'est pas publique et destinée seulement à être consommée par le front (origin du projet front), on ferait bien de configurer une CORS policy pour n'autoriser que l'origin du front à requêter l'API depuis le navigateur. Cette couche ajoute de la sécurité dans le navigateur, mais le SOP(Same Origin Policy) et le CORS (Cross Origin Ressource Sharing) **sont des spec implémentées par le navigateur uniquement**. Depuis un serveur ou un simple script il n'y en a pas. C'est fait pour sécuriser le web, dans le navigateur, uniquement. On ne peut pas uniquement compter dessus pour protéger les endpoints.

Par défaut **[Wordpress autorise toutes les origines à consommer son API REST](https://developer.wordpress.org/rest-api/frequently-asked-questions/#require-authentication-for-all-requests)**. L'API REST de Wordpress est utilisée par tout son front (ses écrans d'admin) mais la sécurité (éviter les CSRF) est gérée en utilisant une politique de [nonce](https://developer.wordpress.org/plugins/security/nonces/).

Il faut donc venir modifier la CORS Policy de l'API REST pour y mettre une whitelist (et y inclure l'origin du front).

C'est ce qui est fait dans le fichier `functions/cors-policy.php`. On peut la tester facilement avec le projet `playground` pour simuler des origines différentes (en changeant le port)


## Adding REST API Support For Custom Content Types

### Registering A Custom Post Type With REST API Support 

>When registering a custom post type, if you want it to be available via the REST API you should set `'show_in_rest' => true`

>In addition, you can pass an argument for `rest_controller_class`. **This class must be a subclass of `WP_REST_Controller`**. By default, `WP_REST_Posts_Controller` is used as the controller. If you are using a custom controller, then you likely will not be within the wp/v2 namespace.

>If you are using a custom rest_controller_class, then the REST API is unable to automatically determine the route for a given post. In this case, you can use the rest_route_for_post filter to provide this information. This allows for your custom post type to be properly formatted in the Search endpoint and enables automated discovery links.

```
function my_plugin_rest_route_for_post( $route, $post ) {
    if ( $post->post_type === 'book' ) {
        $route = '/wp/v2/books/' . $post->ID;
    }
 
    return $route;
}
add_filter( 'rest_route_for_post', 'my_plugin_rest_route_for_post', 10, 2 );
```

### Registering A Custom Taxonomy With REST API Support

Idem que pour exposer un custom post type. Le controlleur par défaut est `WP_REST_Terms_Controller`



## Controleurs customs

Utiles d'en développer (en implémentant l'interface du controller wp fourni ) si on a besoin d'un CRUD qui n'est pas sur un custom post type (une structure custom ds la DB par exemple)

Les controleurs sur les custom post type sont fournis gratuitement (comme pour les posts), donc on peut en profiter. Il faut juste voir comment faire pour adapter le schéma (avec les champs ACF notamment).


## Gravity Forms

### Authentification 

Le schéma `{domain}/wp-json/gf/v2` est publique. On peut override l'authentification sur les endpoints de GF avec le hook `gform_webapi_authentication_required_`.

**On utilise directement l'authentification de WP par JWT Token** (sinon il faut mettre en place OAuth si on veut faire mieux ce qui implique un 3eme serveur etc, pas le temps sur ce projet d'explorer cela).

Les endpoints de GF sont automatiquement authentifiées (même si on les met dans la whitelist avec authentification demandée on ne peut pas y acceder).

**Après des tests, on dirait que la policy d'authentification de GF s'applique après celle définie ds notre fonction hook sur `rest_authentication_errors`**, si bien que si on déclare comme rejetée la réponse et qu'on retourne une erreur, on recupere quand meme la réponse du plugin de GF.

On va **bien définir les capabilities gravity forms du role des user** pour controler ce qu'ils peuvent faire.

**Les soumissions de formulaires via POST gf/v2/forms/{id}/submissions sont toutes publiques par défaut**.


### Post un form

- recuperer le form via `GET gf/v2/forms/{id du form}`. On recupere toutes les infos et notamment les champs sous la clef `fields`, avec leur id.
- le client remplit le form, et le soumet via  `POST gf/v2/forms/{id du form}/submissions`. La validation/sanitization de GF est faite et renvoie soit les erreurs de validation `code 400`, soit l'entrée crée (et son id) `code 200`.

Information intéressante : **le hook `gform_after_submission` est bien fired après une soumission via l'API**

Problèmes trouvés : 
- le endpoint `POST gf/v2/forms/{id du form}/submissions` **ne sanitize pas (strip tags)**, **ne valide que partiellement (par exemple les valeurs admises d'un select, si type nombre demandé et texte recu laisse vide l'entrée mais ne renvoie pas le message d'erreur etc...)**
- pas de nonce sur le form (possible CSRF attack)
- on peut forcer l'authentification sur tous les POSTS de forms ou sur aucun (par défaut publique). Pas trouvé de moyen simple de faire une whitelist de post. Pas réussi non plus à faire marcher le hook `gform_webapi_authentication_required_` pour forcer l'authentification.