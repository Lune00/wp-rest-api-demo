<?php

/**
 * Rédefinit la durée de validité du token (timestamp), avant qu'il n'expire
 * A augmenter en dev
 */
add_filter('jwt_auth_expire', function(){
    return time() + 500;
});