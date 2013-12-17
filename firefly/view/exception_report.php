<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
                      "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>PHP Exception Caught</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

    <style>
        body {
            font-family: verdana, arial, helvetica, sans-serif;
            font-size: 14px;
        }

        a { color: #000; }
        a:visited { color: #666; }
        a:hover { color: #fff; background-color:#000; }
    </style>
    <?php Debugger :: start() ?>

</head>
<body>
<h2><?php echo $exception->getMessage(); ?></h2>
<p><?php echo "Exception throws from <b>" . $exception->getFile() . "(" . $exception->getLine() . "</b>)"; ?></p>

<div id="traces">
    <a href="#" onclick="javascript:(function(){var display=document.getElementById('exception_trace').style.display;document.getElementById('exception_trace').style.display=(display=='block'?'none':'block')})();">Full trace</a>
</div>
<div id="exception_trace" style="display: block; white-space:pre"><?php echo $exception->getTraceAsString(); ?></div>

<h3 style="margin-top: 20px">Request</h3>
<h4>Request parameters</h4>
<div>
    <a href="#" onclick="javascript:(function(){var display=document.getElementById('post_dump').style.display;document.getElementById('post_dump').style.display=(display=='block'?'none':'block')})();">Show $_POST dump</a>
    <br/>
    <div id="post_dump" style="display:none;">
        <?php new debugger($_POST); ?>
    </div>
    <a href="#" onclick="javascript:(function(){var display=document.getElementById('get_dump').style.display;document.getElementById('get_dump').style.display=(display=='block'?'none':'block')})();">Show $_GET dump</a>
    <div id="get_dump" style="display:none;">
        <?php unset($_GET['fireflypath']) ?>
        <?php new debugger($_GET); ?>
    </div>
</div>
<h4>Session and Cookie</h4>
<div>
    <?php
        if (isset($_SESSION)) {
    ?>
    <a href="#" onclick="javascript:(function(){var display=document.getElementById('session_dump').style.display;document.getElementById('session_dump').style.display=(display=='block'?'none':'block')})();">Show $_SESSION dump</a>
    <br/>
    <div id="session_dump" style="display:none;">
        <?php new debugger($_SESSION); ?>
    </div>
    <?php
        }
    ?>

    <a href="#" onclick="javascript:(function(){var display=document.getElementById('cookie_dump').style.display;document.getElementById('cookie_dump').style.display=(display=='block'?'none':'block')})();">Show $_COOKIE dump</a>
    <div id="cookie_dump" style="display:none;">
        <?php new debugger($_COOKIE); ?>
    </div>
</div>
</body>
</html>
