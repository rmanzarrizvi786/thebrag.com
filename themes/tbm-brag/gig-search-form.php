<?php
wp_enqueue_script('bs-dp', get_template_directory_uri() . '/bs/dp/js/bootstrap-datepicker.min.js', array('jquery'), NULL, true);

$in_sidebar = isset($in_sidebar) ? $in_sidebar : false;

$city_state_map = array(
    'sydney' => 'NSW',
    'melbourne' => 'VIC',
    'brisbane' => 'QLD',
    'perth' => 'WA',
    'adelaide' => 'SA',
    'canberra' => 'ACT',
    'darwin' => 'NT',
    'hobart' => 'TAS',
);
?>
<link rel="stylesheet" id="bs-dp-css" href="<?php echo get_template_directory_uri(); ?>/bs/dp/css/bootstrap-datepicker.min.css" type="text/css" media="all" />
<div class="form p-3 bg-light mb-3">
    <!--<h2>Gig Search</h2>-->
    <form action="/gigs/" method="get">
        <div class="row text-white">
            <label class="col-12 <?php echo $in_sidebar ? '' : 'col-md-3'; ?>">
                Gig title
                <input type="text" id="title" name="title" class="form-control" value="<?php echo isset($query_title) ? $query_title : ''; ?>" size="30" maxlength="128">
            </label>
            <label class="col-12 <?php echo $in_sidebar ? '' : 'col-md-3'; ?>">
                Artist
                <input type="text" id="artist" name="artist" class="form-control" value="<?php echo isset($query_artist) ? $query_artist : ''; ?>" size="30" maxlength="128">
            </label>
            <label class="col-12 <?php echo $in_sidebar ? '' : 'col-md-2'; ?>">
                Date
                <input type="text" id="gig_date" name="gig_date" class="form-control datepicker" readonly value="<?php echo isset($query_date) ? $query_date : ''; ?>" size="15" maxlength="10">
            </label>
            <label class="col-12 <?php echo $in_sidebar ? '' : 'col-md-2'; ?>">
                City
                <?php if (isset($city_state_map)) : ?>
                    <select name="city" class="form-control">
                        <?php foreach ($city_state_map as $the_city => $state) : ?>
                            <option value="<?php echo $the_city; ?>" <?php echo isset($query_city) && $query_city == $the_city ? 'selected="selected"' : ''; ?>><?php echo ucfirst($the_city); ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php endif; ?>
            </label>
            <label class="col-12 <?php echo $in_sidebar ? '' : 'col-md-2'; ?> mt-4">
                <input name="search" type="submit" value="Search" class="btn btn-primary d-sm-block">
            </label>
        </div>
    </form>
</div>