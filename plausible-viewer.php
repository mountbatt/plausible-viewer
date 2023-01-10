<?php
/**
 * Plausible Viewer
 *
 * @package       PLAUSIBLEV
 * @author        Tobias Battenberg
 * @license       gplv2
 * @version       1.0.0
 *
 * @wordpress-plugin
 * Plugin Name:   Plausible Viewer
 * Plugin URI:    https://github.com/mountbatt/wp-plausible-viewer
 * Description:   Embeds plausibe.io Analytics and displays plausible.io or selfhostet plausible iframes in WordPress. 
 * Version:       1.0.0
 * Author:        Tobias Battenberg
 * Author URI:    https://github.com/mountbatt/
 * Text Domain:   plausible-viewer
 * Domain Path:   /languages
 * License:       GPLv2
 * License URI:   https://www.gnu.org/licenses/gpl-2.0.html
 *
 * You should have received a copy of the GNU General Public License
 * along with Plausible Viewer. If not, see <https://www.gnu.org/licenses/gpl-2.0.html/>.
 */
defined( 'ABSPATH' ) or die( 'Are you ok?' );

// Add to <head> in frontend
function hook_plausible_viewer_code() {
  if(!is_admin()){
    $options = get_option( 'plausible_viewer_options' );
    $embed_code = $options['embedcode'];
    if($embed_code):
      if( !current_user_can('edit_posts') ) {
        echo "<!-- Plausible embed Code: -->\n";
        echo $embed_code;
        echo "\n<!-- End of Plausible embed Code -->\n";
      } else {
        echo "<!-- Plausible embed code disabled cause you are logged in as editor or admin -->";
      }
    endif;
  }
}
add_action('wp_head', 'hook_plausible_viewer_code');

// Add to end of </body> in frontend
function hook_plausible_viewer_bodycode() {
  if(!is_admin()){
    $options = get_option( 'plausible_viewer_options' );
    $body_code = $options['bodycode'];
    if($body_code):
      if( !current_user_can('edit_posts') ) {
        echo "<!-- Plausible Code: -->\n";
        echo $body_code;
        echo "\n<!-- End of Plausible Code -->\n";
      } else {
        echo "<!-- Plausible code disabled cause you are logged in as editor or admin -->";
      }
    endif;
  }
}
add_action('wp_footer', 'hook_plausible_viewer_bodycode');


  
  // DASHBOARD: 
  function plausibleviewer_options_page_html() {
      ?>
      <div class="wrap">
        <!--<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>-->
          <?php
          $options = get_option( 'plausible_viewer_options' );
          $shared_url = $options['shared_url'];
          if($shared_url):
              ?>
              <iframe plausible-embed src="<?php echo $shared_url; ?>&embed=true&theme=light&background=transparent" scrolling="no" frameborder="0" loading="lazy" style="width: 1px; min-width: 100%; height: 1600px;"></iframe>
              <script async src="//plausible.io/js/embed.host.js"></script>
              <?php
          else:
            ?>
            <p>I looks like you have not added your Shared Link into the Settings. </p>
            <a class="button" href="options-general.php?page=plausible_viewer"><?php echo esc_html__( 'Open Settings', 'plausibleviewer' ); ?></a>
            <?php
          endif; 
          ?>
      </div>
      <?php
  }
  
  add_action( 'admin_menu', 'plausibleviewer_options_page' );
  function plausibleviewer_options_page() {
      add_menu_page(
          'Plausible Viewer',
          'Plausible',
          'manage_options',
          'plausibleviewer',
          'plausibleviewer_options_page_html',
          plugin_dir_url(__FILE__) . 'logo.svg',
          20
      );
  }
  
  // OPTIONS PAGE
  
  add_action('admin_enqueue_scripts', 'codemirror_enqueue_scripts');
   
  function codemirror_enqueue_scripts($hook) {
    $cm_settings['codeEditor'] = wp_enqueue_code_editor(array('type' => 'text/css'));
    wp_localize_script('jquery', 'cm_settings', $cm_settings);
   
    wp_enqueue_script('wp-theme-plugin-editor');
    wp_enqueue_style('wp-codemirror');
  }
  
  function plv_add_settings_page() {
      add_options_page( 'Plausible Viewer Settings', 'Plausible Settings', 'manage_options', 'plausible_viewer', 'plv_render_plugin_settings_page' );
  }
  add_action( 'admin_menu', 'plv_add_settings_page' );

  
  function plv_render_plugin_settings_page() {
      ?>
      <h2>Plausible Viewer – Settings</h2>
      <form action="options.php" method="post">
        <script>
          jQuery(document).ready(function($) {
            wp.codeEditor.initialize($('.codeeditor'), cm_settings);
            wp.codeEditor.initialize($('.codeeditor2'), cm_settings);
          })
        </script>
        <style>
          .CodeMirror {
            border: 1px solid #ddd;
            width: 80%;
          }
        </style>
          <?php 
          settings_fields( 'plausible_viewer_options' );
          do_settings_sections( 'plausible_viewer' ); ?>
          <input name="submit" class="button button-primary" type="submit" value="<?php esc_attr_e( 'Save' ); ?>" />
      </form>
      
      <hr style="margin-top: 40px; margin-bottom: 40px;">
      
      <h3>Status:</h3>
      <?php
      $options = get_option( 'plausible_viewer_options' );
      if($options['embedcode']){
        ?>
        <p>✅ Tracking with this plugin is enabled.</p>
        <?php
      } else {
        ?>
        <p>🛑 Tracking with this plugin is <strong>not</strong> enabled. Enter your embed code above or add it manually in <code>&lt;head&gt;</code></p>
        <?php
      }
      
  }
  
  function plv_register_settings() {
      register_setting( 'plausible_viewer_options', 'plausible_viewer_options', 'plausible_viewer_options_validate' );
      add_settings_section( 'plv_settings', 'Plausible Settings', 'plv_plugin_section_text', 'plausible_viewer' );
  
      add_settings_field( 'plv_plugin_setting_shared_url', 'Shared Link', 'plv_plugin_setting_shared_url', 'plausible_viewer', 'plv_settings' );
      add_settings_field( 'plv_plugin_setting_embedcode', 'Embed Code', 'plv_plugin_setting_embedcode', 'plausible_viewer', 'plv_settings' );
      add_settings_field( 'plv_plugin_setting_bodycode', 'Body Code', 'plv_plugin_setting_bodycode', 'plausible_viewer', 'plv_settings' );
  }
  add_action( 'admin_init', 'plv_register_settings' );

 
  function plv_plugin_section_text() {
      echo '<p>To view the reports you have to enter the Shared Link from your Plausible Account.</p>';
  }
  
  function plv_plugin_setting_shared_url() {
      $options = get_option( 'plausible_viewer_options' );
      echo "<div><input placeholder='' id='plv_plugin_setting_shared_url' name='plausible_viewer_options[shared_url]' type='text' style='width: 80%;' value='" . esc_attr( $options['shared_url'] ) . "' /></div><small> Enter the Shared Link from your plausible instance like: <code>https://plausible.io/share/example.com?auth=a7alRYfSxyzQnvhfkEts</code></small>";
  }
  
  function plv_plugin_setting_embedcode() {
      $options = get_option( 'plausible_viewer_options' );
      echo "<div><textarea id='plv_plugin_setting_embedcode_textarea' name='plausible_viewer_options[embedcode]' class='codeeditor2' style='width: 80%;' rows='3' />". esc_attr( $options['embedcode'] ) ."</textarea></div><small> Enter the embed Code to enable tracking (optional)<br>Tracking is by default disabled to editors and administrators. Open your website in a private window to test if your tracking works!</small>";
  }
  
  function plv_plugin_setting_bodycode() {
      $options = get_option( 'plausible_viewer_options' );
      echo "<div><textarea id='plv_plugin_setting_bodycode_textarea' name='plausible_viewer_options[bodycode]' class='codeeditor' style='width: 80%;' rows='3' />". esc_attr( $options['bodycode'] ) ."</textarea></div><small> Enter additional Code for custom tracking (optional)<br>This goes at the end of the body tag.</small>";
  }
  
  function plv_plugin_settings_link($links) { 
    $settings_link = '<a href="options-general.php?page=plausible_viewer">'.esc_html__( 'Settings', 'plausibleviewer' ).'</a>'; 
    array_unshift($links, $settings_link); 
    return $links; 
  }
  $plugin = plugin_basename(__FILE__); 
  add_filter("plugin_action_links_$plugin", 'plv_plugin_settings_link' );

