<?php
/*
Plugin Name: Custom Image Sizes
Plugin URI: http://austinmatzko.com/wordpress-plugins/filosofo-custom-image-sizes/
Description: A plugin that creates custom image sizes for image attachments.
Author: Austin Matzko
Author URI: http://austinmatzko.com
Version: 1.1

Copyright 2011  Austin Matzko ( email : austin at pressedcode dot com )

		This program is free software; you can redistribute it and/or modify
		it under the terms of the GNU General Public License as published by
		the Free Software Foundation; either version 2 of the License, or
		(at your option) any later version.

		This program is distributed in the hope that it will be useful,
		but WITHOUT ANY WARRANTY; without even the implied warranty of
		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
		GNU General Public License for more details.

		You should have received a copy of the GNU General Public License
		along with this program; if not, write to the Free Software
		Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110, USA
*/

if ( ! class_exists( 'Filosofo_Custom_Image_Sizes' ) ) {

    class Filosofo_Custom_Image_Sizes {

        public function __construct()
        {
            add_filter('image_downsize', array(&$this, 'filter_image_downsize'), 99, 3);
        }

        /**
         * Callback for the "image_downsize" filter.
         *
         * @param bool $ignore A value meant to discard unfiltered info returned from this filter.
         * @param int $attachment_id The ID of the attachment for which we want a certain size.
         * @param string $size_name The name of the size desired.
         */
        public function filter_image_downsize($ignore = false, $attachment_id = 0, $size_name = 'thumbnail')
        {
            global $_wp_additional_image_sizes;

            $attachment_id = (int) $attachment_id;
            if (is_array($size_name)) {
                $size_name = implode('x', $size_name);
            }
            $size_name = trim($size_name);

            $meta = wp_get_attachment_metadata($attachment_id);

            /* the requested size does not yet exist for this attachment */
            if (
                empty( $meta['sizes'] ) ||
                empty( $meta['sizes'][$size_name] )
            ) {
                // let's first see if this is a registered size
                if ( isset( $_wp_additional_image_sizes[$size_name] ) ) {
                    $height = (int) $_wp_additional_image_sizes[$size_name]['height'];
                    $width = (int) $_wp_additional_image_sizes[$size_name]['width'];
                    $crop = (bool) $_wp_additional_image_sizes[$size_name]['crop'];

                    // if not, see if name is of form [width]x[height] and use that to crop
                } else if ( preg_match('#^(\d+)x(\d+)[x]?(\d+)?$#', $size_name, $matches) ) {
                    $height = (int) $matches[2];
                    $width = (int) $matches[1];
                    $crop = (isset($matches[3]) && $matches[3] == '1') ? true : false;
                }

                if ( ! empty( $height ) && ! empty( $width ) ) {
                    $new_image = $this->_generate_attachment($attachment_id, $width, $height, $crop);

                    if( ! is_wp_error( $new_image ) ){
                        $resized_path = $new_image['path'];
                        $width = $new_image['width'];
                        $height = $new_image['height'];
                        $file_name = $new_image['file'];

                        do_action( 'custom_image_size_resized_path', $resized_path, $attachment_id, $width, $height, $crop );

                        $meta['sizes'][$size_name] = array(
                            'file' => $file_name,
                            'width' => $width,
                            'height' => $height,
                        );

                        wp_update_attachment_metadata($attachment_id, $meta);


                        $fullsize_url = wp_get_attachment_url($attachment_id);
                        $new_url = str_replace(basename($fullsize_url), $file_name, $fullsize_url);

                        return array(
                            $new_url,
                            $width,
                            $height,
                            true
                        );

                    }

                }
            }

            return false;
        }

        /**
         * Creates a cropped version of an image for a given attachment ID.
         *
         * @param int $attachment_id The attachment for which to generate a cropped image.
         * @param int $width The width of the cropped image in pixels.
         * @param int $height The height of the cropped image in pixels.
         * @param bool $crop Whether to crop the generated image.
         * @return string The full path to the cropped image.  Empty if failed.
         */
        private function _generate_attachment($attachment_id = 0, $width = 0, $height = 0, $crop = true)
        {
            $attachment_id = (int) $attachment_id;
            $width = (int) $width;
            $height = (int) $height;
            $crop = (bool) $crop;

            $original_path = get_attached_file($attachment_id);


            $image_editor = wp_get_image_editor( $original_path );

            if ( ! is_wp_error( $image_editor ) ) {

                $image_editor->resize( $width, $height, $crop);

                return $image_editor->save();

            }

            return $image_editor;
        }
    }

    function initialize_custom_image_sizes()
    {
        $filosofo_custom_image_sizes = new Filosofo_Custom_Image_Sizes();
    }

    add_action('plugins_loaded', 'initialize_custom_image_sizes');
}