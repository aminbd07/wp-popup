<?php 
$agr = array(
    'post_type' => $this->custom_post_name 
) ; 
$posts = get_posts($agr) ?>

<h1 class="wp-heading-inline">WP Popup </h1>

<a href="<?php echo admin_url() ?>post-new.php?post_type=wpob-popup" class="page-title-action">Add New</a>
<hr class="wp-header-end">

<table class="table table-border" >
<thead>
  <tr>
   <th>Title</th>
   <th>Date</th>
   <th>Short Code</th>
   <th></th>
  </tr>
</thead>
<tbody>
<?php foreach($posts as $post){?>
<tr>
<td><?php echo $post->post_title?></td><td><?php echo $post->post_content ?></td>
</tr>
<?php } ?>
</tbody>

 