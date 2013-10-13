<div class="splash">
	<h1 class="brand"><a href="<?php echo @$logo_url; ?>" alt="<?php echo @$title ?>"><img src="<?php echo @$base; ?>/images/logo.png"></a></h1>
	<div class="contact">
		<?php foreach($addresses as $id => $address): ?>
		<section id="<?php echo $id; ?>" class="addresses">
			<h2><?php echo $address["name"]; ?></h2>
			<dl class="phone">
				<dt>Phone:</dt>
				<dd><span class="tel"><?php echo $address["phone"]; ?></span></dd>
			</dl>
			<dl class="address">
				<dt>Address:</dt>
				<dd><?php echo $address["address"]; ?></dd>
			</dl>
			<?php if (isset($address["open"]) && count($address["open"]) > 0): ?>
			<dl class="open">
				<dt>Open Hours:</dt>
				<?php foreach($address["open"] as $index => $open): ?>
				<dd<?php if($index > 0): ?> class="inset"<?php endif; ?>><?php echo $open; ?></dd>
				<?php endforeach; ?>
			</dl>
			<?php endif; ?>
		</section>
		<?php endforeach; ?>
		<?php if(isset($social) && count($social) > 0): ?>
		<section id="social" class="social">
			<ul>
			<?php foreach($social as $name => $url): ?>
				<li class="<?php echo @$name; ?>"><a href="<?php echo @$url; ?>"><img src="<?php echo @$base; ?>/images/<?php echo $name; ?>.png"></a></li>
			<?php endforeach; ?>
			</ul>
		</section
		<?php endif; ?>
	</div>
</div>