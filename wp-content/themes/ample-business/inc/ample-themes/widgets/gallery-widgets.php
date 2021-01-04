<?php
if (!class_exists('Ample_Business_Gallery_Widget')) {
    class Ample_Business_Gallery_Widget extends WP_Widget
    {

        private function defaults()
        {

            $defaults = array(
                'title' => esc_html__('Our Work', 'ample-business'),
                'sub-title' => '',
                'ample_business_portfolio_filter_all' => esc_html__('All', 'ample-business'),
                'cat_id' => array(),
                'post_column' => 3,
                'post_number' => 80,
                'featured_image_size' => 'full',



            );
            return $defaults;
        }


        public function __construct()
        {
            parent::__construct(
                'ample-business-our-work-widget',
                esc_html__('AB : Work Widget', 'ample-business'),
                array('description' => esc_html__('Business Work Section', 'ample-business'))
            );
        }

        public function widget($args, $instance)
        {

            $instance = wp_parse_args((array)$instance, $this->defaults());

            if (!empty($instance)) {
                $subtitle = esc_html($instance['sub-title']);
                $title = apply_filters('widget_title', !empty($instance['title']) ? esc_html($instance['title']) : '', $instance, $this->id_base);
                $ample_business_ad_title = esc_html($instance['ample_business_portfolio_filter_all']);
                $ample_business_selected_cat = '';
                $post_number = absint($instance['post_number']);
                $column_number = absint($instance['post_column']);
                $featured_image = esc_html($instance['featured_image_size']);


                if (!empty($instance['cat_id'])) {
                    $ample_business_selected_cat = ample_business_sanitize_multiple_category($instance['cat_id']);
                    if (is_array($ample_business_selected_cat[0])) {
                        $ample_business_selected_cat = $ample_business_selected_cat[0];
                    }
                }

                echo $args['before_widget'];
                ?>
                <section id="ample-business-theme-work" class="widget widget-ample-business-theme-work">
                    <div class="container">
                        <?php
                        $sticky = get_option('sticky_posts');
                        $ample_business_cat_post_args = array(
                            'posts_per_page' => $post_number,
                            'no_found_rows' => true,
                            'post_status' => 'publish',
                            'ignore_sticky_posts' => true,
                            'post__not_in' => $sticky,
                        );
                        if (-1 != $ample_business_cat_post_args) {
                            $ample_business_cat_post_args['category__in'] = $ample_business_selected_cat;
                        }
                        $portfolio_filter_query = new WP_Query($ample_business_cat_post_args);

                        ?>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="main-title wow fadeInDown" data-wow-duration="2s">
                                    <h2 class="widget-title"><?php echo $title; ?></h2>
                                    <span class="double-border"></span>
                                    <p><?php echo $subtitle; ?> </p>
                                </div>
                                <div class="portfolioFilter text-center">
                                    <?php
                                    if (!empty($ample_business_ad_title)) {
                                        echo '<a href="#" data-filter="*" class="current">' . $ample_business_ad_title . '</a>';
                                    }

                                    if (!empty($ample_business_selected_cat) && is_array($ample_business_selected_cat)) {
                                        foreach ($ample_business_selected_cat as $ample_business_selected_single_cat) {

                                            echo ' <a href="#" data-filter=".' . esc_attr($ample_business_selected_single_cat) . '">' . esc_html(get_cat_name($ample_business_selected_single_cat)) . '</a>';
                                        }
                                    }

                                    ?>
                                </div>
                                <div class="col-md-12 extend-div">
                                    <div class="row">
                                        <div class="portfolioContainer wow slideInUp text-left" data-wow-duration="2s">


                                            <?php
                                            if ($portfolio_filter_query->have_posts()):
                                                while ($portfolio_filter_query->have_posts()):
                                                    $portfolio_filter_query->the_post();
                                                    $categories = get_the_category(get_the_ID());
                                                    if (!empty($categories)) {
                                                        foreach ($categories as $category) {
                                                            $select_cat = $category->term_id;
                                                        }
                                                    }
                                                    /*for select colmn*/
                                                    if (2 == $column_number)
                                                    {
                                                        $ample_business_column = "col-md-6";
                                                    } elseif (3 == $column_number)
                                                    {
                                                        $ample_business_column = "col-md-4";
                                                    } elseif (4 == $column_number)
                                                    {
                                                        $ample_business_column = 'col-md-3';
                                                    }
                                                    else
                                                    {
                                                        $ample_business_column = 'col-md-12';
                                                    }

                                                    $categories = get_the_category(get_the_ID());
                                                    if (!empty($categories))
                                                    {
                                                        foreach ($categories as $category)
                                                        {
                                                            $ample_business_column .= ' ' . $category->term_id;
                                                        }
                                                    }
                                                    ?>


                                                    <div class="col-xs-12 col-sm-4 <?php echo $ample_business_column; ?> <?php echo esc_attr($select_cat); ?>">
                                                        <div class="workimgoverlay">

                                                                <?php
                                                                if (has_post_thumbnail()) {

                                                                    $image_id = get_post_thumbnail_id();
                                                                    $image_url = wp_get_attachment_image_src($image_id, $featured_image, true);
                                                                    ?>
                                                                    <img src="<?php echo esc_url($image_url[0]); ?>"
                                                                         class="img-responsive">
                                                                <?php } ?>
                                                                <div class="workoverlay">
                                                                    <div class="workdetails">
                                                                        <div class="border-hover">
                                                                            <h5 class="work-title"><a href="<?php the_permalink();?>"><?php the_title();?></a></h5>

                                                                        </div>
                                                                    </div>
                                                                </div>

                                                        </div>
                                                    </div>


                                                    <?php
                                                endwhile;
                                            endif;
                                            wp_reset_postdata();
                                            ?>
                                        </div>
                                    </div>
                                </div><!--portfolioContainer-->
                            </div><!--col-md-12-->
                        </div><!--.row-->
                    </div>
                </section>

                <?php
                echo $args['after_widget'];
            }
        }

        public function update($new_instance, $old_instance)
        {
            $instance = $old_instance;
            $instance['cat_id'] = (isset($new_instance['cat_id'])) ? ample_business_sanitize_multiple_category($new_instance['cat_id']) : array();
            $instance['ample_business_portfolio_filter_all'] = sanitize_text_field($new_instance['ample_business_portfolio_filter_all']);
            $instance['title'] = sanitize_text_field($new_instance['title']);
            $instance['sub-title'] = sanitize_text_field($new_instance['sub-title']);
            $instance['post_number'] = absint( $new_instance['post_number'] );
            $instance['post_column'] = absint( $new_instance['post_column']);
            $instance['featured_image_size'] = sanitize_text_field($new_instance['featured_image_size']);
            return $instance;
        }

        public function form($instance)
        {

            $instance = wp_parse_args((array )$instance, $this->defaults());
            $title = esc_attr($instance['title']);
            $post_number = absint($instance['post_number']);
            $column_number = absint($instance['post_column']);
            $featured_image_size = esc_attr($instance['featured_image_size']);
            $subtitle = esc_attr($instance['sub-title']);
            $icon = esc_attr($instance['icon']);

            $ample_business_ad_title = esc_attr($instance['ample_business_portfolio_filter_all']);
            $ample_business_selected_cat = '';

            if (!empty($instance['cat_id'])) {
                $ample_business_selected_cat = $instance['cat_id'];
                if (is_array($ample_business_selected_cat[0])) {
                    $ample_business_selected_cat = $ample_business_selected_cat[0];
                }
            }
            ?>
            <p>
                <label
                    for="<?php echo esc_attr($this->get_field_id('title')); ?>"><strong><?php esc_html_e('Title:', 'ample-business'); ?></strong></label>
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>"
                       name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text"
                       value="<?php echo $title; ?>"/>
            </p>



            <p>
                <label for="<?php echo esc_attr( $this->get_field_id('sub-title') ); ?>">
                    <?php esc_html_e( 'Sub Title', 'ample-business'); ?>
                </label><br/>
                <input type="text" name="<?php echo esc_attr($this->get_field_name('sub-title')); ?>" class="widefat" id="<?php echo esc_attr($this->get_field_id('sub-title')); ?>" value="<?php echo $subtitle; ?>">
            </p>

            <p>
                <label
                    for="<?php echo esc_attr($this->get_field_id('ample_business_portfolio_filter_all')); ?>"><strong><?php esc_html_e('Our Work Filter All Text', 'ample-business'); ?></strong></label>
                <input class="widefat"
                       id="<?php echo esc_attr($this->get_field_id('ample_business_portfolio_filter_all')); ?>"
                       name="<?php echo esc_attr($this->get_field_name('ample_business_portfolio_filter_all')); ?>"
                       type="text" value="<?php echo $ample_business_ad_title; ?>"/>
            </p>

            <p>
                <label
                    for="<?php echo esc_attr($this->get_field_id('cat_id')); ?>"><strong><?php esc_html_e('Select Category', 'ample-business'); ?></strong></label>
                <select class="widefat" name="<?php echo $this->get_field_name('cat_id'); ?>[]"
                        id="<?php echo esc_attr($this->get_field_id('post_number')); ?>" multiple="multiple">
                    <?php
                    $option = '';
                    $categories = get_categories();
                    if ($categories) {
                        foreach ($categories as $category) {
                            $result = in_array($category->term_id, $ample_business_selected_cat) ? 'selected=selected' : '';
                            $option .= '<option value="' . esc_attr($category->term_id) . '"' . esc_attr($result) . '>';
                            $option .= esc_html($category->cat_name);
                            $option .= esc_html(' (' . $category->category_count . ')');
                            $option .= '</option>';
                        }
                    }
                    echo $option;
                    ?>
                </select>
            </p>
            <hr>


            <p>
                <label for="<?php echo esc_attr($this->get_field_id('post_number')); ?>"><strong><?php esc_html_e('Number of Posts:', 'ample-business'); ?></strong></label>
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('post_number')); ?>" name="<?php echo esc_attr($this->get_field_name('post_number')); ?>" type="number" value="<?php echo $post_number; ?>" min="1"/>
            </p>

            <p>
                <label for="<?php echo esc_attr($this->get_field_id('post_column')); ?>"><strong><?php esc_html_e('Number of Columns:', 'ample-business'); ?></strong></label>
                <?php
                $this->dropdown_post_columns(
                    array(
                        'id' => esc_attr($this->get_field_id('post_column')),
                        'name' => esc_attr($this->get_field_name('post_column')),
                        'selected' => $column_number
                    )
                );
                ?>
            </p>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id('featured_image_size')); ?>"><strong><?php esc_html_e('Select Image Size:', 'ample-business'); ?></strong></label>
                <?php
                $this->dropdown_image_sizes(array(
                        'id' => esc_attr($this->get_field_id('featured_image_size')),
                        'name' => esc_attr($this->get_field_name('featured_image_size')),
                        'selected' => $featured_image_size,
                    )
                );
                ?>
            </p>


            <?php

        }


        function dropdown_post_columns($args)
        {
            $defaults = array(
                'id' => '',
                'name' => '',
                'selected' => 0,
            );

            $r = wp_parse_args($args, $defaults);
            $output = '';

            $choices = array(
                2 => esc_html__('2', 'ample-business'),
                3 => esc_html__('3', 'ample-business'),

                4 => esc_html__('4', 'ample-business'),
            );

            if (!empty($choices))
            {

                $output = "<select name='" . esc_attr($r['name']) . "' id='" . esc_attr($r['id']) . "'>\n";
                foreach ($choices as $key => $choice) {
                    $output .= '<option value="' . esc_attr($key) . '" ';
                    $output .= selected($r['selected'], $key, false);
                    $output .= '>' . esc_html($choice) . '</option>\n';
                }
                $output .= "</select>\n";
            }

            echo $output;
        }

        function dropdown_image_sizes($args)
        {
            $defaults = array(
                'id' => '',
                'class' => 'widefat',
                'name' => '',
                'selected' => 0,
            );

            $r = wp_parse_args($args, $defaults);
            $output = '';

            $choices = array(
                'thumbnail' => esc_html__('Thumbnail', 'ample-business'),
                'medium' => esc_html__('Medium', 'ample-business'),
                'large' => esc_html__('Large', 'ample-business'),
                'full' => esc_html__('Full', 'ample-business'),
            );

            if (!empty($choices))
            {

                $output = "<select name='" . esc_attr($r['name']) . "' id='" . esc_attr($r['id']) . "' class='" . esc_attr($r['class']) . "'>\n";
                foreach ($choices as $key => $choice)
                {
                    $output .= '<option value="' . esc_attr($key) . '" ';
                    $output .= selected($r['selected'], $key, false);
                    $output .= '>' . esc_html($choice) . '</option>\n';
                }
                $output .= "</select>\n";
            }

            echo $output;
        }



    }
}

add_action('widgets_init', 'ample_business_Gallery_widget');
function ample_business_Gallery_widget()
{
    register_widget('Ample_Business_Gallery_Widget');

}
