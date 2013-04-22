 <!--LIST-->
                            <ul id="dhtmlgoodies_tree2" class="dhtmlgoodies_tree">
                            <?php
                                $tree = array();
                                $rs = mysql_query("SELECT * FROM page") or die(mysql_error());
                                while($row = mysql_fetch_array($rs))
                                            adj_tree($tree, $row);
                                print_tree($tree[1], 0);
								function adj_tree(&$tree, $item) {
		$i = $item['page_id'];
		$p = $item['page_parentId'];
		$tree[$i] = isset($tree[$i]) ? $item + $tree[$i] : $item;
		$tree[$p]['_children'][] = &$tree[$i];
	}
	
                                function print_tree($node, $indent) {
                                    //echo str_repeat('...', $indent) . $node['page_id'], "<br>\n";
                                    if($node['page_parentId']== null || !isset($node['page_parentId']))
                                        echo '<li id="'.$node['page_id'].'" noDrag="true" noSiblings="true" noDelete="true" noRename="true"><a class="editPage" href="#" title="'.$node['page_title'].'">'.$node['page_title'].'</a><a href="#" title="Add child" class="addChild"><img src="include/images/add.png" class="eyelet"/></a><a href="#" title="Edit" class="editContent"><img src="include/images/edit.png" class="eyelet"/></a><a href="#" class="smallText" style="float:right;margin-right:10%;">'.$node['page_status'].'</a><hr/>';
                                    else echo '<li id="'.$node['page_id'].'" noRename="true" noDelete="true" noDrag="true"><a class="editPage" href="#" title="'.$node['page_title'].'">'.$node['page_title'].'</a><a href="#" title="Add child" class="addChild"><img src="include/images/add.png" class="eyelet"/></a><a href="#" title="Delete" class="deletePage"><img src="include/images/delete.png" class="eyelet"/></a><a href="#" title="Edit" class="editContent"><img src="include/images/edit.png" class="eyelet"/></a><a href="#" class="smallText" style="float:right;margin-right:10%;">'.$node['page_status'].'</a><hr/>';
                                    if(isset($node['_children'])){
                                        echo "<ul>";
                                        foreach($node['_children'] as $child)
                                            print_tree($child, $indent + 1);
                                        echo "</ul>";
                                    }
                                    echo "</li>";
                                }
                            ?>

                                </ul>

                                <script type="text/javascript">	
                                treeObj = new JSDragDropTree();
                                treeObj.setTreeId('dhtmlgoodies_tree2');
                                treeObj.setMaximumDepth(7);
                                treeObj.setMessageMaximumDepthReached('Maximum depth reached'); // If you want to show a message when maximum depth is reached, i.e. on drop.
                                treeObj.initTree();
                                treeObj.expandAll(); 
                                </script>
                           <!--END OF LIST-->