<style>
    .sub { padding-left: 25px; }
</style>
<?php
//wp_enqueue_script( 'td-newsletter', plugin_dir_url( __FILE__ ) . '/js/newsletter.js', array( 'jquery' ), '1.0', true );
//wp_enqueue_script( 'jquery-ui', get_template_directory_uri() . '/js/jquery-ui.js', array ( 'jquery' ), 1.0, true);
wp_enqueue_style( 'jquery-ui', get_template_directory_uri() . '/css/jquery-ui.css' );
?>
<script>
    jQuery(document).ready(function($) {
        $('#gig-date').datepicker( { dateFormat: 'yy-mm-dd' } );
    } );
</script>
<!--<div>
<label>
    <input type="checkbox" id="gig-all-day" name="gig[all_day]" value="1">
    All Day
</label>
</div>-->

<div class="">
    <label>
        Date 
        <input type="text" id="gig-date" name="gig[date]" value="<?php echo isset($gig) && isset( $gig['date'] ) ? $gig['date'] : ''; ?>" size="20" maxlength="30" readonly>
    </label>
    <small class="description">e.g. 2017-11-25 (yyyy-mm-dd)</small>
    <br>
    <label id="gig-time-wrap">
        Time
        <input class="" type="text" id="gig-time" name="gig[time]" value="<?php echo isset($gig) && isset( $gig['time'] ) ? $gig['time'] : ''; ?>" size="15" maxlength="10">
        <small class="description">e.g. 21:05 (HH:MM)</small>
    </label>
</div>
<br>
<div>
    <label>
    <input type="checkbox" id="gig-repeat" name="gig[repeat][settings]" value="1" <?php echo isset($rules) && isset($rules['FREQ']) ? 'checked="checked"' : '';?>>
    Repeat
    </label>
</div>
<div class="repeat_rules_wrap <?php echo isset($repeat) ? '' : 'hide'; ?>">
    <div class="sub">
        <label>
            Repeats
            <?php
                $freqs = array(
                    '',
                    'DAILY',
                    'WEEKLY',
//                    'MONTHLY',
//                    'YEARLY',
                );
            ?>
            <select id="gig-repeat-freq" name="gig[repeat][freq]">
                <?php foreach ( $freqs as $freq ): ?>
                    <option value="<?php echo $freq; ?>" <?php echo isset( $rules ) && isset( $rules['FREQ'] ) && $freq == $rules['FREQ'] ? ' selected="selected"': '';?>>
                        <?php echo ucfirst(strtolower($freq)); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
    </div>
    <div class="repeat_rule daily sub <?php echo isset($rules) && $rules['FREQ'] == 'DAILY' ? '' : (!isset($rules) ? '' : 'hide'); ?>">
        <label>Repeats every </label>
        <div class="sub">
            <div class="container-inline interval">
                <input type="radio" id="gig-repeat-rule-daily-byday-everyday" name="gig[repeat][rule][daily][byday]" value="INTERVAL" <?php echo isset( $repeat['daily']['byday'] ) && $repeat['daily']['byday'] == 'everyday' ? 'checked="checked"' : ''; ?>>
                <label>
                    <input placeholder="#" type="text" id="gig-repeat-rule-daily-interval" name="gig[repeat][rule][daily][INTERVAL]" value="<?php echo isset($rules) && isset( $rules['FREQ'] ) && $rules['FREQ'] == 'DAILY' ? $rules['INTERVAL'] : '1'; ?>" size="3" maxlength="3" class="form-text"> days
                </label>
            </div>
            <div>
                <label>
                    <input type="radio" id="gig-repeat-rule-daily-byday-every-weekday" name="gig[repeat][rule][daily][byday]" value="every_weekday" <?php echo isset( $repeat['daily']['byday'] ) && $repeat['daily']['byday'] == 'every_weekday' ? 'checked="checked"' : ''; ?>> Every weekday
                </label>
            </div>
            <div>
                <label>
                    <input type="radio" id="gig-repeat-rule-daily-byday-every-mo-we-fr" name="gig[repeat][rule][daily][byday]" value="every_mo_we_fr" <?php echo isset( $repeat['daily']['byday'] ) && $repeat['daily']['byday'] == 'every_mo_we_fr' ? 'checked="checked"' : ''; ?>> Every Mon, Wed, Fri
                </label>
            </div>
            <div>
                <label>
                <input type="radio" id="gig-repeat-rule-daily-byday-every-tu-th" name="gig[repeat][rule][daily][byday]" value="every_tu_th" <?php echo isset( $repeat['daily']['byday'] ) && $repeat['daily']['byday'] == 'every_tu_th' ? 'checked="checked"' : ''; ?>> Every Tue, Thu
                </label>
            </div>
        </div>
    </div>
    
    <div class="repeat_rule weekly sub <?php echo isset($rules) && $rules['FREQ'] == 'WEEKLY' ? '' : 'hide'; ?>">
        <div>
            <label>Repeats every </label>
            <label>
                <input placeholder="#" type="text" id="gig-repeat-rule-weekly-interval" name="gig[repeat][rule][weekly][INTERVAL]" value="<?php echo isset($rules) && isset( $rules['FREQ'] ) && $rules['FREQ'] == 'WEEKLY' ? $rules['INTERVAL'] : ''; ?>" size="3" maxlength="3">
                weeks
            </label>
            <br><br>
            <div class="hide">
                <label>Repeat on </label>
                <?php
                $days = array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun');
                foreach ($days as $day):
                ?>
                    <label>
                        <input type="checkbox" id="gig-repeat-rule-weekly-byday-<?php echo strtolower(substr($day, 0, 2)); ?>" name="gig[repeat][rule][weekly][BYDAY][<?php echo strtoupper(substr($day, 0, 2)); ?>" value="<?php echo strtoupper(substr($day, 0, 2)); ?>"<?php echo isset($repeat['weekly']['byday']) && in_array(strtoupper(substr($day, 0, 2)), $repeat['weekly']['byday']) ? 'checked="checked"' : ''; ?>>
                            <?php echo $day; ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <div class="repeat_rule monthly sub <?php echo isset($rules) && $rules['FREQ'] == 'MONTHLY' ? '' : 'hide'; ?>"">
        <div>
            <label>
                <input type="radio" id="gig-repeat-rule-monthly-day-month-bymonthday-bymonth" name="gig[repeat][rule][monthly][day_month]" value="BYMONTHDAY_BYMONTH" <?php echo isset($rules) && $rules['FREQ'] == 'MONTHLY' && $repeat['monthly']['day_month'] == 'BYMONTHDAY_BYMONTH' ? 'checked="checked"' : ''; ?>>
                On day 
                <select id="gig-repeat-rule-monthly-bymonthday-bymonth-child-bymonthday" name="gig[repeat][rule][monthly][BYMONTHDAY_BYMONTH_child][BYMONTHDAY]">
                <?php foreach(range(1,31) as $d) : ?>
                    <option value="<?php echo $d; ?>">
                        <?php echo $d; ?>
                    </option>
                <?php endforeach; ?>
                </select>
            </label>
            of
            <div class="sub">
                <?php
                    $months = array(
                        1 => 'Jan',
                        2 => 'Feb',
                        3 => 'Mar',
                        4 => 'Apr',
                        5 => 'May',
                        6 => 'Jun',
                        7 => 'Jul',
                        8 => 'Aug',
                        9 => 'Sep',
                        10 => 'Oct',
                        11 => 'Nov',
                        12 => 'Dec'
                    );
                    foreach ($months as $month_number => $month_name):
                ?>
                <label style="width: 15%; display: inline-block;">
                    <input type="checkbox" id="gig-repeat-rule-monthly-bymonthday-bymonth-child-bymonth-<?php echo $month_number; ?>" name="gig[repeat][rule][monthly][BYMONTHDAY_BYMONTH_child][BYMONTH][<?php echo $month_number; ?>]" value="<?php echo $month_number; ?>"> <?php echo $month_name; ?>
                </label>
                <?php echo $month_number == 6 ? '<br>' : ''; ?>
                <?php endforeach; ?>
            </div>
        </div>
        <br>
        <div>
            <input type="radio" id="gig-repeat-rule-monthly-day-month-byday-bymonth" name="gig[repeat][rule][monthly][day_month]" value="BYDAY_BYMONTH" <?php echo isset($rules) && $rules['FREQ'] == 'MONTHLY' && $repeat['monthly']['day_month'] == 'BYDAY_BYMONTH' ? 'checked="checked"' : ''; ?>>
            <label>On the </label>
            <?php
                $rule_byday_counts = array(
                    '+1' => 'First',
                    '+2' => 'Second',
                    '+3' => 'Third',
                    '+4' => 'Fourth',
                    '+5' => 'Fifth',
                    '-1' => 'Last',
                    '-2' => 'Next to last',
                    '-3' => 'Third from last',
                    '-4' => 'Fourth from last',
                    '-5' => 'Fifth from last',
                );
            ?>
            <select id="gig-repeat-rule-monthly-byday-bymonth-child-byday-count" name="gig[repeat][rule][monthly][BYDAY_BYMONTH_child][BYDAY_COUNT]">
                <?php foreach ( $rule_byday_counts as $key => $value ): ?>
                    <option value="<?php echo $key; ?>" <?php echo isset($rules) && $rules['FREQ'] == 'MONTHLY' && $repeat['monthly']['day_month'] == 'BYDAY_BYMONTH' && $repeat['rule_byday_count'] == $key ? 'selected="selected"' : ''; ?>>
                        <?php echo $value; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <?php
                $rule_byday_days = array(
                    'SU' => 'Sunday',
                    'MO' => 'Monday',
                    'TU' => 'Tuesday',
                    'WE' => 'Wednesday',
                    'TH' => 'Thursday',
                    'FR' => 'Friday',
                    'SA' => 'Saturday',
                );
            ?>
            <select id="gig-repeat-rule-monthly-byday-bymonth-child-byday-day" name="gig[repeat][rule][monthly][BYDAY_BYMONTH_child][BYDAY_DAY]">    
                <?php foreach ( $rule_byday_days as $key => $value ): ?>
                    <option value="<?php echo $key; ?>" <?php echo isset($rules) && $rules['FREQ'] == 'MONTHLY' && $repeat['monthly']['day_month'] == 'BYDAY_BYMONTH' && $repeat['rule_byday_day'] == $key ? 'selected="selected"' : ''; ?>>
                        <?php echo $value; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <label> of </label>
        </div>
        
        <div class="sub">
            <?php
                foreach ($months as $month_number => $month_name):
            ?>
            <label style="width: 15%; display: inline-block;">
                <input type="checkbox" id="gig-repeat-rule-monthly-byday-bymonth-child-bymonth-<?php echo $month_number; ?>" name="gig[repeat][rule][monthly][monthly][BYDAY_BYMONTH_child][BYMONTH][<?php echo $month_number; ?>]" value="<?php echo $month_number; ?>" <?php echo isset($rules) && $rules['FREQ'] == 'MONTHLY' && $repeat['monthly']['day_month'] == 'BYDAY_BYMONTH' && in_array($month_number, $repeat['months']) ? 'checked="checked"' : ''; ?>>
                    <?php echo $month_name; ?>
            </label>
            <?php echo $month_number == 6 ? '<br>' : ''; ?>
            <?php endforeach; ?>
        </div>
    </div>
    
    <div class="repeat_rule yearly sub <?php echo isset($rules) && $rules['FREQ'] == 'YEARLY' ? '' : 'hide'; ?>"">
        <div>
            <div>
                <div>
                    Repeats Every <input placeholder="#" type="text" id="gig-repeat-rule-yearly-interval" name="gig[repeat][rule][yearly][INTERVAL]" value="1" size="3" maxlength="3"> years
                </div>
            </div>
            <div>
                <div>   
                    <div>
                        <label>
                            <input type="radio" id="gig-repeat-rule-yearly-day-month-bymonthday-bymonth" name="gig[repeat][rule][yearly][day_month]" value="BYMONTHDAY_BYMONTH">
                            On day
                            <select id="gig-repeat-rule-yearly-bymonthday-bymonth-child-bymonthday" name="gig[repeat][rule][yearly][BYMONTHDAY_BYMONTH_child][BYMONTHDAY]">
                            <?php foreach(range(1,31) as $d) : ?>
                                <option value="<?php echo $d; ?>"><?php echo $d; ?></option>
                            <?php endforeach; ?>
                            </select>
                        </label>
                        of
                        <br>
                        <div class="sub">
                            <?php
                            foreach ($months as $month_number => $month_name):
                            ?>
                            <label style="width: 15%; display: inline-block;">
                                <input type="checkbox" id="gig-repeat-rule-yearly-bymonthday-bymonth-child-bymonth-<?php echo $month_number; ?>" name="gig[repeat][rule][yearly][BYMONTHDAY_BYMONTH_child][BYMONTH][<?php echo $month_number; ?>]" value="<?php echo $month_number; ?>"> <?php echo $month_name; ?>
                            </label>
                            <?php echo $month_number == 6 ? '<br>' : ''; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <br>
                <div>
                    <input type="radio" id="gig-repeat-rule-yearly-day-month-byday-bymonth" name="gig[repeat][rule][yearly][day_month]" value="BYDAY_BYMONTH">
                    On the
                    <select id="gig-repeat-rule-yearly-byday-bymonth-child-byday-count" name="gig[repeat][rule][yearly][BYDAY_BYMONTH_child][BYDAY_COUNT]">
                        <option value="+1">First</option>
                        <option value="+2">Second</option>
                        <option value="+3">Third</option>
                        <option value="+4">Fourth</option>
                        <option value="+5">Fifth</option>
                        <option value="-1">Last</option>
                        <option value="-2">Next to last</option>
                        <option value="-3">Third from last</option>
                        <option value="-4">Fourth from last</option>
                        <option value="-5">Fifth from last</option>
                    </select>
                    <select id="gig-repeat-rule-yearly-byday-bymonth-child-byday-day" name="gig[repeat][rule][yearly][BYDAY_BYMONTH_child][BYDAY_DAY]">
                        <option value="SU">Sunday</option>
                        <option value="MO">Monday</option>
                        <option value="TU">Tuesday</option>
                        <option value="WE">Wednesday</option>
                        <option value="TH">Thursday</option>
                        <option value="FR">Friday</option>
                        <option value="SA">Saturday</option>
                    </select>
                    of
                    <div class="sub">
                        <?php
                        foreach ($months as $month_number => $month_name):
                        ?>
                        <label style="width: 15%; display: inline-block;">
                            <input type="checkbox" id="gig-repeat-rule-yearly-byday-bymonth-child-bymonth-<?php echo $month_number; ?>" name="gig[repeat][rule][yearly][BYDAY_BYMONTH_child][BYMONTH][<?php echo $month_number; ?>]" value="<?php echo $month_number; ?>"> <?php echo $month_name; ?>
                        </label>
                        <?php echo $month_number == 6 ? '<br>' : ''; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="sub">
        
        <h4>Stop repeating </h4>
        <div>
            <div>
                <label>
                    <input type="radio" id="gig-repeat-rule-range-of-repeat-count" name="gig[repeat][rule][range_of_repeat]" value="COUNT"<?php echo isset($repeat) && $repeat['range_of_repeat'] == 'COUNT' ? 'checked="checked"' : '';?>>
                
                    After
                    <input placeholder="#" type="text" id="gig-repeat-rule-count-child" name="gig[repeat][rule][count_child]" value="<?php echo isset($rules) && isset($rules['COUNT']) ? $rules['COUNT'] : ''; ?>" size="10" maxlength="10" class="form-text">
                    occurrences
                </label>
            </div>
            <div class="hide">
                <label>
                    <input type="radio" id="gig-repeat-rule-range-of-repeat-until" name="gig[repeat][rule][range_of_repeat]" value="UNTIL"<?php echo isset($repeat) && $repeat['range_of_repeat'] == 'UNTIL' ? 'checked="checked"' : '';?>>                
                    On Date
                    <input type="text" id="gig-repeat-rule-until" name="gig[repeat][rule][until]" value="<?php echo isset($rules) && isset($rules['UNTIL']) ? $rules['UNTIL'] : ''; ?>" size="20" maxlength="30" class="form-text">
                    <small class="description"> E.g. 2017-04-07</small>
                </label>
            </div>
        </div>
    </div>
</div>

<script>
    jQuery(document).ready(function($) {
        $('select[name="gig[repeat][freq]"]').on('change', function() {
            selected = $(this).val().toLowerCase();
            $('.repeat_rule').addClass('hide');
            $('.' + selected).removeClass('hide');
        });
        $('input[name="gig[repeat][settings]"').on('change', function() {
            if( ! $(this).prop('checked')) {
                $('.repeat_rules_wrap').addClass('hide');
            } else {
                $('.repeat_rules_wrap').removeClass('hide');
            }
        });
        $('input[name="gig[all_day]"').on('change', function() {
            if( $(this).prop('checked')) {
                $('#gig-time-wrap').addClass('hide');
                $('#gig-time-wrap').find('input').val('');
            } else {
                $('#gig-time-wrap').removeClass('hide');
            }
        });
    });
</script>