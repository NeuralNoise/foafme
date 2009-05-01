<?
// Include the SimplePie library, and the one that handles internationalized domain names.
require_once('simplepie/1.1.3/simplepie.inc');
require_once('simplepie/1.1.3/idn/idna_convert.class.php');
require_once('lib/libAuthentication.php');

$auth = $_SESSION['auth'];

if (isset($_REQUEST['webid'])) {
  $auth = get_agent($_REQUEST['webid']);
}

// Initialize some feeds for use.
$feed = new SimplePie();

$a1 = replace_with_rss($auth['agent']['holdsAccount']);
$a2 = replace_with_rss($auth['agent']['accountProfilePage']);

if ( $a1 || $a2 ) {
  $feed->set_feed_url(array_merge(  $a1?$a1:array(), $a2?$a2:array() ));
} else {
  $feed->set_feed_url( "http://example.com" );
}

// When we set these, we need to make sure that the handler_image.php file is also trying to read from the same cache directory that we are.
$feed->set_favicon_handler('./handler_image.php');
$feed->set_image_handler('./handler_image.php');

// Initialize the feed.
$feed->init();

// Make sure the page is being served with the UTF-8 headers.
$feed->handle_content_type();

// Begin the (X)HTML page.
?>

			


	<?php if ($feed->error): ?>
		<p>No Activity Discovered Yet...</p>
	<?php endif ?>


	<?php
	// Let's loop through each item in the feed.
	foreach($feed->get_items() as $item):

	// Let's give ourselves a reference to the parent $feed object for this particular item.
	$feed = $item->get_feed();
	?>

		<div class="chunk">
			<img src="<?php echo $feed->get_favicon(); ?>" width="16" height="16" class="activity-favicon" alt="[icon]" />
			<h4><a href="<?php echo $item->get_permalink(); ?>"><?php echo html_entity_decode($item->get_title(), ENT_QUOTES, 'UTF-8'); ?></a></h4>
			<!-- get_content() prefers full content over summaries -->
			<?php echo $item->get_content(); ?>

			<?php if ($enclosure = $item->get_enclosure()): ?>
				<div>
				<?php echo $enclosure->native_embed(array(
					// New 'mediaplayer' attribute shows off Flash-based MP3 and FLV playback.
					'mediaplayer' => '../demo/for_the_demo/mediaplayer.swf'
				)); ?>
				</div>
			<?php endif; ?>

			<p class="footnote">Source: <a href="<?php echo $feed->get_permalink(); ?>"><?php echo $feed->get_title(); ?></a> | <?php echo $item->get_date('j M Y | g:i a'); ?></p>
		</div>

	<?php endforeach ?>


