<?php
/**
 * Main Class file for `WP_OSA`
 *
 * Main class that deals with all other classes.
 *
 * @since   1.0.0
 * @package WPOSA
 */

// Exit if accessed directly.
if (! defined('ABSPATH')) {
    exit;
}

/**
 * WP_OSA.
 *
 * WP Settings API Class.
 *
 * @since 1.0.0
 */

if (! class_exists('WP_OSA')) :

    class WP_OSA
    {
        /**
         * Sections array.
         *
         * @var   array
         * @since 1.0.0
         */
        private $sections_array = array();

        /**
         * Fields array.
         *
         * @var   array
         * @since 1.0.0
         */
        private $fields_array = array();


        private $metabox = null;
        private $options = null;
        protected $settings_name = null;


        /**
         * Constructor.
         *
         * @since  1.0.0
         */
        public function __construct($options = null, $metabox = null)
        {
            $this->options = $options;
            if($metabox) {
                // Use for post metabox
                $this->metabox = $metabox;
                add_action('admin_init', array($this, 'add_metabox'));
                add_action('save_post', array($this, 'save_metabox'), 10, 2);
            } else {
                $this->init_consts();
                if(is_admin()) {
                    // Enqueue the admin scripts.
                    add_action('admin_enqueue_scripts', array( $this, 'admin_scripts' ));

                    // Hook it up.
                    add_action('admin_init', array( $this, 'admin_init' ));

                    // Menu.
                    add_action('admin_menu', array( $this, 'admin_menu' ));
                    $this->init_options();
                }

                // To allow multiple instanciations off this class
                if ($this->settings_name) {
                    do_action('settings_ready_'. $this->settings_name);
                } else {
                    do_action('settings_ready');
                }
            }
        }


        /**
         * Initializes the sections and fields.
         */
        public function init_options()
        {
            foreach($this->options as $section) {
                $name = $title = $fields = null;
                extract($section);
                $this->add_section(
                    [
                        'id'    => $name,
                        'title' => $title,
                    ]
                );
                if ($fields) {
                    foreach ($fields as $field) {
                        if (isset($field['show_if'])  && is_callable($field['show_if'])) {
                            $show = $field['show_if']();
                            if(!$show) {
                                continue;
                            }
                        }

                        $this->add_field($name, $field);
                    }
                }
            }
        }

        /**
         * Defines the constants from the saved options or from the fields default values if options are not saved.
         */
        public function init_consts()
        {
            foreach($this->options as $section) {
                $options = get_option($section['name']);
                if($options) {
                    foreach ($options as $key => $value) {
                        $option_name = $section['name']  . '_' . $key;
                        if (!defined($option_name)) {
                            define($option_name, $value);
                        }
                    }
                }
                // init from default if constant is not defined
                foreach($section['fields'] as $field) {
                    $option_name = $section['name']  . '_' . $field['id'];
                    if (!defined($option_name) && array_key_exists('default', $field)) {
                        define($option_name, $field['default']);
                    }
                }
            }
        }

        /**
         * Admin Scripts.
         *
         * @since 1.0.0
         */
        public function admin_scripts()
        {
            // jQuery is needed.
            wp_enqueue_script('jquery');

            // Color Picker.
            wp_enqueue_script(
                'iris',
                admin_url('js/iris.min.js'),
                array( 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' ),
                false,
                1
            );

            // Media Uploader.
            wp_enqueue_media();
        }


        /**
         * Set Sections.
         *
         * @param array $sections
         * @since 1.0.0
         */
        public function set_sections($sections)
        {
            // Bail if not array.
            if (! is_array($sections)) {
                return false;
            }

            // Assign to the sections array.
            $this->sections_array = $sections;

            return $this;
        }


        /**
         * Add a single section.
         *
         * @param array $section
         * @since 1.0.0
         */
        public function add_section($section)
        {
            // Bail if not array.
            if (! is_array($section)) {
                return false;
            }

            // Assign the section to sections array.
            $this->sections_array[] = $section;

            return $this;
        }


        /**
         * Set Fields.
         *
         * @since 1.0.0
         */
        public function set_fields($fields)
        {
            // Bail if not array.
            if (! is_array($fields)) {
                return false;
            }

            // Assign the fields.
            $this->fields_array = $fields;

            return $this;
        }


        public const default_field = [
            'id' => '',
            'name' => 'No name',
            'desc' => '',
            'type'    => 'text',
            'label_for' => null,
            'default' => null,
            'std' => null,
            'size' => null,
            'options' => null,
            'query' => null,
            'callback' => null,
            'placeholder' => null,
            'sanitize_callback' => null,
            'sanitization_error_message' => null
        ];

        /**
         * Add a single field.
         *
         * @since 1.0.0
         */
        public function add_field($section, $field_array)
        {
            // Combine the defaults with user's arguements.
            $field_array['section'] =  $section;
            if(isset($field_array['default'])) {
                $field_array['std'] =  $field_array['default']  ;
            }
            if(isset($field_array['title'])) {
                $field_array['name'] =  $field_array['title']  ;
            }
            if(isset($field_array['description'])) {
                $field_array['desc'] =  $field_array['description']  ;
            }
            $field_array['label_for'] = "{$section}[{$field_array['id']}]";

            $arg = wp_parse_args($field_array, self::default_field);

            // Each field is an array named against its section.
            $this->fields_array[ $section ][] = $arg;

            return $this;
        }



        /**
         * Initialize API.
         *
         * Initializes and registers the settings sections and fields.
         * Usually this should be called at `admin_init` hook.
         *
         * @since  1.0.0
         */
        public function admin_init()
        {
            /**
             * Register the sections.
             *
             * Sections array is like this:
             *
             * $sections_array = array (
             *   $section_array,
             *   $section_array,
             *   $section_array,
             * );
             *
             * Section array is like this:
             *
             * $section_array = array (
             *   'id'    => 'section_id',
             *   'title' => 'Section Title'
             * );
             *
             * @since 1.0.0
             */
            foreach ($this->sections_array as $section) {
                if (false == get_option($section['id'])) {
                    // Add a new field as section ID.
                    add_option($section['id']);
                }

                // Deals with sections description.
                if (isset($section['desc']) && ! empty($section['desc'])) {
                    // Build HTML.
                    $section['desc'] = '<div class="inside">' . $section['desc'] . '</div>';

                    // Create the callback for description.
                    $callback = function () use ($section) {
                        echo str_replace('"', '\"', $section['desc']);
                    };
                } elseif (isset($section['callback'])) {
                    $callback = $section['callback'];
                } else {
                    $callback = null;
                }

                /**
                 * Add a new section to a settings page.
                 *
                 * @param string $id
                 * @param string $title
                 * @param callable $callback
                 * @param string $page | Page is same as section ID.
                 * @since 1.0.0
                 */
                add_settings_section($section['id'], $section['title'], $callback, $section['id']);
            } // foreach ended.

            /**
             * Register settings fields.
             *
             * Fields array is like this:
             *
             * $fields_array = array (
             *   $section => $field_array,
             *   $section => $field_array,
             *   $section => $field_array,
             * );
             *
             *
             * Field array is like this:
             *
             * $field_array = array (
             *   'id'   => 'id',
             *   'name' => 'Name',
             *   'type' => 'text',
             * );
             *
             * @since 1.0.0
             */
            foreach ($this->fields_array as $section => $field_array) {
                foreach ($field_array as $field) {
                    /**
                     * Add a new field to a section of a settings page.
                     *
                     * @param string   $id
                     * @param string   $title
                     * @param callable $callback
                     * @param string   $page
                     * @param string   $section = 'default'
                     * @param array    $args = array()
                     * @since 1.0.0
                     */

                    // @param string 	$id
                    $field_id = $section . '[' . $field['id'] . ']';
                    $type =  $field['type'];
                    $name =  $field['name'];
                    add_settings_field(
                        $field_id,
                        $name,
                        array( $this, 'callback_' . $type ),
                        $section,
                        $section,
                        $field
                    );
                } // foreach ended.
            } // foreach ended.

            // Creates our settings in the fields table.
            foreach ($this->sections_array as $section) {
                $section_id = $section['id'];
                /**
                 * Registers a setting and its sanitization callback.
                 *
                 * @param string $field_group   | A settings group name.
                 * @param string $field_name    | The name of an option to sanitize and save.
                 * @param callable  $sanitize_callback = ''
                 * @since 1.0.0
                 */
                register_setting($section_id, $section_id, function ($fields) use ($section_id) {
                    return $this->sanitize_fields($fields, $section_id);
                });
            } // foreach ended.
        } // admin_init() ended.

        public function default_sanitization_error_message($field_config)
        {
            return sprintf(__('Please insert a valid %s'), $field_config['type']);
        }


        protected function get_sanitizer($field_config)
        {
            return isset($field_config['sanitize_callback']) && is_callable($field_config['sanitize_callback']) ?
                $field_config['sanitize_callback'] :
                function ($field_value) use ($field_config) {
                    return $this->sanitize_field($field_value, $field_config);
                };
        }
        protected function get_error_message($field_config)
        {
            return isset($field_config['sanitization_error_message']) ?
                                $field_config['sanitization_error_message'] :
                                $this->default_sanitization_error_message($field_config);
        }

        /**
         * Sanitize callback for Settings API fields.
         *
         * @param      array  $fields      The fields
         * @param      string  $section_id  The section identifier
         *
         * @return     array  The sanitized fields
         */
        public function sanitize_fields($fields, $section_id)
        {
            $old_values = get_option($section_id, []);
            foreach ($fields as $field_slug => $field_value) {
                if (!empty($field_value) && $field_config = $this->get_field_config($section_id, $field_slug)) {
                    // Use sanitizer from field config, if not provided, use internal sanitization
                    $sanitize_callback = $this->get_sanitizer($field_config);

                    $sanitized = call_user_func($sanitize_callback, $field_value);
                    if (empty($sanitized)) {
                        add_settings_error(
                            $section_id,
                            $section_id.'['.$field_slug.']', // so we can easily access the field ( see script method sanitization errors )
                            $this->get_error_message($field_config),
                            'error'
                        );
                        if(isset($old_values[$field_slug])) {
                            // Get the old value
                            $sanitized = $old_values[$field_slug];
                        }
                    }
                    $fields[ $field_slug ] = $sanitized ;
                }
            }
            return $fields;
        }

        /**
         * General Sanitize callback for a field, uses the field config to get the type of field
         *
         * @param      string $field_value   The field value
         * @param      array  $field_config  The field configuration
         *
         * @return     mixed   The sanitized field
         */
        public function sanitize_field($field_value, $field_config)
        {
            $type = $field_config['type'];
            switch ($type) {
                case 'checkbox':
                    return $field_value == 'on' ? 'on' : 'off' ;
                case 'range':
                case 'number':
                    return (is_numeric($field_value)) ? $field_value : 0;
                case 'textarea':
                    return wp_kses_post($field_value);
                case 'email':
                    return sanitize_email($field_value);
                case 'url':
                    return sanitize_url($field_value);
                default:
                    return !empty($field_value) ? sanitize_text_field($field_value) : '';
            }
        }

        /**
         * Gets the field configuration.
         *
         * @param      string  $section_id  The section identifier
         * @param      string  $field_slug  The field slug
         *
         * @return     array  The field configuration or null.
         */
        public function get_field_config($section_id, $field_slug)
        {
            foreach ($this->fields_array[$section_id] as $field) {
                if ($field['id'] == $field_slug) {
                    return $field;
                }
            }
            return null;
        }


        /**
         * Get field description for display
         *
         * @param array $args settings field args
         */
        public function get_field_description($args)
        {
            if (! empty($args['desc'])) {
                $desc = sprintf('<p class="description">%s</p>', $args['desc']);
            } else {
                $desc = '';
            }

            return $desc;
        }


        /**
         * Displays a title field for a settings field
         *
         * @param array $args settings field args
         */
        public function callback_title($args)
        {
            $value = esc_attr($this->get_option($args['id'], $args['section'], $args['std']));
            if ('' !== $args['name']) {
                $name = $args['name'];
            } else {
            };
            $type = isset($args['type']) ? $args['type'] : 'title';

            $html = '';
            echo $html;
        }


        /**
         * Displays a text field for a settings field
         *
         * @param array $args settings field args
         */
        public function callback_text($args)
        {
            $value = esc_attr($this->get_option($args['id'], $args['section'], $args['std'], $args['placeholder']));
            $size  = isset($args['size']) && ! is_null($args['size']) ? $args['size'] : 'regular';
            $type  = isset($args['type']) ? $args['type'] : 'text';
            $attributes  = isset($args['attributes']) && is_array($args['attributes']) ? wp_sanitize_script_attributes($args['attributes']) : '';
            $after  = isset($args['after']) ? $args['after'] : '';

            $html  = sprintf('<input type="%1$s" class="%2$s-text" id="%3$s[%4$s]" name="%3$s[%4$s]" value="%5$s" placeholder="%6$s" %7$s /> %8$s', $type, $size, $args['section'], $args['id'], $value, $args['placeholder'], $attributes, $after);
            $html .= $this->get_field_description($args);

            echo $html;
        }


        /**
         * Displays a url field for a settings field
         *
         * @param array $args settings field args
         */
        public function callback_url($args)
        {
            $this->callback_text($args);
        }

        /**
         * Displays a date field for a settings field
         *
         * @param array $args settings field args
         */
        public function callback_date($args)
        {
            $this->callback_text($args);
        }

        /**
         * Displays an email field for a settings field
         *
         * @param array $args settings field args
         */
        public function callback_email($args)
        {
            $this->callback_text($args);
        }

        /**
         * Displays a number field for a settings field
         *
         * @param array $args settings field args
         */
        public function callback_number($args)
        {
            $this->callback_text($args);
        }

        /**
         * Displays a range field for a settings field
         *
         * @param array $args settings field args
         */
        public function callback_range($args)
        {
            $value = esc_html($this->get_option($args['id'], $args['section'], $args['std']));
            $args['after'] = "<output>$value</output>";
            if (!isset($args['attributes']) ||  !is_array($args['attributes'])) {
                $args['attributes'] = [];
            }
            $args['attributes']['oninput']="this.nextElementSibling.value = this.value";
            $this->callback_text($args);
        }

        /**
         * Displays a checkbox for a settings field
         *
         * @param array $args settings field args
         */
        public function callback_checkbox($args)
        {
            $value = esc_attr($this->get_option($args['id'], $args['section'], $args['std']));

            $html  = '<fieldset>';
            $html .= sprintf('<label for="wposa-%1$s[%2$s]">', $args['section'], $args['id']);
            $html .= sprintf('<input type="hidden" name="%1$s[%2$s]" value="off" />', $args['section'], $args['id']);
            $html .= sprintf('<input type="checkbox" class="checkbox" id="wposa-%1$s[%2$s]" name="%1$s[%2$s]" value="on" %3$s />', $args['section'], $args['id'], checked($value, 'on', false));
            $html .= sprintf('%1$s</label>', $args['desc']);
            $html .= '</fieldset>';

            echo $html;
        }

        /**
         * Displays a multicheckbox a settings field
         *
         * @param array $args settings field args
         */
        public function callback_multicheck($args)
        {
            $value = $this->get_option($args['id'], $args['section'], $args['std']);

            $html = '<fieldset>';
            foreach ($args['options'] as $key => $label) {
                $checked = isset($value[ $key ]) ? $value[ $key ] : '0';
                $html   .= sprintf('<label for="wposa-%1$s[%2$s][%3$s]">', $args['section'], $args['id'], $key);
                $html   .= sprintf('<input type="checkbox" class="checkbox" id="wposa-%1$s[%2$s][%3$s]" name="%1$s[%2$s][%3$s]" value="%3$s" %4$s />', $args['section'], $args['id'], $key, checked($checked, $key, false));
                $html   .= sprintf('%1$s</label><br>', $label);
            }
            $html .= $this->get_field_description($args);
            $html .= '</fieldset>';

            echo $html;
        }

        /**
         * Displays a multicheckbox a settings field
         *
         * @param array $args settings field args
         */
        public function callback_radio($args)
        {
            $value = $this->get_option($args['id'], $args['section'], $args['std']);

            $html = '<fieldset>';
            foreach ($args['options'] as $key => $label) {
                $html .= sprintf('<label for="wposa-%1$s[%2$s][%3$s]">', $args['section'], $args['id'], $key);
                $html .= sprintf('<input type="radio" class="radio" id="wposa-%1$s[%2$s][%3$s]" name="%1$s[%2$s]" value="%3$s" %4$s />', $args['section'], $args['id'], $key, checked($value, $key, false));
                $html .= sprintf('%1$s</label><br>', $label);
            }
            $html .= $this->get_field_description($args);
            $html .= '</fieldset>';

            echo $html;
        }

        /**
         * Displays a selectbox for a settings field
         *
         * @param array $args settings field args
         */
        public function callback_select($args)
        {
            $value = esc_attr($this->get_option($args['id'], $args['section'], $args['std']));
            $size  = isset($args['size']) && ! is_null($args['size']) ? $args['size'] : 'regular';

            $html = sprintf('<select class="%1$s" name="%2$s[%3$s]" id="%2$s[%3$s]">', $size, $args['section'], $args['id']);
            $options = isset($args['options']) ? $args['options'] : [];
            if (isset($args['query']) && $args['query']['type']=='callback') {
                if (is_callable($args['query']['function'])) {
                    $query_args = isset($args['query']['args']) ? $args['query']['args'] : [];
                    $options = call_user_func($args['query']['function'], $query_args);
                }
            }
            foreach ($options as $key => $label) {
                $html .= sprintf('<option value="%s"%s>%s</option>', $key, selected($value, $key, false), $label);
            }
            $html .= sprintf('</select>');
            $html .= $this->get_field_description($args);

            echo $html;
        }

        /**
         * Displays a textarea for a settings field
         *
         * @param array $args settings field args
         */
        public function callback_textarea($args)
        {
            $value = esc_textarea($this->get_option($args['id'], $args['section'], $args['std']));
            $size  = isset($args['size']) && ! is_null($args['size']) ? $args['size'] : 'regular';

            $html  = sprintf('<textarea rows="5" cols="55" class="%1$s-text" id="%2$s[%3$s]" name="%2$s[%3$s]">%4$s</textarea>', $size, $args['section'], $args['id'], $value);
            $html .= $this->get_field_description($args);

            echo $html;
        }

        /**
         * Displays a textarea for a settings field
         *
         * @param array $args settings field args.
         * @return string
         */
        public function callback_html($args)
        {
            echo $this->get_field_description($args);
        }


        /**
         * Displays a content ( generate via callback )
         *
         * @param array $args settings field args.
         * @return string
         */
        public function callback_content($args)
        {
            echo $this->get_field_description($args);
            if (isset($args['callback'])) {
                $callback = $args['callback'];
                if(isset($callback['function']) && is_callable($callback['function'])) {
                    $args = (isset($callback['args'])) ? $callback['args'] : '';
                    echo  call_user_func($callback['function'], $args);
                } else {
                    echo 'Error wrong callback '.print_r($callback);
                }
            }
        }


        /**
         * Displays a rich text textarea for a settings field
         *
         * @param array $args settings field args.
         */
        public function callback_wysiwyg($args)
        {
            $value = $this->get_option($args['id'], $args['section'], $args['std']);
            $size  = isset($args['size']) && ! is_null($args['size']) ? $args['size'] : '500px';

            echo '<div style="max-width: ' . $size . ';">';

            $editor_settings = array(
                'teeny'         => true,
                'textarea_name' => $args['section'] . '[' . $args['id'] . ']',
                'textarea_rows' => 10,
            );
            if (isset($args['options']) && is_array($args['options'])) {
                $editor_settings = array_merge($editor_settings, $args['options']);
            }

            wp_editor($value, $args['section'] . '-' . $args['id'], $editor_settings);

            echo '</div>';

            echo $this->get_field_description($args);
        }

        /**
         * Displays a file upload field for a settings field
         *
         * @param array $args settings field args.
         */
        public function callback_file($args)
        {
            $value = esc_attr($this->get_option($args['id'], $args['section'], $args['std']));
            $size  = isset($args['size']) && ! is_null($args['size']) ? $args['size'] : 'regular';
            $id    = $args['section'] . '[' . $args['id'] . ']';
            $label = isset($args['options']['button_label']) ?
            $args['options']['button_label'] :
            __('Choose File');

            $html  = sprintf('<input type="text" class="%1$s-text wpsa-url" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s"/>', $size, $args['section'], $args['id'], $value);
            $html .= '<input type="button" class="button wpsa-browse" value="' . $label . '" />';
            $html .= $this->get_field_description($args);

            echo $html;
        }

        /**
         * Displays an image upload field with a preview
         *
         * @param array $args settings field args.
         */
        public function callback_image($args)
        {
            $value = esc_attr($this->get_option($args['id'], $args['section'], $args['std']));
            $size  = isset($args['size']) && ! is_null($args['size']) ? $args['size'] : 'regular';
            $id    = $args['section'] . '[' . $args['id'] . ']';
            $label = isset($args['options']['button_label']) ?
            $args['options']['button_label'] :
            __('Choose Image');

            $html  = sprintf('<input type="text" class="%1$s-text wpsa-url" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s"/>', $size, $args['section'], $args['id'], $value);
            $html .= '<input type="button" class="button wpsa-browse" value="' . $label . '" />';
            $html .= $this->get_field_description($args);
            $html .= '<p class="wpsa-image-preview"><img src=""/></p>';

            echo $html;
        }

        /**
         * Displays a password field for a settings field
         *
         * @param array $args settings field args
         */
        public function callback_password($args)
        {
            $value = esc_attr($this->get_option($args['id'], $args['section'], $args['std']));
            $size  = isset($args['size']) && ! is_null($args['size']) ? $args['size'] : 'regular';

            $html  = sprintf('<input type="password" class="%1$s-text" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s"/>', $size, $args['section'], $args['id'], $value);
            $html .= $this->get_field_description($args);

            echo $html;
        }

        /**
         * Displays a color picker field for a settings field
         *
         * @param array $args settings field args
         */
        public function callback_color($args)
        {
            $value = esc_attr($this->get_option($args['id'], $args['section'], $args['std'], $args['placeholder']));
            $size  = isset($args['size']) && ! is_null($args['size']) ? $args['size'] : 'regular';

            $html  = sprintf('<input type="text" class="%1$s-text color-picker" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s" data-default-color="%5$s" placeholder="%6$s" />', $size, $args['section'], $args['id'], $value, $args['std'], $args['placeholder']);
            $html .= $this->get_field_description($args);

            echo $html;
        }


        /**
         * Displays a separator field for a settings field
         *
         * @param array $args settings field args
         */
        public function callback_separator($args)
        {
            $type = isset($args['type']) ? $args['type'] : 'separator';

            $html  = '';
            $html .= '<div class="wpsa-settings-separator"></div>';
            echo $html;
        }


        /**
         * Get the value of a settings field (or meta)
         *
         * @param string $option  settings field name.
         * @param string $section the section name this field belongs to.
         * @param string $default default text if it's not found.
         * @return string
         */
        public function get_option($option, $section, $default = '')
        {
            if(isset($this->metabox)) {
                global $post;
                static $metas;
                if($metas == null) {
                    $metas = get_post_meta($post->ID);
                }
                if(isset($metas[$section.'_'.$option])) {
                    return $metas[$section.'_'.$option][0];
                }
            } else {
                $options = get_option($section);
                if (isset($options[ $option ])) {
                    return $options[ $option ];
                }
            }

            return $default;
        }

        /**
         * Add submenu page to the Settings main menu.
         *
         * @param string $page_title
         * @param string $menu_title
         * @param string $capability
         * @param string $menu_slug
         * @param callable $function = ''
         * @author Ahmad Awais
         * @since  [version]
         */

        // public function admin_menu( $page_title = 'Page Title', $menu_title = 'Menu Title', $capability = 'manage_options', $menu_slug = 'settings_page', $callable = 'plugin_page' ) {
        public function admin_menu()
        {
            // add_options_page( $page_title, $menu_title, $capability, $menu_slug, array( $this, $callable ) );
            add_options_page(
                'WP OOP Settings API',
                'WP OOP Settings API',
                'manage_options',
                'wp_osa_settings',
                array( $this, 'plugin_page' )
            );
        }

        public function plugin_page()
        {
            echo '<div class="wrap">';
            echo '<h1>WP OOP Settings API <span style="font-size:50%;">v' . WPOSA_VERSION . '</span></h1>';
            $this->show_navigation();
            $this->show_forms();
            echo '</div>';
        }

        /**
         * Show navigations as tab
         *
         * Shows all the settings section labels as tab
         */
        public function show_navigation()
        {
            $html = '<h2 class="nav-tab-wrapper">';

            foreach ($this->sections_array as $tab) {
                $html .= sprintf('<a href="#%1$s" class="nav-tab" id="%1$s-tab">%2$s</a>', $tab['id'], $tab['title']);
            }

            $html .= '</h2>';

            echo $html;
        }

        /**
         * Show the section settings forms
         *
         * This function displays every sections in a different form
         */
        public function show_forms()
        {
            ?>
			<div class="metabox-holder">
				<?php foreach ($this->sections_array as $form) { ?>
					<!-- style="display: none;" -->
					<div id="<?php echo $form['id']; ?>" class="group" >
						<form method="post" action="options.php">
							<?php
                            do_action('wsa_form_top_' . $form['id'], $form);
				    settings_errors($form['id']);
				    settings_fields($form['id']);
				    do_settings_sections($form['id']);
				    do_action('wsa_form_bottom_' . $form['id'], $form);
				    ?>
							<div style="padding-left: 10px">
								<?php submit_button(null, 'primary', 'submit_'.$form['id']); ?>
							</div>
						</form>
						<?php do_action('wsa_after_form_' . $form['id'], $form); ?>
					</div>
				<?php } ?>
			</div>
			<?php
            $this->script();
        }

        /**
         * Adds a metabox.
         */
        public function add_metabox()
        {
            $this->init_options();
            add_meta_box(
                $this->metabox['id'],
                $this->metabox['title'],
                array( $this, 'display_metas' ),
                $this->metabox['post_types'],
                $this->metabox['context'],
                $this->metabox['priority']
            );
        }

        /**
         * Display the metabox
         */
        public function display_metas()
        {
            $this->show_navigation();
            $this->show_forms_metabox();
        }



        /**
         * Show the section settings forms
         *
         * This function displays every sections in a different form
         */
        public function show_forms_metabox()
        {
            ?>
			<div class="metabox-holder">
				<?php foreach ($this->sections_array as $form) {
				    $section  = $form['id'];
				    echo "<div id='$section' class='group' ><h3>{$form['title']}</h3>\n";
				    echo '<table class="form-table" role="presentation">';
				    $fields = $this->fields_array[$section];
				    foreach ($fields as $field) {
				        $label_for = $field['label_for'];
				        $name = $field['name'];
				        $type = $field['type'];
				        $callback = 'callback_'.$type;
				        echo "<tr>";
				        if ($label_for) {
				            echo '<th scope="row"><label for="' . esc_attr($label_for) . '">' . $name  . '</label></th>';
				        } else {
				            echo '<th scope="row">' . $name  . '</th>';
				        }
				        echo '<td>';
				        $this->$callback($field);
				        echo '</td>';
				        echo '</tr>';
				    }
				    echo '</table></div>';
				} ?>
			</div>
			<?php
            $this->script();
        }

        /**
         * Saves the metabox hook
         *
         * @param      int  $post_id  The post ID
         * @param      mixed $post  The post
         */
        public function save_metabox($post_id, $post)
        {
            $posted_data = $_POST;
            if (isset($posted_data['post_type']) && in_array($posted_data['post_type'], $this->metabox['post_types'])) {
                foreach ($this->sections_array as $section) {
                    $section_id = $section['id'];
                    $section_data = isset($posted_data[ $section_id  ]) ? $posted_data[$section_id ] : null ;
                    if ($section_data) {
                        foreach($section_data as $field_name=>$field_value) {
                            $field_config = $this->get_field_config($section_id, $field_name);
                            $sanitizer = $this->get_sanitizer($field_config);
                            if($sanitizer) {
                                $field_value = call_user_func($sanitizer, $field_value);
                            }
                            update_post_meta($post_id, $section_id.'_'.$field_name, $field_value);
                        }
                    }
                }
            }
        }

        /**
         * Tabbable JavaScript codes & Initiate Color Picker
         *
         * This code uses localstorage for displaying active tabs
         */
        public function script()
        {
            ?>
			<script>
				jQuery( document ).ready( function( $ ) {

				//Initiate Color Picker.
				$('.color-picker').iris();

				// Switches option sections
				$( '.group' ).hide();
				var activetab = '';
				if ( 'undefined' != typeof localStorage ) {
					activetab = localStorage.getItem( 'activetab' );
				}
				if ( '' != activetab && $( activetab ).length ) {
					$( activetab ).fadeIn();
				} else {
					$( '.group:first' ).fadeIn();
				}
				$( '.group .collapsed' ).each( function() {
					$( this )
						.find( 'input:checked' )
						.parent()
						.parent()
						.parent()
						.nextAll()
						.each( function() {
							if ( $( this ).hasClass( 'last' ) ) {
								$( this ).removeClass( 'hidden' );
								return false;
							}
							$( this )
								.filter( '.hidden' )
								.removeClass( 'hidden' );
						});
				});

				if ( '' != activetab && $( activetab + '-tab' ).length ) {
					$( activetab + '-tab' ).addClass( 'nav-tab-active' );
				} else {
					$( '.nav-tab-wrapper a:first' ).addClass( 'nav-tab-active' );
				}
				$( '.nav-tab-wrapper a' ).click( function( evt ) {
					$( '.nav-tab-wrapper a' ).removeClass( 'nav-tab-active' );
					$( this )
						.addClass( 'nav-tab-active' )
						.blur();
					var clicked_group = $( this ).attr( 'href' );
					if ( 'undefined' != typeof localStorage ) {
						localStorage.setItem( 'activetab', $( this ).attr( 'href' ) );
					}
					$( '.group' ).hide();
					$( clicked_group ).fadeIn();
					evt.preventDefault();
				});

				$( '.wpsa-browse' ).on( 'click', function( event ) {
					event.preventDefault();

					var self = $( this );

					// Create the media frame.
					var file_frame = ( wp.media.frames.file_frame = wp.media({
						title: self.data( 'uploader_title' ),
						button: {
							text: self.data( 'uploader_button_text' )
						},
						multiple: false
					}) );

					file_frame.on( 'select', function() {
						attachment = file_frame
							.state()
							.get( 'selection' )
							.first()
							.toJSON();

						self
							.prev( '.wpsa-url' )
							.val( attachment.url )
							.change();
					});

					// Finally, open the modal
					file_frame.open();
				});

				$( 'input.wpsa-url' )
					.on( 'change keyup paste input', function() {
						var self = $( this );
						self
							.next()
							.parent()
							.children( '.wpsa-image-preview' )
							.children( 'img' )
							.attr( 'src', self.val() );
					})
					.change();
					
				// Sanitization errors
				$('div.settings-error').each(function(){
					// Get the field mame and make sure to escape brackets
					var field_id = $(this).attr('id')
						.replace('setting-error-', '')
						.replace('[', "\\[") 
						.replace(']', "\\]")
					var $field = $("[name="+field_id+"]");
					$field.addClass('sanitization-error');
					$field.one('change',function(){
						$field.removeClass('sanitization-error');
					});
				});
			});

			</script>

			<style type="text/css">
				/** WordPress 3.8 Fix **/
				.form-table th {
					padding: 20px 10px;
				}

				#wpbody-content .metabox-holder {
					padding-top: 5px;
				}

				.wpsa-image-preview img {
					height: auto;
					max-width: 70px;
				}

				.wpsa-settings-separator {
					background: #ccc;
					border: 0;
					color: #ccc;
					height: 1px;
					position: absolute;
					left: 0;
					width: 99%;
				}
				.group .form-table input.color-picker {
					max-width: 100px;
				}

				/* Pretty much like :focus with red ( like notice-error )*/
				.form-table input.sanitization-error, .form-table select.sanitization-error {
					border-color: #d63638;
					box-shadow: 0 0 0 1px #d63638;
					outline: 2px solid transparent;
				}

                tr output{ 
                   font-weight: bold;
                   vertical-align: top;
                   margin-left: 1em; 
                }
			</style>
			<?php
        }
    } // WP_OSA ended.

endif;
