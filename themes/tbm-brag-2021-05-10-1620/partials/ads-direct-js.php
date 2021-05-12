<?php
$network_code = '9876188';
$adunit_parent_code = 'brag';
$adunit_page = 'homepage_';

if (is_home() || is_front_page()) {
  $adunit_page = 'homepage_';
} else if (is_category()) {
  if (is_category('food-drink')) {
    $adunit_page = 'food_drink_front_';
  } else if (is_category('travel')) {
    $adunit_page = 'travel_front_';
  } else if (is_category('comedy')) {
    $adunit_page = 'comedy_front_';
  } else if (is_category('culture')) {
    $adunit_page = 'culture_front_';
  } else if (is_category('adulting')) {
    $adunit_page = 'adulting_front_';
  }
} else if (is_single()) {
  if (has_term('', 'genre')) {
    $adunit_page = 'genre_article_';
  } else if (in_category('food-drink') || post_is_in_descendant_category('food-drink')) {
    $adunit_page = 'food_drink_article_';
  } else if (in_category('travel') || post_is_in_descendant_category('travel')) {
    $adunit_page = 'travel_article_';
  } else if (in_category('comedy') || post_is_in_descendant_category('comedy')) {
    $adunit_page = 'comedy_article_';
  } else if (in_category('culture') || post_is_in_descendant_category('culture')) {
    $adunit_page = 'culture_article_';
  } else if (in_category('adulting') || post_is_in_descendant_category('adulting')) {
    $adunit_page = 'adulting_article_';
  }
}
?>
<script async src="https://securepubads.g.doubleclick.net/tag/js/gpt.js"></script>
<script>
  var adslot_skin,
    adslot_leaderboard,
    adslot_side_1,
    adslot_rail_1,
    adslot_rail_2,
    adslot_rail_X;
  window.googletag = window.googletag || {
    cmd: []
  };
  googletag.cmd.push(function() {
    var mapping_skin = googletag.sizeMapping().
    addSize([0, 0], []).
    addSize([1200, 500], [1600, 1200]). // Desktop
    build();
    var adslot_skin = googletag.defineSlot('/<?php echo $network_code; ?>/<?php echo $adunit_parent_code; ?>/<?php echo $adunit_page; ?>skin', [1600, 1200], 'adm_skin').addService(googletag.pubads());
    adslot_skin.defineSizeMapping(mapping_skin);

    // var adslot_inskin = googletag.defineSlot('/9876188/brag/brag_inskin', [1, 1], 'brag_inskin').addService(googletag.pubads());

    // HREC 1
    var mapping_leaderboard = googletag.sizeMapping().
    addSize([0, 0], [
      [320, 50],
      [320, 100]
    ]).
    addSize([970, 250], [
      [970, 250],
      [970, 66],
      [970, 90],
      [728, 90]
    ]). // Desktop
    build();
    adslot_leaderboard = googletag.defineSlot('/<?php echo $network_code; ?>/<?php echo $adunit_parent_code; ?>/<?php echo $adunit_page; ?>leaderboard', [
      [970, 250],
      [970, 66],
      [970, 90],
      [728, 90],
      [320, 50],
      [320, 100]
    ], 'adm_leaderboard').addService(googletag.pubads());
    adslot_leaderboard.defineSizeMapping(mapping_leaderboard);

    <?php if (is_single()) : ?>
      var mapping_rail = googletag.sizeMapping().
      addSize([970, 250], [
        [300, 250],
        [300, 251],
        [300, 600]
      ]). // Desktop
      addSize([0, 0], []).
      build();
      // Rail 1
      adslot_rail_1 = googletag.defineSlot('/<?php echo $network_code; ?>/<?php echo $adunit_parent_code; ?>/<?php echo $adunit_page; ?>rail1', [
          [300, 250],
          [300, 251]
        ], 'adm_rail1').addService(googletag.pubads())
        .setTargeting('pos', 'rail1');
      adslot_rail_1.defineSizeMapping(mapping_rail);

      var adslot_teads = googletag.defineSlot('/9876188/brag/teads', [1, 1], 'teads-outstream').addService(googletag.pubads());

      // var adslot_minute_media = googletag.defineSlot('/9876188/brag/brag_minute_media', [1, 1], 'brag_minute_media').addService(googletag.pubads());

      // Inbody 1
      var adslot_inbody1 = googletag.defineSlot('/<?php echo $network_code; ?>/<?php echo $adunit_parent_code; ?>/<?php echo $adunit_page; ?>inbody1', [
          [300, 250],
          [300, 251]
        ], 'adm_inbody1')
        .addService(googletag.pubads())
        .setTargeting('count', '1')
        .setTargeting('pos', 'inbody1');
    <?php else : ?>
      // Rail 1
      adslot_rail_1 = googletag.defineSlot('/<?php echo $network_code; ?>/<?php echo $adunit_parent_code; ?>/<?php echo $adunit_page; ?>rail1', [
          [300, 250],
          [300, 251]
        ], 'adm_rail1')
        .addService(googletag.pubads())
        .setTargeting('count', '1')
        .setTargeting('pos', 'rail1');
    <?php endif; ?>

    // Rail 2
    adslot_rail_2 = googletag.defineSlot('/<?php echo $network_code; ?>/<?php echo $adunit_parent_code; ?>/<?php echo $adunit_page; ?>rail2', [
        [300, 600],
        [300, 250],
        [300, 251]
      ], 'adm_rail2')
      .addService(googletag.pubads())
      .setTargeting('count', '1')
      .setTargeting('pos', 'rail2');
    <?php if (is_single()) : ?>
      adslot_rail_2.defineSizeMapping(mapping_rail);
    <?php endif; ?>

    // Rail X
    adslot_rail_X = googletag.defineSlot('/<?php echo $network_code; ?>/<?php echo $adunit_parent_code; ?>/<?php echo $adunit_page; ?>railX', [
        [300, 250],
        [300, 251]
      ], 'adm_railX')
      .addService(googletag.pubads())
      .setTargeting('count', '1')
      .setTargeting('pos', 'railX');
    <?php if (is_single()) : ?>
      adslot_rail_X.defineSizeMapping(mapping_rail);
    <?php endif; ?>

    googletag.pubads().enableSingleRequest();
    googletag.pubads().collapseEmptyDivs();
    googletag.pubads().enableLazyLoad({
      fetchMarginPercent: 10,
      renderMarginPercent: 5,
      mobileScaling: 2.0 // Double the above values on mobile.
    });

    <?php
    if (isset($_GET['screenshot'])) {
      $pagepath = 'screenshot';
    } else if (isset($_GET['dfp_key'])) {
      $pagepath = $_GET['dfp_key'];
    } else if (is_home() || is_front_page()) {
      $pagepath = 'homepage';
    } else {
      $pagepath_uri = substr(str_replace('/', '', $_SERVER['REQUEST_URI']), 0, 40);
      $pagepath_e = explode('?', $pagepath_uri);
      $pagepath = $pagepath_e[0];
    }
    ?>
    googletag.pubads().setTargeting("pagepath", '<?php echo $pagepath; ?>');

    var fn_pageskin = "false";
    if (screen.width >= 1230) {
      fn_pageskin = "true";
    }
    // googletag.pubads().setTargeting("inskin_yes",fn_pageskin);

    googletag.enableServices();

    googletag.pubads().addEventListener('slotRenderEnded', function(event) {

      /*
      if ( event.slot == adslot_leaderboard && event.isEmpty ) {
        var newScript = document.createElement("script");
        var inlineScript = document.createTextNode("propertag.cmd.push(function() { proper_display('thebrag_main_1'); });");
        newScript.appendChild(inlineScript);

        var slot_id_leaderboard = event.slot.getSlotElementId();
        jQuery('#' + slot_id_leaderboard ).show();
        document.getElementById(slot_id_leaderboard).innerHTML = '<div class="proper-ad-unit"><div id="proper-ad-thebrag_main_1"></div></div>';
        document.getElementById(slot_id_leaderboard).appendChild( newScript);

        jQuery('#' + slot_id_leaderboard ).show();
      }

      if ( event.slot == adslot_rail_1 && event.isEmpty ) {
        var newScript = document.createElement("script");
        var inlineScript = document.createTextNode("propertag.cmd.push(function() { proper_display('thebrag_side_1'); });");
        newScript.appendChild(inlineScript);

        var slot_id_vrec_1 = event.slot.getSlotElementId();
        jQuery('#' + slot_id_vrec_1 ).show();
        document.getElementById(slot_id_vrec_1).innerHTML = '<div class="proper-ad-unit"><div id="proper-ad-thebrag_side_1"></div></div>';
        document.getElementById(slot_id_vrec_1).appendChild( newScript);

        jQuery('#' + slot_id_vrec_1 ).show();
      }

      if ( event.slot == adslot_rail_2 && event.isEmpty ) {
        var newScript = document.createElement("script");
        var inlineScript = document.createTextNode("propertag.cmd.push(function() { proper_display('thebrag_content_6'); });");
        newScript.appendChild(inlineScript);

        var slot_id_vrec_2 = event.slot.getSlotElementId();
        jQuery('#' + slot_id_vrec_2 ).show();
        document.getElementById(slot_id_vrec_2).innerHTML = '<div class="proper-ad-unit"><div id="proper-ad-thebrag_content_6"></div></div>';
        document.getElementById(slot_id_vrec_2).appendChild( newScript);

        jQuery('#' + slot_id_vrec_2 ).show();
      }
      */
    });
  });
</script>