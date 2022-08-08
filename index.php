<?php
date_default_timezone_set("Europe/Berlin");


function sorter($key, $order) {
    return function ($a, $b) use ($key, $order) {
        if(is_integer($a[$key]))
            return ($a[$key] - $b[$key]) * $order;
        else
            return strnatcmp($a[$key], $b[$key]) * $order;
    };
}

function createtable()
{
    $sortkey = "name";
    if(array_key_exists('s', $_GET)) {
        if($_GET['s'] === "d")
            $sortkey = "date";
        else if($_GET['s'] === "s")
            $sortkey = "size";
        else
            $sortkey = "name";
    }
    $sortorder = 1;
    if(array_key_exists('o', $_GET)) {
        if($_GET['o'] === "d")
            $sortorder = -1;
    }

    $files = glob("*");

    // add parent folder if it's readable.
    if(is_readable("../")) {
        array_push($files, "..");
    }

    // create list of items.
    $arr = array();
    foreach($files as $item)
    {
        // hide ourselves.
        if($item == basename($_SERVER['PHP_SELF']))
            continue;
        if($item == "index.txt")
            continue;
        $item_size = filesize($item);
        $item_date = date("Y-m-d H:i:s", filemtime($item));
        $item_name = $item;

        if(is_link($item))
            $item_link = readlink($item);
        else
            $item_link = false;

        $arr[] = array("name" => $item_name, "size" => $item_size,
                       "date" => $item_date, "link" => $item_link);
    }
    // sort items
    if(count($arr) > 0) {
        usort($arr, sorter($sortkey, $sortorder));
    }

    // display table header
    $hdritems = array("n" => "Name", "s" => "Size", "d" => "Date");
    echo("<table summary='Folder Listing' id='ls'>\n");
    echo("<tbody>\n");
    echo("<thead><tr class='title'>\n");
    foreach($hdritems as $key => $value) {
        $direction = $key == "n" && !array_key_exists("s", $_GET) ? "d" : "a";
        $view = $key == "n" && !array_key_exists("s", $_GET) ? "↓" : "";
        if(array_key_exists("s", $_GET) && $_GET["s"] == $key
                && array_key_exists("o", $_GET)) {
            $direction = $_GET["o"] == "a" ? "d" : "a";
            $view = $_GET["o"] == "a" ? "↓" :"↑";
        }
        echo("<th><a href='$_SERVER[PHP_SELF]?s=$key&amp;o=$direction'>$value&nbsp;$view</a></th>\n");
    }
    echo("</tr></thead>\n");
    // display items.
    $g = 0;
    $totalsize = 0;
    $sort = array_key_exists("s", $_GET) ? $_GET["s"] : "n";
    if(count($arr) > 0) {
        foreach($arr as $item) {
            $totalsize += $item["size"];
            if($item["size"] > 1024 * 1024) {
                $item["size"] = (int) ($item["size"] / 1024 / 1024);
                $u = "MiB";
            }
            else if($item["size"] > 1024) {
                $item["size"] = (int) ($item["size"] / 1024);
                $u = "kiB";
            }
            else {
                $u = "B";
            }
            if(is_dir($item["name"])) {
                $a = "folder";
            }
            else {
                $a = "file";
            }
            echo("<tr class='grey$g'>");
            $cls = ($sort === "n") ? "high$g" : "grey$g";
            echo("<td class='n$g $cls'><div class='$a'>");
            echo("<a href='$item[name]'>");
            if(is_string($item["link"])) {
                echo("<span class='symlink'>$item[name] → $item[link]</span>");
            }
            else {
                echo("$item[name]");
            }
            echo("</a></div></td>");
            $cls = ($sort === "s") ? $cls = "high$g" : "grey$g";
            echo("<td class='s$g $cls'>$item[size]&nbsp;$u</td>");
            $cls = ($sort === "d") ? $cls = "high$g" : "grey$g";
            echo("<td class='d$g $cls'>$item[date]</td>");
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
    echo("</tbody>\n");
    echo("<tr class='footer'>");
    echo("<td class='footer'>" . count($arr) . "&nbsp;files</td>");
    echo("<td class='footer'>$total&nbsp;$u</td>");
    echo("<td class='footer'></td>");
    echo("</tr>\n");
    echo("</table>\n");
}

?>
<?php
$title = preg_replace(':.*/:', '', dirname($_SERVER['PHP_SELF']));
if($title === "") $title = $_SERVER['HTTP_HOST'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='en' lang='en'>
<head>
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta name="color-scheme" content="dark light">
<title><?php echo($title); ?></title>
<style type='text/css'>
body { font-family:verdana,sans-serif; background-color:#fff; }
.grey0 { background-color:#ddd; }
.grey1 { background-color:#bbb; }
.high0 { background-color:#ccc; }
.high1 { background-color:#aaa; }
.title { background-color:#eee; }
.footer { border-top:1px solid black; background-color:#eee;
  font-size:small; text-align:right; }
.symlink { font-style:italic; }
h1 { text-align:center; }
table { margin:0px; border:0px; padding:2px;
  border-spacing:0px; border:1px dashed #000;
  margin-left:auto; margin-right:auto; }
td { padding:.3em; }
th { padding:.5em; }
a { text-decoration:none; }
a:hover { text-decoration:underline; }
a:visited { color:#0000ff; }
.s0, .s1 { text-align:right; }
.file {
  background: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH1QQWFA84umAmQgAAANpJREFUOMutkj1uhDAQhb8HSLtbISGfgZ+zbJkix0HmFhwhUdocBnMBGvqtTIqIFSReWKK8aix73nwzHrVt+zEMwwvH9FrX9TsA1trpqKy10+yUzME4jnjvAZB0LzXHkojjmDRNVyh3A+89zrlVwlKSqKrqVy/J8lAUxSZBSMny4ZLgp54iyPM8UPHGNJ2IomibAKDv+9VlWZbABbgB5/0WQgSSkC4PF2JF4JzbHN430c4vhAm0TyCJruuClefph4yCBCGT3T3Isoy/KDHGfDZNcz2SZIx547/0BVRRX7n8uT/sAAAAAElFTkSuQmCC')
    no-repeat left center;
  padding-left:24px;
}
.folder {
  background:url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAAN1wAADdcBQiibeAAAABl0RVh0U29mdHdhcmUAd3d3Lmlua3NjYXBlLm9yZ5vuPBoAAAHCSURBVDiNpZAxa5NRFIafc+9XLCni4BC6FBycMnbrLpkcgtDVX6C70D/g4lZX/4coxLlgxFkpiiSSUGm/JiXfveee45AmNlhawXc53HvPee55X+l2u/yPqt3d3Tfu/viatwt3fzIYDI5uBJhZr9fr3TMzzAx3B+D09PR+v98/7HQ6z5fNOWdCCGU4HH6s67oAVDlnV1UmkwmllBUkhMD29nYHeLuEAkyn06qU8qqu64MrgIyqYmZrkHa73drc3KTVahFjJITAaDRiPB4/XFlQVVMtHH5IzJo/P4EA4MyB+erWPQB7++zs7ccYvlU5Z08pMW2cl88eIXLZeDUpXzsBkNQ5eP1+p0opmaoCTgzw6fjs6gLLsp58FB60t0DcK1Ul54yIEIMQ43Uj68pquDmCeJVztpwzuBNE2LgBoMVpslHMCUEAFgDVxQbzVAiA+aK5uGPmmDtZF3VpoUm2ArhqQaRiUjcMf81p1G60UEVhcjZfAFTVUkrgkS+jc06mDX9nvq4YhJ9nlxZExMwMEaHJRutOdWuIIsJFUoBSuTvHJ4YIfP46unV4qdlsjsBRZRtb/XfHd5+C8+P7+J8BIoxFwovfRxYhnhxjpzEAAAAASUVORK5CYII=')
    no-repeat left center;
  padding-left:24px;
}
@media (prefers-color-scheme: dark) {
    body {
        background: #333;
    }
    table { border:1px dashed #ddd; }
    .grey0 { background-color:#444; }
    .grey1 { background-color:#222; }
    .high0 { background-color:#333; }
    .high1 { background-color:#111; }
    .title { background-color:#555; }
    .footer { border-top:1px solid grey; background-color:#333;
</style>
</head>
<body>
<?php
// sort column and order. Assume (n)ame and (a)scending if unset.
echo("<h1>Index of $title</h1>");
createtable();
// check for a file index.txt. If it exists, add it to the output
@readfile("index.txt");
?>
</body>
</html>
