<?php
/*
Plugin Name: GA Authors
Plugin Script: ga-authors.php
Plugin URI: http://marto.lazarov.org/plugins/ga-authors
Description: Track page views by authors
Version: 1.0.2
Author: mlazarov
Author URI: http://marto.lazarov.org
Min WP Version: 2.7
Max WP Version: 3.2.1
Update Server: http://marto.lazarov.org/plugins/ga-authors

== Changelog ==
= 1.0.2 =
* Updated install howto
* Bugfixes

= 1.0.1 =
* Bugfix

= 1.0.0 =
* First release

*/

if (!class_exists('ga_authors')) {
	class ga_authors {
	
		function ga_authors() {
			$this->__construct();
			
		}
		function __construct() {
			$stored_options = get_option('ga_authors_options');
			
			$this->options = (array)(is_serialized($stored_options)) ? unserialize($stored_options) : $stored_options;
			
			// Setting filters, actions, hooks....      
			add_action("admin_menu", array (
				& $this,
				"admin_menu_link"
			));
			
			add_action('wp_footer', array(&$this,'footer'));

		}

		// -----------------------------------------------------------------------------------------------------------	
		/**
		* @desc Adds the options subpanel
		*/
		function admin_menu_link() {
			add_management_page('GA Authors', 'GA Authors', 8, basename(__FILE__), array (
				& $this,
				'admin_options_page'
			));
			add_filter('plugin_action_links_' . plugin_basename(__FILE__), array (
				& $this,
				'filter_plugin_actions'
			), 10, 2);
		}

		// -----------------------------------------------------------------------------------------------------------	
		/**
		* Adds the Settings link to the plugin activate/deactivate page
		*/
		function filter_plugin_actions($links, $file) {
			$settings_link = '<a href="tools.php?page=' . basename(__FILE__) . '">' . __('Settings') . '</a>';
			array_unshift($links, $settings_link); // before other links

			return $links;
		}
		
		function Footer(){
                        global $posts;
                        if($this->options['ga_code']){
                        ?>

<script type="text/javascript">
// GA authors tracker
        var _gaq = _gaq || [];
        _gaq.push(['_setAccount', '<?=$this->options['ga_code'];?>']);
        _gaq.push(['_trackPageview']);<?php
            if (is_single()){
                 $authorID = $posts[0]->post_author;
                ?>

        _gaq.push(['_trackPageview', '/by-author/<?php the_author_meta('display_name',$authorID);?>']);// <?php the_author_meta('ID',$authorID);?>
        <?php }?>

        (function() {
                var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
                ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
                var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
        })();
// End GA authors tracker
</script>
<?php }
                }

		// -----------------------------------------------------------------------------------------------------------	
		/**
		* Administration options page
		*/
		function admin_options_page() {
			global $wpdb;
			
			if ($_POST['ga_authors']) {
				$this->options['ga_code'] = $_POST['ga_code'];
				update_option('ga_authors_options', serialize($this->options));
			
			}
			
			?>
			<div class="wrap">
				<div id="dashboard" style="width:250px;padding:10px;">
					<h3>GA Authors options</h3>
					<form method="post">
						<div  style=""> 
							Google Analytics web property ID (UA-XXXXX-YY):<br/>
							<input type="text" name="ga_code" value="<?=$this->options['ga_code'];?>" size="20"/>
							<input type="submit" name="ga_authors" class="button-primary" value="Save" />
						</div>
					</form>
				</div>
				
				
				
			</div>
			<?php
		}

	} //End Class
}

if (class_exists('ga_authors')) {
	$wp_ga_authors_var = new ga_authors();
}
?>
