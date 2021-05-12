<?php
if ( ! get_field('paid_content') ) :
  if ( 'mobile' == $device_type ) :
?>
<div class="text-center sticky-ad-bottom py-2 d-md-none bg-white">
  <!-- 71161633/SSM_thebrag/tb_header_mobile --><div data-fuse="21873375741"></div>
</div>
<?php else : // For Non-mobile ?>
  <div class="text-center sticky-ad-bottom d-none d-md-block">
    <!-- 71161633/SSM_thebrag/tb_home_hrec_1 --><div data-fuse="21718737332"></div>
  </div>
<?php endif; // If Mobile / Desktop
endif; // If it's not Paid Content
