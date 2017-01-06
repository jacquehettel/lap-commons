<?php if (!$label_hidden) : ?>
<div class="field-label"<?php print $title_attributes; ?>><?php print $label ?>:&nbsp;</div>
<?php endif; ?>

<?php
// Reduce number of images in teaser view mode to single image
if ($element['#view_mode'] == 'teaser') : ?>
  <div class="field-item field-type-image even"<?php print $item_attributes[0]; ?>>
    <div class="overlayed"><?php print render($items[0]); ?></div>
  </div>
<?php return; endif; ?>

<?php
  $node=$element['#object'];
  $images = field_get_items('node', $node, 'field_image');
  $first_image = field_view_value('node', $node, 'field_image', $images[0], array('type' => 'image','settings' => array('image_style' => 'large',)));
  $first_image_file = file_load($images[0]['fid']);
  $first_image_uri = $first_image_file->uri;
?>

<div class="images-container clearfix">

  <div class="image-preview">
    <div class="overlayed large plus">
      <a class="image-popup" href="<?php print file_create_url($first_image_uri); ?>" title="<?php print $images[0]['title']; ?>">
        <?php print render($first_image); ?>
      </a>
    </div>

    <?php if ($images[0]['title']) :?>
    <div class="image-caption clearfix">
      <p><?php print ($images[0]['title']); ?></p>
    </div>
    <?php endif; ?>

  </div>

  <?php if (count($images)>1) { ?>
    <div class="image-listing-items">

      <?php $i=0; foreach ($images as $key=>$file) {
        if ($key==0) { continue; }
        $image = field_view_value('node', $node, 'field_image', $images[$key], array('type' => 'image','settings' => array('image_style' => 'small',)));
        $image_file = file_load($images[$key]['fid']);
        $image_uri = $image_file->uri;
        ?>
        <div class="image-listing-item overlayed small plus">
          <a class="image-popup" href="<?php print file_create_url($image_uri); ?>" title="<?php print $images[$key]['title']; ?>">
            <?php print render($image); ?>
          </a>
        </div>
      <?php } ?>

    </div>
  <?php } ?>

</div>

<?php
  drupal_add_js(drupal_get_path('theme', 'scholarly') .'/js/magnific-popup/jquery.magnific-popup.js', array('preprocess' => false));
  drupal_add_css(drupal_get_path('theme', 'scholarly') . '/js/magnific-popup/magnific-popup.css');

  drupal_add_js('
    jQuery(document).ready(function($) {
      $(window).load(function() {

        $(".image-popup").magnificPopup({
          type:"image",
          removalDelay: 300,
          mainClass: "mfp-fade",
          gallery: {
            enabled: true, // set to true to enable gallery
          }
        });

      });
    });',array('type' => 'inline', 'scope' => 'footer', 'weight' => 3)
  );
?>
