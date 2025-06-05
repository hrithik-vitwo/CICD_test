<?php

############## Function for page navigation #############

$GLOBALS['show']=25;	
if(isset($_REQUEST['pageNo'])=="")
{

	$GLOBALS['start'] = 0;

	$_REQUEST['pageNo'] = 1;

}else{

	$GLOBALS['start']=($_REQUEST['pageNo']-1) * $GLOBALS['show'];
}


function pagination($count,$frmName)
	{
		if(isset($_POST['mode']))
			{
				if($_REQUEST['mode']=='delete'){
					$count=$count-1;
					$noOfPages = ceil($count/$GLOBALS['show']);
					$_REQUEST['pageNo']=$noOfPages;
				}
				else{
					$noOfPages = ceil($count/$GLOBALS['show']);
				}
			}

			else{
					$noOfPages = ceil($count/$GLOBALS['show']);
				}
		
		
?>
<script language="JavaScript">

function prevPage(no){
	document.<?php echo $frmName?>.action="<?php echo $_SERVER['REQUEST_URI']?>";
	document.<?php echo $frmName?>.pageNo.value = no-1;
	document.<?php echo $frmName?>.submit();
}
function nextPage(no){
	document.<?php echo $frmName?>.action="<?php echo $_SERVER['REQUEST_URI']?>";
	document.<?php echo $frmName?>.pageNo.value = no+1;
	document.<?php echo $frmName?>.submit();
}
function disPage(no){
	document.<?php echo $frmName?>.action="<?php echo $_SERVER['REQUEST_URI']?>";
	document.<?php echo $frmName?>.pageNo.value = no;
	document.<?php echo $frmName?>.submit();
}

//-->
</script>
<?php if($_REQUEST['pageNo']!=1){ ?>
 				<a class="" href="<?php echo $_SERVER['REQUEST_URI']?>" title="First Page">First</a>
				<a href="javascript:prevPage(<?php echo $_REQUEST['pageNo'] ?>);" onmouseout="javascript:window.status='Done';" onmousemove="javascript:window.status='Go to Previous Page';" >Previous</a>

 
 
 			<?php }?><?php ####### script to display no of pages #########
			//condition where no of pages is less than display limit
			$displayPageLmt = $GLOBALS['show']; #holds no of page links to display
			if($noOfPages <= $displayPageLmt){
				for($pgLink = 1; $pgLink <= $noOfPages; $pgLink++){
					if($pgLink==$_REQUEST['pageNo']){
						echo "<a href=\"#\" style=\"text-decoration:none\" onmouseout=\"javascript:window.status='Done';\" onmousemove=\"javascript:window.status='Go to this Page';\" class=\"number current\" >&nbsp;&nbsp;$pgLink&nbsp;</a>";
					}
					else{
						echo "<a href=\"javascript:disPage($pgLink)\"  onmouseout=\"javascript:window.status='Done';\" onmousemove=\"javascript:window.status='Go to this Page';\" class=\"number\" >$pgLink</a>";
					}	
					if($pgLink<>$noOfPages) echo "&nbsp;&nbsp;";
				} #end of for loop
			} #end of if
			//condition for no of pages greater than display limit
			if($noOfPages > $displayPageLmt){
				if(($_REQUEST['pageNo']+($displayPageLmt-1)) <= $noOfPages){
					for($pgLink = $_REQUEST['pageNo']; $pgLink <= ($_REQUEST['pageNo']+$displayPageLmt-1); $pgLink++){
						if($pgLink==$_REQUEST['pageNo']){
							echo "<a href=\"#\" style=\"text-decoration:none\" onmouseout=\"javascript:window.status='Done';\" onmousemove=\"javascript:window.status='Go to this Page';\" class=\"number current\">&nbsp;&nbsp;$pgLink&nbsp;&nbsp;</a>";
						}
						else{
							echo "<a href=\"javascript:disPage($pgLink)\" style=\"text-decoration:none\" onmouseout=\"javascript:window.status='Done';\" onmousemove=\"javascript:window.status='Go to this Page';\" class=\"number\">$pgLink</a>";
						}
						if($pgLink<>($_REQUEST['pageNo']+$displayPageLmt-1)) echo "&nbsp;&nbsp;";
					}#end of for loop						
				}#end of inner if
				else{
					for($pgLink = ($noOfPages - ($displayPageLmt-1)); $pgLink <= $noOfPages; $pgLink++){
						if($pgLink==$_REQUEST['pageNo']){
							echo "<a href=\"#\" style=\"text-decoration:none\" onmouseout=\"javascript:window.status='Done';\" onmousemove=\"javascript:window.status='Go to this Page';\" class=\"number current\">&nbsp;&nbsp;$pgLink&nbsp;&nbsp;</a>";
						}
						else{
							echo "<a href=\"javascript:disPage($pgLink)\" style=\"text-decoration:none\" onmouseout=\"javascript:window.status='Done';\" onmousemove=\"javascript:window.status='Go to this Page';\" class=\"number\">$pgLink</a>";
						}
						if($pgLink<>$noOfPages) echo "&nbsp;&nbsp;";
					}#end of for loop
				}					
			}#end of if noOfPage>displayPageLmt
		?>
		<?php if($_REQUEST['pageNo'] != $noOfPages) { ?>
				<a href="javascript:nextPage(<?php echo $_REQUEST['pageNo'] ?>)" onmouseout="javascript:window.status='Done';" onmousemove="javascript:window.status='Go to Next Page';" style="text-decoration:none">Next</a>
				<a href="javascript:disPage(<?php echo $noOfPages?>)" title="Last Page">Last</a>
			<?php }?>
<?php
}



?>