# ACF helpers

Some general helpers that save writing get_post_meta and avoids using get_field/the_field, as such this reduces the query 
count by a significant amount when using a large amount of custom fields.

The only consideration of note is images are saved to an additional meta or option field which allows the further 
reduction on queries per page especially in image heavy scenarios but this can lead to a larger data set. 

```php
// Get field
wp_get_field('field_name', true, 20);

// The field
wp_the_field('field_name', true, 20);

// Load an image (must be returning image id)
wp_get_image('field_name', 20);
```

### Examples
```php
// Heading
<h2><?php wp_the_field('heading'); ?></h2>


// WYSIWYG
<div><?php echo apply_filters('the_content', wp_get_field('wysiwyg')); ?></div>

// Image
<?php $image = wp_get_image('image_field'); ?>
<img src="<?php echo $image->url; ?>" alt="<?php echo $image->alt; ?>">
```

