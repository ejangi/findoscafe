<?php $me = json_decode(@$me); ?>
<article class="card default">
	<section class="card">
		<?php if(isset($me->name)): ?>
		<h1><?php echo $me->name; ?></h1>
		<?php endif; ?>
		<?php if(isset($me->cover->source)): ?>
		<div class="cover"><img src="<?php echo $me->cover->source; ?>"<?php if(isset($me->cover->offset_x)): ?> data-offset-x="<?php echo $me->cover->offset_x; ?>"<?php endif; ?><?php if(isset($me->cover->offset_y)): ?> data-offset-y="<?php echo $me->cover->offset_y; ?>"<?php endif; ?><?php if(isset($me->cover->offset_y) || isset($me->cover->offset_x)): ?> style="<?php if(isset($me->cover->offset_x)): ?>margin-left: -<?php echo $me->cover->offset_x; ?>px;<?php endif; ?><?php if(isset($me->cover->offset_y)): ?>margin-top: -<?php echo $me->cover->offset_y; ?>px;<?php endif; ?>"<?php endif; ?>></div>
		<?php endif; ?>
		<?php if(isset($me->picture->data->url)): ?>
		<div class="picture"><img src="<?php echo $me->picture->data->url; ?>"></div>
		<?php endif; ?>
		<?php if(isset($me->work) || isset($me->email) || isset($me->link) || isset($me->location)): ?>
		<div class="details">
			<?php if(isset($me->location->name)): ?>
			<p class="location"><span class="address"><?php echo $me->location->name; ?></span>
			<?php endif; ?>
			<?php if(isset($me->work) && is_array($me->work) && count($me->work) > 0): ?>
			<p class="work"><span class="position"><?php echo $me->work[0]->position->name; ?></span><span class="at"> at </span><span class="employer"><?php echo $me->work[0]->employer->name; ?></span></p>
			<?php endif; ?>
			<?php if(isset($me->email)): ?>
			<p class="email"><span class="address"><a href="mailto:<?php echo $me->email; ?>"><?php echo $me->email; ?></a></span>
			<?php endif; ?>
		</div>
		<?php endif; ?>
	</section>
	<?php if(isset($me->bio)): ?>
	<section class="timeline">
		<span class="node">&#8226;</span>
	</section>
	<section class="bio">
		<h2>About</h2>
		<div class="bio"><?php echo simple_format($me->bio); ?></div>
	</section>
	<?php endif; ?>
</article>
<?php if(false): ?>
<pre><?php echo print_r($me, true); ?></pre>
<?php endif; ?>