<?php $caption_style = theme_get_setting('caption_style');?>

<div id="slideshow" class="main-slider <?php print $caption_style; ?>">

  <div class="flexslider">
    <ul class="slides">
      <?php foreach ($rows as $id => $row) { ?>
        <li>

        <?php
          $nodeType = $view->render_field('type', $id);
          $image = $view->render_field('field_teaser_image', $id);
          $title = $view->render_field('title', $id);
          $teaser_text = $view->render_field('field_teaser_text', $id);

          if ($nodeType=='mt_slideshow_entry') {
            $path = $view->render_field('field_slideshow_entry_path', $id);
            $slideshow_caption = $view->render_field('field_slideshow_caption', $id);
              if ($path) { ?>
                <div class="views-field views-field-field-teaser-image">
                  <div class="field-content">
                    <a href="<?php print url($path); ?>"><?php print $image; ?></a>
                  </div>
                </div>
              <?php } else { ?>
                <div class="views-field views-field-field-teaser-image">
                  <div class="field-content">
                    <?php print $image; ?>
                  </div>
                </div>
              <?php } ?>

              <?php if($slideshow_caption == "1") { ?>
                <div class="caption-wrapper">
                  <div class="caption">

                    <?php
                      $title_suffix="";
                      if (strlen($title)>70): $title_suffix="..."; endif;
                      $title = substr($title,0, 70);
                      if ($path) { ?>
                        <h1><a href="<?php print url($path); ?>"><?php print strip_tags($title) . $title_suffix; ?></a></h1>
                      <?php } else { ?>
                        <h1><?php print strip_tags($title) . $title_suffix; ?></h1>
                      <?php } ?>

                    <?php if ($teaser_text): ?>
                      <?php
                      $text_teaser_suffix="";
                      if (strlen($teaser_text)>100): $text_teaser_suffix="..."; endif;
                      $teaser_text = substr($teaser_text,0, 100);
                      ?>
                      <div class="hidden-sm hidden-xs text"><p><?php print $teaser_text . $text_teaser_suffix; ?></p></div>
                    <?php endif; ?>

                    <?php if ($path): ?>
                      <a href="<?php print url($path); ?>" class="more"><?php print t('Read more'); ?></a>
                    <?php endif; ?>

                  </div>
                </div>
              <?php } ?>

          <?php } else { print $row; } ?>

        </li>
      <?php } ?>
    </ul>
  </div>
</div>
<?php
  $effect=theme_get_setting('slideshow_effect');
  $effect_time=theme_get_setting('slideshow_effect_time')*1000;

  drupal_add_js('jQuery(document).ready(function($) {

    $(window).load(function() {
      $("#slideshow .flexslider").fadeIn("slow");
      $("#slideshow .flexslider").flexslider({
        animation: "'.$effect.'",             // Select your animation type, "fade" or "slide"
        slideshowSpeed: "'.$effect_time.'",   // Set the speed of the slideshow cycling, in milliseconds
        prevText: "",
        nextText: "",
        pauseOnAction: false,
        useCSS: false
      });

    });
  });',
  array('type' => 'inline', 'scope' => 'header'));

?>
