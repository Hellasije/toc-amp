<?php
/*
 * Plugin Name: Table of Contents AMP
 * Plugin URI: https://www.ascic/tocamp/
 * Description: Generates a table of contents for posts and pages based on the subheadings in the post content. Works on AMP too.
 * Tags: table of content, toc, amp, simple toc, toc amp
 * Version: 1.2
 * Tested up to: 6.1.1
 * Requires PHP: 7.4
 * Author: Zeljko Ascic
 * Author URI:  https://www.ascic.net/
 */

add_shortcode('table_of_contents', 'generate_table_of_contents_shortcode');

function generate_table_of_contents($content) {
    if (!is_singular()) {
        return $content;
    }
    // Get the $auto_insert_posts and $auto_insert_pages settings from the WordPress database
    $auto_insert_posts = get_option('toc_auto_insert_posts', true);
    $auto_insert_pages = get_option('toc_auto_insert_pages', true);
    if (is_single() && !$auto_insert_posts) {
        return $content;
    }

    if (is_page() && !$auto_insert_pages) {
        return $content;
    }
	// Get the table of contents title from the plugin's settings
    $toc_title = get_option('toc_title', 'Table of Contents');
	// Check the number of headings in the content
    $num_headings = preg_match_all('/<h[1-6]/i', $content, $matches);

    // If there are no headings or only one heading, return the content without a table of contents
    if ($num_headings < 2) {
        return $content;
    }

    $content = preg_replace_callback(
        '/<h([1-6])>(.*?)<\/h[1-6]>/i',
        function($matches) {
            $level = $matches[1];
            $title = $matches[2];
            $id = sanitize_title($title);
            return '<h' . $level . ' id="' . $id . '">' . $title . '</h' . $level . '>';
        },
        $content
    );
	$details_open = get_option('toc_details_open', true);
	$open_attribute = $details_open ? 'open' : '';
	$table_of_contents = '<div class="table-of-contents">';
	$table_of_contents .= '<details ' . $open_attribute . '>';
	$table_of_contents .= '<summary>' . esc_html($toc_title) . '</summary>';
    $table_of_contents .= '<ul>';
    $current_level = 1;
    $content = preg_replace_callback(
        '/<h([1-6]) id="(.*?)">(.*?)<\/h[1-6]>/i',
        function($matches) use (&$table_of_contents, &$current_level) {
            $level = $matches[1];
            $id = $matches[2];
            $title = $matches[3];
            while ($level > $current_level) {
                $table_of_contents .= '<ul>';
                $current_level++;
            }
            while ($level < $current_level) {
                $table_of_contents .= '</ul>';
				$current_level--;
			}
	$table_of_contents .= '<li><a href="#' . esc_attr($id) . '">' . $title . '</a></li>';
	return '<h' . $level . ' id="' . $id . '">' . $title . '</h' . $level . '>';
		},
		$content
);
while ($current_level > 1) {
$table_of_contents .= '</ul>';
$current_level--;
}
$table_of_contents .= '</ul></details></div>';
$content = $table_of_contents . $content;
return $content;
}

add_filter('the_content', 'generate_table_of_contents');

function generate_table_of_contents_hierarchy_html($hierarchy) {
    $html = '<ul>';
    foreach ($hierarchy as $level) {
        foreach ($level['items'] as $item) {
            $html .= '<li><a href="#' . $item['id'] . '">' . $item['title'] . '</a>';
            if (!empty($level['items'])) {
                $html .= generate_table_of_contents_hierarchy_html($level['items']);
            }
            $html .= '</li>';
        }
    }
    $html .= '</ul>';

    return $html;
}
// Add Menu Settings for the plugin
function toc_add_menu() {
    add_options_page(
        'Table of Contents AMP Settings',
        'Table of Contents AMP',
        'manage_options',
        'toc_options',
        'toc_options_page'
    );
}
add_action('admin_menu', 'toc_add_menu');

function toc_register_settings() {
    register_setting('toc_options', 'toc_auto_insert_posts');
    register_setting('toc_options', 'toc_auto_insert_pages');
	register_setting('toc_options', 'toc_title', 'sanitize_text_field');
	register_setting('toc_options', 'toc_details_open');
	register_setting( 'toc_options', 'toc_custom_css' );
}
add_action('admin_init', 'toc_register_settings');

function toc_options_page() {
    ?>
    <div class="wrap">
        <h1>Table of Contents AMP Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('toc_options');
            do_settings_sections('toc_options');
            // Add a form field to allow the user to edit the table of contents title
            ?>
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="toc_title">Table of Contents Title</label></th>
                    <td><?php $toc_title = get_option('toc_title', 'Table of Contents');?><input type="text" name="toc_title" value="<?php echo esc_attr($toc_title); ?>"></td>
                </tr>
            </table>
            <?php

            // Add a checkbox to enable or disable automatic insertion of TOC for posts
            $auto_insert_posts = get_option('toc_auto_insert_posts', true);
            ?>
            <fieldset>
                <legend class="screen-reader-text">
                    <span>Insert TOC for posts</span>
                </legend>
                <label for="toc_auto_insert_posts">
                    <input name="toc_auto_insert_posts" type="checkbox" id="toc_auto_insert_posts" value="1" <?php checked($auto_insert_posts); ?>>
                    Insert TOC for posts
                </label>
            </fieldset>
            <?php

            // Add a checkbox to enable or disable automatic insertion of TOC for pages
            $auto_insert_pages = get_option('toc_auto_insert_pages', true);
            ?>
            <fieldset>
                <legend class="screen-reader-text">
                    <span>Insert TOC for pages</span>
                </legend>
                <label for="toc_auto_insert_pages">
                    <input name="toc_auto_insert_pages" type="checkbox" id="toc_auto_insert_pages" value="1" <?php checked($auto_insert_pages); ?>>
					Insert TOC for pages
            </label>
            </fieldset>
			<?php
            // Add a checkbox to enable or disable auto collapsing of TOC
            $toc_details_open = get_option('toc_details_open', true);
            ?>
            <fieldset>
                <legend class="screen-reader-text">
                    <span>Check for TOS to be expanded, unchek to be closed.</span>
                </legend>
                <label for="toc_details_open">
                    <input name="toc_details_open" type="checkbox" id="toc_details_open" value="1" <?php checked($toc_details_open); ?>>
					Check for TOS to be expanded, uncheck to be closed.
            </label>
            </fieldset>
			<?php
			// Add a textarea to allow the user to add custom CSS
		    $custom_css = get_option('toc_custom_css', '');
    		?>
    		<table class="form-table">
        	<tr>
            	<th scope="row"><label for="toc_custom_css">Custom CSS</label></th>
            	<td><textarea name="toc_custom_css" id="toc_custom_css" rows="10" cols="50"><?php echo esc_textarea($custom_css); ?></textarea></td>
        	</tr>
			<tr>
				<th>Reset CSS to default initial settings</th>
				<td><input type="button" value="Restore Default" id="restore-default-css" class="button"></td>
			</tr>
    		</table>
			<h2>
				Shortcode usage
			</h2>
			<p>
				You can use the following shortcode <strong>[table_of_contents]</strong> to show TOC anywhere in content. <br>
				You can also specify the custom title attribute <strong>title="Table of Contents"</strong>. It should look like this <strong>[table_of_contents title="Table of Contents" ]</strong>. If attribute is not specifed, [table_of_contents] shortcode will use title from the settings.<br>
				To hide the title completely in output add the <strong>show_title="0" attribute to shortcode</strong>. For example <strong>[table_of_contents title="Table of Contents" show_title="0"]</strong>.
			</p>
			<h2>
				 Usage in template
			</h2>
			<p>
			    To use it somewhere in the template you can echo shortcode like this <strong>&lt;?php echo do_shortcode('[table_of_contents]'); ?&gt;</strong>"
			</p>
			<h2>
				Usage in sidebar
			</h2>
			<p>
				If you wish, you can use the shortcode <strong>[table_of_contents show_title="0"]</strong> in the sidebar too. Enter "TOC" title as the widget title.<br>
				In order for the widget title to not be show everywhere, you could use <a href="https://wordpress.org/plugins/widget-logic/">Widget Logic</a> plugin to show it on specific places. For example, to show the widget only on pages and post you can add the following condition <strong>is_single() or is_page()</strong> to widget logic.
			</p>
			<?php
            submit_button();
            ?>
        </form>
<script>
function restoreDefaultCSS() {
  var default_css = `.table-of-contents summary {
      font-size: 20px;
      border-bottom:1px solid #999;
  }

  .table-of-contents {
      background-color: #f0f0f0;
      padding: 10px;
      font-size:16px;
      margin-bottom: 25px;
  }

  .table-of-contents ul {
      list-style: circle;
      margin:5px;
  }

  .table-of-contents li {
      font-weight: bold;
      margin-top: 10px;
      margin-bottom: 10px;
  }

  .table-of-contents ul li {
      line-height: 30px;
      font-size:16px;
  }
  `;

  // Set the value of the textarea to the default value
  document.getElementById('toc_custom_css').value = default_css;
}

// Add an event listener to the button to call the function when the button is clicked
document.getElementById('restore-default-css').addEventListener('click', restoreDefaultCSS);
</script>
    </div>
    <?php
}

// Define a callback function to echo the custom CSS code
function add_toc_custom_css() {
  // Get the custom CSS code from the textarea
  $custom_css = get_option('toc_custom_css', '');
  // Echo the custom CSS code
  echo '<style type="text/css">' . $custom_css . '</style>';
}

// Add the callback function to the wp_head action hook
add_action('wp_head', 'add_toc_custom_css', 100);

function generate_table_of_contents_shortcode($atts) {
    // Get the table of contents title from the shortcode attributes
$atts = shortcode_atts(array(
    'title' => get_option('toc_title', 'Table of Contents'),
	'show_title' => '1'
), $atts);
    // Get the post content
    $content = get_the_content();
    // Check the number of headings in the content
    $num_headings = preg_match_all('/<h[1-6]/i', $content, $matches);

    // If there are no headings or only one heading, return an empty string
    if ($num_headings < 2) {
        return '';
    }

    $content = preg_replace_callback(
        '/<h([1-6])>(.*?)<\/h[1-6]>/i',
        function($matches) {
            $level = $matches[1];
            $title = $matches[2];
            $id = sanitize_title($title);
            return '<h' . $level . ' id="' . $id . '">' . $title . '</h' . $level . '>';
        },
        $content
    );
	$show_title = $atts['show_title'];
    $table_of_contents = '<div class="table-of-contents">';
    if ($show_title != '0') {
        $table_of_contents .= '<h3>' . esc_html($atts['title']) . '</h3>';
    }
    $table_of_contents .= '<ul>';
    $current_level = 1;
    $content = preg_replace_callback(
        '/<h([1-6]) id="(.*?)">(.*?)<\/h[1-6]>/i',
        function($matches) use (&$table_of_contents, &$current_level) {
            $level = $matches[1];
            $id = $matches[2];
            $title = $matches[3];
            while ($level > $current_level) {
                $table_of_contents .= '<ul>';
                $current_level++;
            }
            while ($level < $current_level) {
                $table_of_contents .= '</ul>';
                $current_level--;
            }
            $table_of_contents .= '<li><a href="#' . esc_attr($id) . '">' . $title . '</a></li>';
            return '<h' . $level . ' id="' . $id . '">' . $title . '</h' . $level . '>';
},
$content
);
while ($current_level > 1) {
$table_of_contents .= '</ul>';
$current_level--;
}
$table_of_contents .= '</ul></div>';
return $table_of_contents;
}