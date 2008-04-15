<dl>

	<dt>Video ID:</dt>
	<dd><?php echo $video->id; ?></dd>
	
	<dt>Title:</dt>
	<dd><?php echo $video->title; ?></dd>
	
	<dt>Link:</dt>
	<dd><a href="<?php echo $video->link; ?>"><?php echo $video->link; ?></a></dd>
	
	<dt>Author:</dt>
	<dd><?php echo $video->author; ?></dd>
	
	<dt>Content:</dt>
	<dd><?php echo $video->content; ?></dd>
	
	<dt>Categories:</dt>
	<dd>
		<?php
			foreach($video->categories as $category) {
				echo $category.', ';
			}
		?>
	</dd>
	
	<dt>Published:</dt>
	<dd><?php echo $video->published; ?></dd>
	
	<dt>Updated:</dt>
	<dd><?php echo $video->updated; ?></dd>
	
</dl>