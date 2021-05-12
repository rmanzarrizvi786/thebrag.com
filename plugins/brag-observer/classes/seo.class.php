<?php
class SEO {
  public function wpseo_opengraph_image( $url ) {
    global $wp_query;
    $observer_slug = $wp_query->get( 'observer_slug' );
    if ( $observer_slug ) {
      global $wpdb;
      return $wpdb->get_var( "SELECT image_url FROM {$wpdb->prefix}observer_lists WHERE slug = '{$observer_slug}' LIMIT 1" );
    }
    return $url;
  }

  public function wpseo_canonical( $canonical ) {
    global $wp_query;
    $observer_slug = $wp_query->get( 'observer_slug' );
    if ( $observer_slug ) {
      global $wpdb;
      $slug = $wpdb->get_var( "SELECT slug FROM {$wpdb->prefix}observer_lists WHERE slug = '{$observer_slug}' LIMIT 1" );
      if ( $slug ) {
        return home_url( '/observer/' . $slug . '/' );
      }
    }

    $category_slug = $wp_query->get( 'category_slug' );
    if ( $category_slug ) {
      global $wpdb;
      $slug = $wpdb->get_var( "SELECT slug FROM {$wpdb->prefix}observer_categories WHERE slug = '{$category_slug}' LIMIT 1" );
      if ( $slug ) {
        return home_url( '/observer/category/' . $slug . '/' );
      }
    }

    return $canonical;
  }

  public function wpseo_opengraph_url( $url ) {
    global $wp_query;
    $observer_slug = $wp_query->get( 'observer_slug' );
    if ( $observer_slug ) {
      global $wpdb;
      $slug = $wpdb->get_var( "SELECT slug FROM {$wpdb->prefix}observer_lists WHERE slug = '{$observer_slug}' LIMIT 1" );
      if ( $slug ) {
        return home_url( '/observer/' . $slug . '/' );
      }
    }

    $category_slug = $wp_query->get( 'category_slug' );
    if ( $category_slug ) {
      global $wpdb;
      $slug = $wpdb->get_var( "SELECT slug FROM {$wpdb->prefix}observer_categories WHERE slug = '{$category_slug}' LIMIT 1" );
      if ( $slug ) {
        return home_url( '/observer/category/' . $slug . '/' );
      }
    }

    return $url;
  }

  public function wpseo_opengraph_title( $title ) {
    global $wp_query;
    $observer_slug = $wp_query->get( 'observer_slug' );
    $category_slug = $wp_query->get( 'category_slug' );

    global $wpdb;
    if ( $observer_slug ) {
      $list = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}observer_lists WHERE slug = '{$observer_slug}' LIMIT 1" );
      $title_part = trim( str_ireplace( 'Observer', '', $list->title ) );
      return $title_part . ' Observer Newsletter';
      // return 'The ' . $wpdb->get_var( "SELECT title FROM {$wpdb->prefix}observer_lists WHERE slug = '{$observer_slug}' LIMIT 1" ) . ' Observer Newsletter';
    } else if ( $category_slug ) {
      return $wpdb->get_var( "SELECT title FROM {$wpdb->prefix}observer_categories WHERE slug = '{$category_slug}' LIMIT 1" ) . ' - The Brag Observer';
    }
    return $title;
  }

  public function wpseo_opengraph_desc( $desc ) {
    global $wp_query;
    $observer_slug = $wp_query->get( 'observer_slug' );
    $category_slug = $wp_query->get( 'category_slug' );

    global $wpdb;
    if ( $observer_slug ) {
      $list = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}observer_lists WHERE slug = '{$observer_slug}' LIMIT 1" );
      $title_part = trim( str_ireplace( 'Observer', '', $list->title ) );
      return 'Get the latest ' . $title_part . ' news, features, updates and giveaways straight to your inbox';
    } else if ( $category_slug ) {
      $description = $wpdb->get_var( "SELECT description FROM {$wpdb->prefix}observer_categories WHERE slug = '{$category_slug}' LIMIT 1" );
      if ( $description ) {
        return $description;
      }
    }
    return $desc;
  }

  public function wpseo_title( $title ) {
    global $wp_query;
    $observer_slug = $wp_query->get( 'observer_slug' );
    $category_slug = $wp_query->get( 'category_slug' );

    global $wpdb;
    if ( $observer_slug ) {
      return $wpdb->get_var( "SELECT title FROM {$wpdb->prefix}observer_lists WHERE slug = '{$observer_slug}' LIMIT 1" ) . ' - The Brag Observer';
    } else if ( $category_slug ) {
      return $wpdb->get_var( "SELECT title FROM {$wpdb->prefix}observer_categories WHERE slug = '{$category_slug}' LIMIT 1" ) . ' - The Brag Observer';
    }
    return $title;
  }

  public function wpseo_metadesc( $desc ) {
    global $wp_query;
    $observer_slug = $wp_query->get( 'observer_slug' );
    $category_slug = $wp_query->get( 'category_slug' );

    global $wpdb;
    if ( $observer_slug ) {
      $description = $wpdb->get_var( "SELECT description FROM {$wpdb->prefix}observer_lists WHERE slug = '{$observer_slug}' LIMIT 1" );
      if ( $description ) {
        return $description;
      }
    } else if ( $category_slug ) {
      $description = $wpdb->get_var( "SELECT description FROM {$wpdb->prefix}observer_categories WHERE slug = '{$category_slug}' LIMIT 1" );
      if ( $description ) {
        return $description;
      }
    }
    return $desc;
  }




}
