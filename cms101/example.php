<?php
include( "config.php" );
function adj_tree(&$tree, $item) {
    $i = $item['page_id'];
    $p = $item['page_parentId'];
    $tree[$i] = isset($tree[$i]) ? $item + $tree[$i] : $item;
    $tree[$p]['_children'][] = &$tree[$i];
}

function print_tree($node, $indent) {
    echo str_repeat('...', $indent) . $node['page_id'], "<br>\n";
    if(isset($node['_children']))
        foreach($node['_children'] as $child)
            print_tree($child, $indent + 1);
}

$tree = array();
$rs = mysql_query("SELECT * FROM page") or die(mysql_error());
while($row = mysql_fetch_array($rs))
    adj_tree($tree, $row);

print_tree($tree[1], 0);
?>