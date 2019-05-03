<?php
$perpage = $stieinfo['paging_size']; //limit in each page
$pagetitles='Ads';
if(isset($_POST['itf_datasid'],$_POST['itfactions']))
{
	$acts = $_POST['itfactions'];
	$ids = implode(',',$_POST['itf_datasid']);


	if($acts == 'delete')
		$obj->admin_delete($ids);
		flash("Your Ad has been succesfully deleted");
		redirectUrl("itfmain.php?itfpage=".$currentpagenames);
}


if(isset($_REQUEST['search']) and !empty($_REQUEST['search']))
{

	if(isset($_POST['txtsearch'])) $_SESSION['SEARCHDATA']=$_POST;
	
     
	$InfoData1 = $obj->ShowAllProductsSearch($_SESSION['SEARCHDATA']['txtsearch']);	
  
  
	$urlpath=CreateLinkAdmin(array($currentpagenames,"search"=>"text"))."&";
}
else

{

	unset($_SESSION['SEARCHDATA']);
	$InfoData1 = $obj->ShowAllProductPaid();
	$urlpath=CreateLinkAdmin(array($currentpagenames))."&";

}


$urlpath = CreateLinkAdmin(array($currentpagenames))."&";
$pagingobj = new ITFPagination($urlpath,$perpage);
$InfoData = $pagingobj->setPaginateData($InfoData1);

?>

<!-- Box -->
<div class="full_w">
    <!-- Page Heading -->
    <div class="h_title"><?php echo $pagetitles;?></div>
    <!-- Page Heading -->
    <div class="entry top_buttons">
        <!--<a href="<?php echo CreateLinkAdmin(array($currentpagenames,'actions'=>'add')); ?>" class="button add"><span>Add New <?php echo $pagetitle; ?></span></a>-->
<!--        <a href="<?php echo CreateLinkAdmin(array($currentpagenames,'actions'=>'import')); ?>" class="button import"><span>Import/Export <?php echo $pagetitle; ?></span></a>
-->        <a onclick="return itfsubmitfrm('delete','itffrmlists');" class="button cancel"><span>Delete</span></a>
     <form id="itffrmsearch" name="itffrmsearch" method="post" action="<?php echo CreateLinkAdmin(array($currentpagenames,'search'=>'text')); ?>">

				<label>Search <?php echo $pagetitle; ?></label>

				<input type="hidden" name="itfpage" value="<?php echo $currentpagenames; ?>" />

				<input name="txtsearch" type="text" id="txtsearch" class="field small-field" value="<?php echo isset($_SESSION['SEARCHDATA']['txtsearch'])?$_SESSION['SEARCHDATA']['txtsearch']:""; ?>" />

				<input name="searchuser" type="submit" id="searchuser"class="button" value="Search" />

				</form>
    </div>
    <div class="clear"></div>

    <form id="itffrmlists" name="itffrmlists" method="post" action="">
        <input type="hidden" name="itfactions" id="itfactions" value="" />
        <input type="hidden" name="itf_status" id="itf_status" value="" />
    <table>
        <thead>

        <tr>
            <th scope="col" class="chk_box">&nbsp;<input name="selectalls" id="selectalls" type="checkbox" value="0" /></th>
            <th scope="col" class="pro_name">Name</th>
            <!--  <th scope="col">Code</th>-->
            <th scope="col">Description</th>
            <th scope="col">Status</th>
            <th scope="col">Popular</th>
            <th scope="col" style="width: 65px;">Modify</th>
        </tr>
        </thead>

        <tbody>
        <?php
        
     
        if(count($InfoData) > 0){
        for($i=0;$i<count($InfoData);$i++)
        {
			$imge = unserialize($InfoData[$i]['image']);
            ?>
            <tr>
                <td class="align-center chk_box"><input name="itf_datasid[]" type="checkbox" value="<?php echo $InfoData[$i]['id']; ?>" class="itflistdatas"/></td>
                <td class="align-left pro_name">
                    <?php if(file_exists(PUBLICFILE."products/".$imge['0'])) { ?>
                    <img src="<?php echo PUBLICPATH."products/".$imge['0']; ?>" height="40" width="40"/>
                    <?php } else { ?>
                        <img src="<?php echo PUBLICPATH.'products/noImageProduct.jpg'; ?>" height="40" width="40"/>
                    <?php } ?>
                    <span class="pname"><?php echo $InfoData[$i]['name']; ?></span></td>
               <!-- <td class="align-left">
                    <?php echo $InfoData[$i]['code']; ?>		</td>-->
                <td class="align-left">
                    <?php echo WordLimit($InfoData[$i]['logn_desc'],6); ?>		</td>
                <td class="align-center">
                    <a href="#itf" class="activations" rel="<?php echo $InfoData[$i]['id']; ?>" rev="product"><img src="imgs/<?php echo $InfoData[$i]['status']; ?>.png" /></a>
                </td>
                 <td class="align-center">
                    <a href="#itf" class="activations" rel="<?php echo $InfoData[$i]['id']; ?>" rev="product_feature"><img src="imgs/<?php echo $InfoData[$i]['feature']; ?>.png" /></a>
                </td>
                <td class="align-center"><a href="<?php echo CreateLinkAdmin(array($currentpagenames,'actions'=>'edit_paid','id'=>$InfoData[$i]['id'])); ?>" title="Edit" alt="Edit"><img src="img/i_edit.png" border="0" /></a>	  </td>
            </tr>
        <?php
        } } else {
        ?>
            <tr>
                <td colspan="10" class="align-center">No Record Available !</td>
            </tr>
        <?php } ?>

        </tbody>
    </table>
    </form>

    <div class="entry">
        <div class="pagination">
            <?php echo $pagingobj->Pages(); ?>
        </div>
        <div class="sep"></div>
    </div>


</div>
<!-- End Box -->