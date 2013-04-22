<script>
	$(function() {
		$( "#userTabs" ).tabs();
		//search
			$("#kwd_search").keyup(function(){
			// When value of the input is not blank
				if( $(this).val() != "")
				{
					var id = $('#userTabs .ui-tabs-selected > a').attr('href');
				// Show only matching TR, hide rest of them
					$(id + "> .my-table tbody>tr").hide();
					$(".my-table td:contains-ci('" + $(this).val() + "')").parent("tr").show();
				}
				else
				{
				// When there is no input or clean again, show everything back
					$(".my-table tbody>tr").show();
				}
			});
			//search 
			
			//modal
			$('.adminEditUser').click(function(e) {
				//Cancel the link behavior
				e.preventDefault();
				//Get the A tag
				var id = $(this).attr('href');
			 	//put value on forms
					var row = $(this).closest('tr').attr('id');
					$('#hEditId').attr('value',row);
					$.ajax({
						url: "/cms101/userFunc.php",
						data: "action=searchById&usr_id="+row,
						dataType: "html",
						type: "post",
						success:function(data) {
							var obj  = jQuery.parseJSON( data );
							// Now the two will work
							$.each(obj, function(key, value) {
    							if(key=='email') {$('#adminEditemail').attr('value', value);$('#hadminEditemail').attr('value', value);}
								if(key=='pwd') {$('#adminEditpwd').attr('value', value);$('#hadminEditpwd').attr('value', value);}
								if(key=='fname') {$('#adminEditfname').attr('value', value);$('#hadminEditfname').attr('value', value);}
								if(key=='mname') {$('#adminEditmname').attr('value', value);$('#hadminEditmname').attr('value', value);}
								if(key=='lname') {$('#adminEditlname').attr('value', value);$('#hadminEditlname').attr('value', value);}
								if(key=='role') {$('#adminEditRole').val(value);$('#hadminEditRole').val(value);}
							});
							
							
						}
					});	
				//Get the screen height and width
				var maskHeight = $(document).height();
				var maskWidth = $(window).width();
			 
				//Set height and width to mask to fill up the whole screen
				$('#mask').css({'width':maskWidth,'height':maskHeight});
				 
				//transition effect     
				$('#mask').fadeIn(1000);    
				$('#mask').fadeTo("slow",0.8);  
			 
				//Get the window height and width
				var winH = $(window).height();
				var winW = $(window).width();
					   
				//Set the popup window to center
				$(id).css('top',  winH/2-$(id).height()/2);
				$(id).css('left', winW/2-$(id).width()/2);
			 
				//transition effect
				$(id).fadeIn(2000); 
			 
			});
			 
			//if close button is clicked
			$('.window .close, #adminEditUser').click(function (e) {
				//Cancel the link behavior
				e.preventDefault();
				$('#mask, .window').hide();
			});     
			 
			//if mask is clicked
			$('#mask').click(function () {
				$(this).hide();
				$('.window').hide();
			});         
			//modal
	});
	$.extend($.expr[":"],
		{
    		"contains-ci": function(elem, i, match, array)
			{
				return (elem.textContent || elem.innerText || $(elem).text() || "").toLowerCase().indexOf((match[3] || "").toLowerCase()) >= 0;
			}
		});
</script>
                     	<?php
								echo "<div id='adminAdd'><table>";
								echo "<tr><td><input type='text' placeholder='Last Name' id='addLname'/><input type='text' placeholder='Middle Name' id='addMname'/><input type='text' placeholder='First Name' id='addFname'/></td><td><input type='email' placeholder='E-mail' id='addEmail'/></td><td><input type='password' placeholder='Password' id='addPwd'/></td><td><input type='password' placeholder='Confirm Password' id='addCPwd'/></td><td><select id='addRole'><option value='typical'>Typical</option><option value='admin'>Administrator</option></select></td><td><input type='button' value='Add' id='adminAddUser'/><a href='#' id='adminCancelUser'>Cancel</a></td></tr>";
								echo "</table></div>";
						?>
                        
                        <div id="userTabs">
                            <ul>
                                <li id='confirmedUserTab'><a href="#confirmedUser">Confirmed User</a></li>
                                <li id='pendingUserTab'><a href="#pendingUser">Pending User</a></li>
                                <li style='float:right;'><label for="kwd_search">Search:</label><input type="text" id="kwd_search" value=""/></li>
                            </ul>
        <div id="confirmedUser">
        	<?php
			echo '<table id="cTable" class="my-table" border="1" style="border-collapse:collapse">';
			echo "<thead><tr><th>Id</th><th>Name</th><th>E-mail</th><th>Password</th><th>Role</th><th>Reg. Date</th><th>Member since</th><th>Action</th></tr></thead>";
			$sql="SELECT * FROM user where usr_cDate is not null";
			if (!($result=mysql_query($sql)))
			{
				echo json_encode('Error: ' . mysql_error());
			}
			else{
				echo '<tbody>';
				while($row = mysql_fetch_array($result)){
				echo "<tr id='".$row['usr_id']."'><td>".$row['usr_id']."</td><td>".$row['usr_lname']." ".$row['usr_mname']." ".$row['usr_fname']."</td><td>".$row['usr_email']."</td><td>".$row['usr_pwd']."</td><td>".$row['usr_role']."</td><td>".$row['usr_regDate']."</td><td>".$row['usr_cDate']."</td><td><a href='#' title='Delete User' class='adminDeleteUser'><img src='include/images/delete.png' class='eyelet'/></a><a href='#dialog' class='adminEditUser'title='Edit User'><img src='include/images/edit.png' class='eyelet'/></a></td></tr>";
				}
			}
			echo "</tbody></table>";
			?>
        </div>
        
        <div id="pendingUser">
        	<?php
			echo '<table class="my-table" border="1" style="border-collapse:collapse">';
			echo "<thead><tr><th>Id</th><th>Name</th><th>E-mail</th><th>Password</th><th>Role</th><th>Reg. Date</th><th>Action</th></tr></thead>";
			$sql="SELECT * FROM user where usr_cDate is null";
			if (!($result=mysql_query($sql)))
			{
				echo json_encode('Error: ' . mysql_error());
			}
			else{
				echo '<tbody>';
				while($row = mysql_fetch_array($result)){
				echo "<tr id='".$row['usr_id']."'><td>".$row['usr_id']."</td><td>".$row['usr_lname']." ".$row['usr_mname']." ".$row['usr_fname']."</td><td>".$row['usr_email']."</td><td>".$row['usr_pwd']."</td><td>".$row['usr_role']."</td><td>".$row['usr_regDate']."</td><td><a href='#' title='Confirm User' class='adminConfirmUser'><img src='include/images/check.png' class='eyelet'/></a><a href='#' title='Delete User' class='adminDeleteUser' ><img src='include/images/delete.png' class='eyelet'/></a></td></tr>";
				}
			}
			echo "</tbody></table>";
			?>
        </div>
        <!--End of pending user-->
        <!--Modal Form-->
        <div id="boxes">
    	<!-- #customize your modal window here -->
 
    		<div id="dialog" class="window">
        		<b>Edit User</b>
         		<form>
                     <div id='errorReg'></div>
                     <table>
                     		<tr><p>
                                <td><label for="name">E-mail</label></td>
                                <td><input type="email" name="email" id="adminEditemail"/></td>
                                <input type="hidden" id="hadminEditemail"/>
                            </p></tr>
                            <tr><p>
                                <td><label for="country">Password</label></td>
                                <td><input type="text" name="password" id="adminEditpwd"/></td>
                                <input type="hidden" id="hadminEditpwd"/>
                            </p></tr>
                            <tr><p>
                                <td><label for="phone">Firstname</label></td>
                                <td><input type="text" name="fname" id="adminEditfname"/></td>
                                <input type="hidden" id="hadminEditfname"/>
                            </p></tr>
                           <tr> <p>
                                <td><label for="website">Middlename</label></td>
                               <td><input type="text" name="mname" id="adminEditmname"/></td>
                               <input type="hidden" id="hadminEditmname"/>
                            </p></tr>
                            <tr><p>
                                <td><label for="website">Lastname</label></td>
                               <td><input type="text" name="lname" id="adminEditlname"/></td>
                               <input type="hidden" id="hadminEditlname"/>
                            </p></tr>
                            <tr><p>
                                <td><label for="website">Role</label></td>
                               <td><select id="adminEditRole"><option value="typical">Typical</option><option value="admin">Admin</option></select></td>
                               <input type="hidden" id="hadminEditRole"/>
                            </p></tr>
                     </table>
                     <!-- close button is defined as close class -->
                    <table>
        			<tr><td><input type='button' id='adminEditUser' value='Edit'/></td><td><a href="#" class="close">Close it</a></td></tr>
                	</table>
                    <input type='hidden' id='hEditId'/>
                    </form>
        		
    		</div>

   		 <!-- Do not remove div#mask, because you'll need it to fill the whole screen --> 
   			 	<div id="mask"></div>
		</div>
        <!--End of modal-->
	</div>