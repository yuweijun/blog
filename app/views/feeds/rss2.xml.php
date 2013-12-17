<?php
/**
 * RSS2 Feed Template for displaying RSS2 Posts feed.
 *
 * Based on WordPress feed-rss2.php.
 */
header('Content-Type: text/xml; charset=UTF-8', true);
echo '<' . '?xml version="1.0" encoding="UTF-8"?' . '>';
?>

<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	xmlns:slash="http://purl.org/rss/1.0/modules/slash/" >

<channel>
	<title>david.yu's blog</title>
	<atom:link href="http://www.phpfirefly.com/feeds/rss2" rel="self" type="application/rss+xml" />
	<link>http://www.phpfirefly.com</link>
	<description>{"select": "jQuery", "from": "javascript frameworks"}</description>
	<lastBuildDate><?php echo date('D, d M Y H:i:s +0000', $posts[0]->created_on); ?></lastBuildDate>
	<generator>http://www.phpfirefly.com</generator> 
	<language>en</language>
	<sy:updatePeriod>hourly</sy:updatePeriod>
	<sy:updateFrequency>1</sy:updateFrequency>

	<?php foreach ($posts as $post) { ?>
		
	<item>
		<title><?php echo $post->title ?></title>
		<link>http://www.phpfirefly.com/posts/show/<?php echo $post->id ?></link>
		<comments>http://www.phpfirefly.com/posts/show/<?php echo $post->id ?>#respond</comments>
		<pubDate><?php echo date('D, d M Y H:i:s +0000', $post->created_on); ?></pubDate>
		<dc:creator><?php echo $post->user->name ?></dc:creator>
		<category><![CDATA[blog]]></category> 

		<guid isPermaLink="false">http://www.phpfirefly.com/posts/show/<?php echo $post->id ?></guid>
		<description><![CDATA[<?php echo $post->title ?>]]></description>
		<content:encoded><![CDATA[<?php echo $post->content ?>]]></content:encoded>
		<wfw:commentRss><?php echo "http://www.phpfirefly.com/feeds/rss2/" . $post->id ?></wfw:commentRss>
		<slash:comments><?php echo sizeof($post->comments) ?></slash:comments>
	</item>
	
	<?php } ?>
	
</channel>
</rss>
