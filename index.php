<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='en' lang='en'>
<head>
<meta http-equiv='content-type' content='text/html; charset=utf-8' />
<meta name='MSSmartTagsPreventParsing' content='TRUE' />
<?php
$base = preg_replace(':.*/:', '', dirname($_SERVER['PHP_SELF']));
echo("<title>Index of $base</title>");
?>
<style type='text/css'>
body { font-family:sans-serif; }
.grey0 { background-color:#ddd; }
.grey1 { background-color:#bbb; }
.title { background-color:#eee; }
.footer { border-top:1px solid black; background-color:#eee; font-size:small; text-align:right; }
h1 { text-align:center; }
table { margin:0px; border:0px; padding:2px; border-spacing:0px; border:1px dashed #000; margin-left:auto; margin-right:auto; }
td { padding:.3em; }
a { text-decoration:none; }
a:hover { text-decoration:underline; }
.s0, .s1 { text-align:right; }
<?php
// generate style for order column hilighting
// sort column and order. Assume (n)ame and (a)scending if unset.
$sort = "n";
$order = "a";
if(array_key_exists('s', $_GET))
    $sort = $_GET['s'];
if($sort != "n" && $sort != "d" && $sort != "s")
    $sort = "n";
if(array_key_exists('o', $_GET))
    $order = $_GET['o'];
if($order != "a" && $order != "d")
    $order = "a";
echo("." . $sort . "0 { background-color:#ccc; }\n");
echo("." . $sort . "1 { background-color:#aaa; }\n");
?>
</style>
</head>
<body>

<?php
echo("<h1>Index of $base</h1>");

date_default_timezone_set("Europe/Berlin");

$files = glob("*", GLOB_MARK);

// create list of items.
foreach($files as $item)
{
    // hide ourselves.
    if($item == basename($_SERVER['PHP_SELF']))
        continue;
    if($item == "index.txt")
        continue;
    $item_size = filesize($item);
    $item_date = date("Y-m-d H:i:s O", filemtime($item));
    $item_name = $item;

    if($sort == "n")
        $index = $item_name;
    if($sort == "d")
        $index = $item_date;
    if($sort == "s")
        $index = $item_size;

    $arr[$index] = implode('\t', array($item_name, $item_size, $item_date));
}
// sort items
if(count($arr) > 0) {
    if($order == "d") {
        krsort($arr);
        $o = 'a';
    }
    else {
        ksort($arr);
        $o = 'd';
    }
}

// display table header
echo("<table>\n");
$g = 0;
echo("<tr class='title'>");
echo("<td>Name <a href='$_SERVER[PHP_SELF]?s=n&amp;o=a'>↑</a>"
    ."<a href='$_SERVER[PHP_SELF]?s=n&amp;o=d'>↓</a></td>");
echo("<td>Size <a href='$_SERVER[PHP_SELF]?s=s&amp;o=a'>↑</a>"
    ."<a href='$_SERVER[PHP_SELF]?s=s&amp;o=d'>↓</a></td>");
echo("<td>Date <a href='$_SERVER[PHP_SELF]?s=d&amp;o=a'>↑</a>"
    ."<a href='$_SERVER[PHP_SELF]?s=d&amp;o=d'>↓</a></td>");
echo("</tr>\n");
// display items.
$totalsize = 0;
if(count($arr) > 0) {
    foreach($arr as $item) {
        list($n, $s, $d) = explode('\t', $item);
        $totalsize += $s;
        if($s > 1024) {
            $s = (int) ($s / 1024);
            $u = "kiB";
        }
        else {
            $u = "B";
        }
        echo("<tr class='grey$g'>");
        echo("<td class='n$g'><a href='$n'>$n</a></td>");
        echo("<td class='s$g'>$s $u</td>");
        echo("<td class='d$g'>$d</td>");
        echo("</tr>\n");
        $g ^= 1;
    }
}
// display footer
if($totalsize > 1024) {
    if($totalsize > (1024 * 1024)) {
        $total = (int) ($totalsize / (1024 * 1024));
        $u = "MiB";
    }
    else {
        $total = (int) ($totalsize / 1024);
        $u = "kiB";
    }
}
else {
    $total = $totalsize;
    $u = "B";
}
echo("<tr class='footer'>");
echo("<td class='footer'>" . count($arr) . " files</td>");
echo("<td class='footer'>$total $u</td>");
echo("<td class='footer'></td>");
echo("</tr>\n");
echo("</table>\n");

// check for a file index.txt. If it exists, add it to the output
@readfile("index.txt");

?>
</body>
</html>
