<!DOCTYPE html>
<html lang="fr">
<head>
<title>emailPoubelle - Template-exemple</title>
<meta charset="UTF-8">
<meta name="description" content="">
<meta name="robots" content="index,follow,noarchive">
<link rel="stylesheet" href="template-exemple/style.css">
<!--[if IE]><script src="template-exemple/html5-ie.js"></script><![endif]--> 
</head>
<body>
<!--[if lte IE 6]> 
<div class="alert-ie6" style="padding: 1em; background: #900; font-size: 1.1em; color: #fff;">
      	<p><strong>Attention ! </strong> Votre navigateur (Internet Explorer 6 ou 7) présente de sérieuses lacunes en terme de sécurité et de performances, dues à son obsolescence (il date de 2001).<br />En conséquence, ce site sera consultable mais de manière moins optimale qu'avec un navigateur récent (<a href="http://www.browserforthebetter.com/download.html" style="color: #fff;">Internet Explorer 8</a>, <a href="http://www.mozilla-europe.org/fr/firefox/" style="color: #fff;">Firefox 3</a>, <a href="http://www.google.com/chrome?hl=fr" style="color: #fff;">Chrome</a>, <a href="http://www.apple.com/fr/safari/download/" style="color: #fff;">Safari</a>,...)</p>
      </div>
<![endif]-->  
	<div id="a">
		<header>
			<a href="/" title="Startseite"><strong>OWMX-2</strong> HTML5 &amp; CSS3</a>
		</header>
		<div id="b">
			<article>
				<h1>Emails poubelle libre</h1>
				<p>Générer des emails poubelle sans contrainte de durée de vie. </p>
				<?php 
				// Intégration dans votre site :
				if (file_exists('../conf.php')) {
					include('../conf.php');
				} else {
					include('../conf-dist.php');
				}
				include('../emailPoubelle.php'); 
				?>
				<h3>Let the Show Begin - Pre and Code</h3>
				<p>This is how it looks if you use <code>pre</code> and <code>code</code> together, for example to highlight and nicely markup a piece of code:</p>
				<pre><code>pre {<br /> font-size : 12px;<br /> background : #F0F0F0;<br />}</code></pre>
				<h3>Example Blockquote</h3>
				<p>If you want to quote somebody, you can use this perfectly semantic example for a blockquote:</p>
				<cite>Jonas Jacek</cite>
				<blockquote cite="http://www.rield.com/">
				During my years in the Internet Marketing business I have seen and done many things I never thought would be of interest to me or anyone else.
				</blockquote>
				<h3>Examples Alerts, Notice  &amp; Confirmation</h3>
				<p>These sample styles for alerts and notices are useful if you want to use the template in content management systems.</p>
				<p class="highlight-1"><strong>Alert:</strong> This is how an alert looks like.</p>
				<p class="highlight-2"><strong>Notice:</strong> This is how a notice looks like.</p>
				<p class="highlight-3"><strong>Confirmation:</strong> This is how a confirmation looks like.</p>
				<h3>Example Table</h3>
				<p>The following is the design for a table. The style is simple and user-friendly. Some of the effects were made with CSS3.</p>
				<table>
					<caption>Mini HTML5 Reference Guide</caption>
					<tbody>
							<tr>
								<th>Tag</th>
								<th>Info</th>
								<th>Attributes</th>
							</tr>
						    	<tr>
								<td>&lt;abbr&gt;</td>
								<td>abbreviation</td>
								<td>global attributes**</td>
						    	</tr>
						    	<tr>
								<td>&lt;area&gt;</td>
								<td>in an image map</td>
								<td> alt, coords, href, hreflang, media, ping, rel, shape, target, type</td>
						    	</tr>
						    	<tr>
								<td>&lt;article&gt;</td>
								<td>article/ content</td>
								<td>global attributes**</td>
						    	</tr>
						    	<tr>
								<td>&lt;aside&gt;</td>
								<td>sidebar</td>
								<td>global attributes**</td>
						    	</tr>
						    	<tr>
								<td>&lt;audio&gt;</td>
								<td>sound content</td>
								<td>autobuffer, autoplay, controls, loop, src</td>
						    	</tr>
						    	<tr>
								<td>&lt;b&gt;</td>
								<td>bold text</td>
								<td>global attributes**</td>
							</tr>
						</tbody>
				</table>
				<h3>Example hCalendar</h3>
				<p>The following is a definition list in combination with the hCalendar microformat. </p>
				<dl class="vevent">
					<dt class="dtstart"><abbr title="2010-11-18" class="dtstart">11-18-2010</abbr></dt>
					<dd class="summary">Conference Name</dd>
					<dd><a href="#" class="url">http://www.conference-website.com/</a></dd>
					<dd class="description">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi nec eleifend diam. Fusce lobortis odio ac sem scelerisque sed iaculis purus ornare.</dd>
				</dl>
				<h3>Example Video</h3>
				<p>You can put your Video-Files here...</p>
				<video src="movie.ogg" controls>your browser does not support the video tag</video>
				<h3>Example Audio</h3>
				<p>You can put your Audio-Files here...</p>
				<audio src="file.ogg" controls>your browser does not support the audio tag</audio>
				<h3>Example Lists</h3>
				<p>Two different kinds of lists were styled: Ordered lists (ol) and unordered (ul) lists.</p>
				<ol>
					<li>This is</li>
					<li>The Ordered</li>
					<li>Listing</li>
				</ol>
				<ul>
					<li>This is</li>
					<li>The Unordered</li>
					<li>Listing</li>
				</ul>
				<h3>Example Form</h3>
				<p>This is how a form will look like in this template.</p>
				<form action="#">
					<label>Name</label>
					<input name="#" type="text" value="Your Name">
					<label>Email</label>
					<input name="#" type="text" value="Your Email">
					<label>Your Comments</label>
					<textarea>This is a Comment...</textarea>
					<br>
					<input class="button" type="submit">
				</form>
				<section class="meta">
				<p>Author: <a href="http://www.jabz.info/contact/jonas-jared-jacek/">Jonas Jacek</a> | Date: 2010-01-21 | Comments: <a href="#">7</a></p>
				<p>Tags: <a href="#" rel="tag">quisquam</a>, <a href="#" rel="tag">Tags</a>, <a href="#" rel="tag">dolorem</a> <a href="#" rel="tag">Tags</a>, <a href="#" rel="tag">Tags</a></p>
				</section>
			</article>
			<aside>
			        <h4>Search</h4>
				<form action="#" class="s">
					<input name="search" type="text" value="Type term and hit enter...">
				</form>
				<h4>About the Author</h4>
				<div id="c">
					<img src="template-exemple/jonas-jacek.png" alt="Jonas Jacek">
					<p>Hi, my name is <a href="http://www.jabz.info/contact/jonas-jared-jacek/">Jonas Jacek</a>.<br> Welcome to my new HTML5 / CSS3 blog.</p>
					<p>I am a web designer, web developer and Internet marketing enthusiast.</p>
					<p>I strongly believe in open-source software and open standards.</p>
				</div>
				<nav>
					<h4>Navigation</h4>
					<ul>
						<li><a href="#">Lorem</a></li>
						<li><a href="#">Ipsum</a></li>
						<li class="active"><a href="#">Dolor Sit</a>
							<ul>
								<li><a href="#">Amet</a></li>
								<li><a href="#">Amet</a></li>
							</ul>
					
						</li>
						<li><a href="#">Downloads</a></li>
						<li><a href="#">Contact</a></li>
					</ul>
				</nav>
				<h4>Example Gallery</h4>
				<ul class="gallery">
					<li><a href="index.html"><img src="template-exemple/img1.png"/></a></li>
					<li><a href="index.html"><img src="template-exemple/img2.png"/></a></li>
					<li><a href="index.html"><img src="template-exemple/img3.png"/></a></li>
				</ul>
				<ul class="gallery">
					<li><a href="index.html"><img src="template-exemple/img4.png"/></a></li>
					<li><a href="index.html"><img src="template-exemple/img5.png"/></a></li>
					<li><a href="index.html"><img src="template-exemple/img6.png"/></a></li>
				</ul>
				<h4>AdSpace</h4> 
				<div class="adspace"> 
					<a href="http://www.jabz.biz/" rel="me"><img src="template-exemple/jabz-logo.png" alt="Jabz Internet Marketing GmbH" title="Jabz Internet Marketing GmbH"/></a> 
				</div>
				<h4>Example Blogroll</h4>
				<ul>
					<li><a href="http://www.jabz.biz/">Jabz Internet Marketing GmbH</a></li>
					<li><a href="http://www.jabz.info/contact/jonas-jared-jacek/">Jonas Jacek</a></li>
					<li><a href="http://www.w3.org/">World Wide Web Consortium</a></li>
					<li><a href="http://www.getfirefox.com/">Firefox Web Browser</a></li>
				</ul> 
				<h4>Example Tag Cloud</h4>
				<ul id="tagcloud">
					<li class="tagcloudsize-1"><a href="#">Lorem</a></li>
					<li class="tagcloudsize-2"><a href="#">Ipsum</a></li>
					<li class="tagcloudsize-3"><a href="#">Dolor</a></li>
					<li class="tagcloudsize-2"><a href="#">Sit Amet</a></li>
					<li class="tagcloudsize-1"><a href="#">Consectetur</a></li>
				</ul> 
			</aside>
		</div>
		<footer>
			<p>Template Design &amp; Markup by <a href="http://www.jabz.info/contact/jonas-jared-jacek/" title="Profile of Jonas Jacek">Jonas Jacek</a> for <a href="http://www.owmx.com/" title="Free HTML5 &amp; CSS3 Web Template | owmx.com">Free HTML5 &amp; CSS3 Web Templates</a>.</p>
		</footer>
	</div>
</html>
