<?php
function bcg_get_categories_dropdown() {
    $categories = get_categories(['hide_empty' => 0]);
    $options = '';
    foreach ($categories as $category) {
        $options .= sprintf('<option value="%s">%s</option>', esc_attr($category->term_id), esc_html($category->name));
    }
    return $options;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $content_type = sanitize_text_field($_POST['bcg_content_type']);
    $heading = sanitize_text_field($_POST['bcg_heading']);
    $url_structure = sanitize_text_field($_POST['bcg_url_structure']);
    $template = wp_kses_post($_POST['bcg_template']);
    $cities_text = sanitize_textarea_field($_POST['bcg_cities']);
    $cities = array_filter(array_map('trim', explode("\n", $cities_text)));

    if ($content_type === 'category') {
        $category_name_template = sanitize_text_field($_POST['bcg_category_name']);
        $category_slug = sanitize_text_field($_POST['bcg_category_slug']);
        $category_description = sanitize_text_field($_POST['bcg_category_description']);

        foreach ($cities as $city) {
            $cat_name = str_replace('[city]', $city, $category_name_template);
            $cat_slug = str_replace('[city]', sanitize_title($city), $category_slug);
            $cat_description = str_replace('[city]', $city, $category_description);

            wp_insert_term(
                $cat_name,
                'category',
                [
                    'slug' => $cat_slug,
                    'description' => $cat_description,
                ]
            );
        }
        echo '<div class="notice notice-success"><p>Bulk categories generated successfully.</p></div>';
    } else {
        if (empty($template)) {
            echo '<div class="notice notice-error"><p>Please provide a template for ' . $content_type . 's.</p></div>';
        } else {
            $category_id = intval($_POST['bcg_category']);
            $categories = $category_id ? [$category_id] : [];
            $focus_keyword_template = sanitize_text_field($_POST['bcg_focus_keyword']);

            foreach ($cities as $city) {
                $post_title = str_replace('[city]', $city, $heading);
                $post_content = str_replace('[city]', $city, $template);
                $post_slug = str_replace('[city]', sanitize_title($city), $url_structure);
                $new_content = [
                    'post_title' => $post_title,
                    'post_content' => $post_content,
                    'post_status' => 'publish',
                    'post_author' => get_current_user_id(),
                    'post_type' => $content_type,
                    'post_name' => $post_slug,
                    'post_category' => $categories,
                ];

                $post_id = wp_insert_post($new_content);
                $focus_keyword = str_replace('[city]', sanitize_title($city), $focus_keyword_template);
                update_post_meta($post_id, 'rank_math_focus_keyword', $focus_keyword);
            }
            echo '<div class="notice notice-success"><p>Bulk ' . $content_type . 's generated successfully.</p></div>';
        }
    }
}
?>

<div class="wrap">
    <h1>Bulk Content Generator</h1>
    <form method="post">
        <h2>Content Type</h2>
        <p>Select the type of content to generate.</p>
        <select name="bcg_content_type" id="bcg_content_type">
            <option value="post">Post</option>
            <option value="page">Page</option>
            <option value="category">Category</option>
        </select>

        <h2>Heading</h2>
        <p>Enter the heading for the content (applicable only for posts and pages). Use [city] as a placeholder for the city name.</p>
        <input type="text" name="bcg_heading" size="80">

        <h2>URL Structure</h2>
        <p>Enter the URL structure for the content (applicable only for posts and pages). Use [city] as a placeholder for the city name, e.g., hotels-in-[city].</p>
        <input type="text" name="bcg_url_structure" size="80">

        <h2>Template</h2>
        <p>Enter the template for the content (applicable only for posts and pages). Use [city] as a placeholder for the city name.</p>
        <?php wp_editor('', 'bcg_template'); ?>

        <h2>Category</h2>
        <p>Select the category for posts and pages.</p>
        <select name="bcg_category">
            <option value="">Select a category</option>
            <?php echo bcg_get_categories_dropdown(); ?>
        </select>

        <h2>Category Name</h2>
        <p>Enter the category name template. Use [city] as a placeholder for the city name.</p>
        <input type="text" name="bcg_category_name" size="80">

        <h2>Category Slug</h2>
        <p>Enter the slug structure for categories. Use [city] as a placeholder for the city name, e.g., hotels-in-[city].</p>
        <input type="text" name="bcg_category_slug" size="80">

        <h2>Category Description</h2>
        <p>Enter the description for categories. Use [city] as a placeholder for the city name.</p>
        <input type="text" name="bcg_category_description" size="80">

        <h2>Focus Keyword</h2>
        <p>Enter the focus keyword template for Rank Math SEO. Use [city] as a placeholder for the city name, e.g., "hotels in [city]".</p>
        <input type="text" name="bcg_focus_keyword" size="80">

        <h2>Cities</h2>
        <p>Enter the list of cities, one per line.</p>
        <textarea name="bcg_cities" rows="10" cols="80"></textarea>

        <p>
            <input type="submit" name="submit" class="button button-primary" value="Generate Content">
        </p>
    </form>
</div>
