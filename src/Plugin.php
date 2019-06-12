<?php
namespace WRDSB\Lamson;

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/wrdsb
 * @since      1.0.0
 *
 * @package    WRDSB_Lamson
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    WRDSB_Lamson
 * @author     WRDSB <website@wrdsb.ca>
 */
class Plugin
{

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $slug    The string used to uniquely identify this plugin.
     */
    protected $slug;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * The array of actions registered with WordPress.
     *
     * @since    1.0.0
     * @access   protected
     * @var      array    $actions    The actions registered with WordPress to fire when the plugin loads.
     */
    protected $actions;

    /**
     * The array of filters registered with WordPress.
     *
     * @since    1.0.0
     * @access   protected
     * @var      array    $filters    The filters registered with WordPress to fire when the plugin loads.
     */
    protected $filters;

    /**
     * The array of widgets registered with WordPress.
     *
     * @since    1.0.0
     * @access   protected
     * @var      array    $widgets    The widgets registered with WordPress when the plugin loads.
     */
    protected $widgets;

    /**
     * The array of rewrite rules registered with WordPress.
     *
     * @since    1.0.0
     * @access   protected
     * @var      array    $rewrite_rules    The rewrite rules registered with WordPress when the plugin loads.
     */
    protected $rewrite_rules;

    /**
     * The array of query variables registered with WordPress.
     *
     * @since    1.0.0
     * @access   protected
     * @var      array    $query_vars    The query variables registered with WordPress when the plugin loads.
     */
    protected $query_vars;

    /**
     * The array of views (query var + page template) registered with WordPress.
     *
     * @since    1.0.0
     * @access   protected
     * @var      array    $views    The views (query var + page template) registered with WordPress when the plugin loads.
     */
    protected $views;

    /**
     * The array of page templates (name + file path) registered with WordPress.
     *
     * @since    1.0.0
     * @access   protected
     * @var      array    $page_templates    The page templates (name + file path) registered with WordPress when the plugin loads.
     */
    protected $page_templates;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Initialize arrays to store the widgets, rewrite rules, query variables, and views
     * (page templates) which will be registered with WordPress.
     *
     * @since    1.0.0
     */
    public function __construct($slug, $file, $version = '1.0.0')
    {
        $this->slug    = $slug;
        $this->file    = $file;
        $this->version = $version;

        $this->actions = array();
        $this->filters = array();

        $this->widgets = array();
        $this->rewrite_rules = array();
        $this->query_vars = array();
        $this->views = array();
        $this->page_templates = array();

        $this->addQueryVar('view');
    }

    /**
     * Get the plugin's Dependency Injection Container.
     *
     * If a container already exists, return that container. Otherwise, create a
     * new container and return it.
     *
     * @return \Pimple\Container
     */
    public static function getContainer()
    {
        static $container;
        if (! $container) {
            $container = new \Pimple\Container();
        }
        return $container;
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The slug of the plugin.
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * The human-friendly name of the plugin
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function getName()
    {
        return $this->getSlug();
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Add a new action to the collection to be registered with WordPress.
     *
     * @since    1.0.0
     * @param    string               $hook             The name of the WordPress action that is being registered.
     * @param    object               $component        A reference to the instance of the object on which the action is defined.
     * @param    string               $callback         The name of the function definition on the $component.
     * @param    int                  $priority         Optional. The priority at which the function should be fired. Default is 10.
     * @param    int                  $accepted_args    Optional. The number of arguments that should be passed to the $callback. Default is 1.
     */
    public function addAction($hook, $component, $callback, $priority = 10, $accepted_args = 1)
    {
        $this->actions = $this->addHook($this->actions, $hook, $component, $callback, $priority, $accepted_args);
    }

    /**
     * Add a new filter to the collection to be registered with WordPress.
     *
     * @since    1.0.0
     * @param    string               $hook             The name of the WordPress filter that is being registered.
     * @param    object               $component        A reference to the instance of the object on which the filter is defined.
     * @param    string               $callback         The name of the function definition on the $component.
     * @param    int                  $priority         Optional. The priority at which the function should be fired. Default is 10.
     * @param    int                  $accepted_args    Optional. The number of arguments that should be passed to the $callback. Default is 1
     */
    public function addFilter($hook, $component, $callback, $priority = 10, $accepted_args = 1)
    {
        $this->filters = $this->addHook($this->filters, $hook, $component, $callback, $priority, $accepted_args);
    }

    /**
     * A utility function that is used to register the actions and hooks into a single
     * collection.
     *
     * @since    1.0.0
     * @access   private
     * @param    array                $hooks            The collection of hooks that is being registered (that is, actions or filters).
     * @param    string               $hook             The name of the WordPress filter that is being registered.
     * @param    object               $component        A reference to the instance of the object on which the filter is defined.
     * @param    string               $callback         The name of the function definition on the $component.
     * @param    int                  $priority         The priority at which the function should be fired.
     * @param    int                  $accepted_args    The number of arguments that should be passed to the $callback.
     * @return   array                                  The collection of actions and filters registered with WordPress.
     */
    private function addHook($hooks, $hook, $component, $callback, $priority, $accepted_args)
    {
        $hooks[] = array(
            'hook'          => $hook,
            'component'     => $component,
            'callback'      => $callback,
            'priority'      => $priority,
            'accepted_args' => $accepted_args,
        );

        return $hooks;
    }

    /**
     * Register the filters and actions with WordPress.
     *
     * @since    1.0.0
     */
    public function registerHooks()
    {
        // Add an action hook to register all widgets
        $this->addAction('widgets_init', $this, 'registerWidgets');

        // Add an action hook to register all rewrite rules
        $this->addAction('init', $this, 'registerRewriteRules');
        
        // Add a filter hook to register all query vars
        $this->addFilter('query_vars', $this, 'registerQueryVars');

        // Add an action hook to get the current view (page template file)
        $this->addAction('template_include', $this, 'currentView');

        // Cycle through all filter hooks and register them.
        foreach ($this->filters as $hook) {
            add_filter($hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args']);
        }

        // Cycle through all action hooks and register them.
        foreach ($this->actions as $hook) {
            add_action($hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args']);
        }

        register_activation_hook($this->file, array($this, 'activate'));
        register_deactivation_hook($this->file, array($this, 'deactivate'));
    }

    /**
     * Add a new widget to the collection to be registered with WordPress.
     *
     * @since    1.0.0
     * @param    string               $widget           The name of the widget that is being registered.
     * @param    object               $component        A reference to the instance of the object on which the filter is defined.
     * @param    string               $callback         The name of the function definition on the $component.
     * @param    int                  $priority         Optional. The priority at which the function should be fired. Default is 10.
     * @param    int                  $accepted_args    Optional. The number of arguments that should be passed to the $callback. Default is 1
     */
    public function addWidget($widget, $component, $callback, $priority = 10, $accepted_args = 1)
    {
        $this->widgets[] = array(
            'hook'          => $hook,
            'component'     => $component,
            'callback'      => $callback,
            'priority'      => $priority,
            'accepted_args' => $accepted_args,
        );
    }

    /**
     * Register the widgets with WordPress.
     *
     * @since    1.0.0
     */
    public function registerWidgets()
    {
        foreach ($this->widgets as $widget) {
            register_widget();
        }
    }

    /**
     * Add a new rewrite rule to the collection to be registered with WordPress.
     *
     * @param string $key
     * @param string $value
     * @return void
     */
    public function addRewriteRule($key, $value)
    {
        $this->rewrite_rules[$key] = $value;
    }

    /**
     * Register the rewrite rules with WordPress.
     *
     * @since    1.0.0
     */
    public function registerRewriteRules()
    {
        foreach ($this->rewrite_rules as $key => $value) {
            add_rewrite_rule($key, $value, 'top');
        }
    }

    /**
     * Add a new query var name to the collection of query vars to be registered with WordPress.
     *
     * @param string $name
     * @return void
     */
    public function addQueryVar($name)
    {
        $this->query_vars[] = $name;
    }

    /**
     * Register the query vars with WordPress.
     *
     * @param array $vars  WordPress's own array of query vars to which we append our own.
     * @return void
     */
    public function registerQueryVars($vars)
    {
        foreach ($this->query_vars as $query_var) {
            $vars[] = $query_var;
        }
        return $vars;
    }

    /**
     * Add a view to the plugin's collection of views.
     *
     * @param string $view_name
     * @param string $template_name
     * @return void
     */
    public function addView($view_name, $template_name)
    {
        $this->views[$view_name] = $template_name;
    }

    /**
     * Retrieve the current view (page template name)
     * from the plugin's collection of views,
     * based on the value of the 'view' query var.
     *
     * @return void
     */
    public function currentView($template)
    {
        $view_name = get_query_var('view', 'default');

        if ('default' === $view_name) {
            return $template;
        }

        $template_name = $this->views[$view_name];
        $template_file = $this->getPageTemplate($template_name);
        $template_path = $this->getTemplatePath($template_file, $template);

        return $template_path;
    }

    /**
     * Add a page template to the collection of page templates.
     *
     * @param string $template_name
     * @param string $template_path
     * @return void
     */
    public function addPageTemplate($template_name, $template_path)
    {
        $this->page_templates[$template_name] = $template_path;
    }

    /**
     * Retrieve a template file path, given the template's name.
     *
     * @param string $template_name
     * @return void
     */
    public function getPageTemplate($template_name)
    {
        return $this->page_templates[$template_name];
    }

    private function getTemplatePath($template_file, $template)
    {
        $template_path = locate_template(array($template_file));
        if ('' !== $template_path) {
            return $template_path;
        }

        //Check plugin directory next
        $template_path = plugin_dir_path(dirname(__FILE__)) . 'src/Views/templates/' . $template_file;
        if (file_exists($template_path)) {
            return $template_path;
        }

        //Fall back to original template
        return $template;
    }
}
