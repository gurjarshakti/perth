<?php
$perpage = 10;//limit in each page
if(isset($_POST['itf_datasid'],$_POST['itfactions']))
{
	$acts = $_POST['itfactions'];
	$ids = implode(',',$_POST['itf_datasid']);


	if($acts == 'delete')
            $obj->admin_subscriber_delete($ids);
            flash("Member has been succesfully deleted");
            redirectUrl("itfmain.php?itfpage=".$currentpagenames.'&actions=subscriber');
}
$InfoData1 = $obj->ShowAllSubscribers();
$urlpath = CreateLinkAdmin(array($currentpagenames,'actions'=>'subscriber'))."&";
$pagingobj = new ITFPagination($urlpath,$perpage);
$InfoData = $pagingobj->setPaginateData($InfoData1);

?>

<!-- Box -->
<div class="full_w">
    <!-- Page Heading -->
    <div class="h_title"><?php echo "Subscribe Members";?></div>
    <!-- Page Heading -->
    <div class="entry top_buttons">
        <a onclick="return itfsubmitfrm('delete','itffrmlists');" class="button cancel"><span>Delete</span></a>
    </div>
    <div class="clear"></div>

    <form id="itffrmlists" name="itffrmlists" method="post" action="">
        <input type="hidden" name="itfactions" id="itfactions" value="" />
        <input type="hidden" name="itf_status" id="itf_status" value="" />
    <table>
        <thead>

        <tr>
            <th scope="col">&nbsp;<input name="selectalls" id="selectalls" type="checkbox" value="0" /></th>
            <th scope="col">Email</th>
            <th scope="col">Status</th>
          <!--  <th scope="col" style="width: 65px;">Modify</th>-->
        </tr>
        </thead>

        <tbody>
        <?php
        if(count($InfoData) > 0){
        for($i=0;$i<count($InfoData);$i++)
        {
            ?>
            <tr>
                <td class="align-center"><input name="itf_datasid[]" type="checkbox" value="<?php echo $InfoData[$i]['id']; ?>" class="itflistdatas"/></td>
                <td class="align-left">
                    <?php 	echo $InfoData[$i]['email']; ?>		</td>
                <td class="align-center">
                    <a href="#itf" class="activations" rel="<?php echo $InfoData[$i]['id']; ?>" rev="newsletter"><img src="imgs/<?php echo $InfoData[$i]['status']; ?>.png" /></a>
                </td>
               <!-- <td class="align-center"><a href="<?php echo CreateLinkAdmin(array($currentpagenames,'actions'=>'subscriber_edit','id'=>$InfoData[$i]['id'])); ?>" title="Edit" alt="Edit"><img src="img/i_edit.png" border="0" /></a>	  </td>-->
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