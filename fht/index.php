
<?php 
require_once('classes/template.class.php'); //require the template parsing class
require_once('classes/db_mysql.class.php'); //require the database class

// create a database connection
$db_mysql = new db_mysql;
$db_mysql->connect();

$query = "SELECT * FROM home_content";
$query_news = "SELECT * FROM news WHERE site LIKE '%6%'  OR site LIKE '%1%' ORDER BY news_date DESC LIMIT 6";
$fields = $db_mysql->query_first($query);
$news = $db_mysql->query($query_news);

$keywords = $fields['home_content_keywords'];
$desc = 'About CraigsPlates.com';
$title = $fields['home_content_title'];
$content = $fields['home_content_text'];
$heading = $fields['home_content_heading'];
if (isset($_GET['a_aid'])) {
	if ($_GET['a_aid'] != '') {
		$heading .='<img src="http://www.craigsplates.co.uk/affiliate/scripts/t2.php?a_aid=' . 
		$_GET['a_aid'] . '&a_bid=' . $_GET['a_bid'] . '&referrer=' . urlencode($_SERVER['HTTP_REFERER']) . 
		'" border="0" width="1" height="1">';
	}
}
	
$db_mysql->close(); //close the connection

$content .= '<div id="news"><h2>News</h2><div id="newstext">';

$sequence = 0;

while ($row = $db_mysql->fetch_array($news)) {
	$sequence++;
	$content .= '<h3 class="newsheading">
				<a href="news.php?news_id=' . $sequence. '">' . $row['news_heading'] . '</a>
			</h3>
			<h4>Posted: ' . date('F d, Y', strtotime($row['news_date'])). '<br />
			<span class="story"><a href="news.php?news_id=' . $sequence . '">Full Story...</a></span></h4>';
}
$content .= '</div></div>';


$page = new Template('templates/page.inc.html'); //make a new instance of the Template class

$page->SetParameter('HEADING', $heading);
$page->SetParameter('DESCRIPTION', $title);
$page->SetParameter('KEYWORDS', $keywords);
$page->SetParameter('TITLE', $title);
$page->SetParameter('CONTENT', $content);
$page->SetParameter('REEL', file_get_contents('reel.html'));

$renderPage = $page->CreatePage(); 
echo $renderPage;//send the page to the browser
unset($page); //delete the template object from memory

?>