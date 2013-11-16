
--------------------------------------------------------------------------------

THIS TEMPLATE IS NO LONGER USED!

This template has been left here for historical purposes. If you want to modify
the look of a single Ping.fm status update, create a new file in your active
theme directory called 'single-pingfm.php'. At that point, you'll be using the
standard WordPress template tags and NOT the ones used below. However, you're
more than welcome to use the HTML below as a starting point for designing your
new template.

If you want to learn more about theming WordPress, check out their excellent
documentation on the subject:

http://codex.wordpress.org/Theme_Development

--------------------------------------------------------------------------------

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php bloginfo('name'); ?>: Status Update (<?php wp_pingfm_single_date(); ?>)</title>
<style type="text/css">
body {
    background: #fff;
    font-family: Cambria, Georgia, Times, Times New Roman, serif;
    margin: 0;
    padding: 100px 60px 0 60px;
}
#container {
    border: 1px solid #ccc;
    margin: 0 auto;
    padding: 50px;
    width: 500px;
}
#status-text {
    color: #000;
    font-size: 22px;
    font-weight: bold;
    line-height: 30px;
    margin-bottom: 25px;
}
#status-date {
    color: #333;
    font-size: 16px;
}
</style>
</head>

<body>
<div id="container">
  <div id="status-text"><?php wp_pingfm_single(); ?></div>
  <div id="status-date"><?php wp_pingfm_single_date(); ?></div>
</div>
</body>
</html>
