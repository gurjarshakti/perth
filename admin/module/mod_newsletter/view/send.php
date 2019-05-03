<?php
if(isset($_POST['id']))
{
	if(!empty($_POST['id']))
	{
		$obj->admin_update($_POST);
		flash("Content is succesfully updated");
		redirectUrl("itfmain.php?itfpage=".$currentpagenames);
	}
	else
	{
		$obj->admin_send_newsletter($_POST);
		flash("Newsletter is succesfully sent");
		redirectUrl("itfmain.php?itfpage=".$currentpagenames);
	}
}
$ids=isset($_GET['id'])?$_GET['id']:'';
$templates = $obj->ShowAllNewsletter();
$members = $obj->ShowAllSubscribersActive();
//echo"<pre>"; print_r($templates); die;
include(BASEPATHS."/fckeditor/fckeditor.php")
?>
<script type="text/javascript">

$(document).ready(function() {

    var Validator = jQuery('#itffrminput').validate({
        rules: {

            newsletter: "required",
            members: "required"
			
        },
		messages: {
            newsletter: "required",
            members: "required"
		}
    });
});
</script>
<div class="full_w">
	<!-- Page Heading -->
        <div class="h_title">Send Newsletter</div>
<!--    <div class="h_title"><?php echo ($ids=="")?"Add New ":"Edit "; echo $pagetitle;?></div>-->
    <!-- Page Heading -->
					
<form action="" method="post" name="itffrminput" id="itffrminput">
<input type="hidden" name="id" id="id" value="<?php echo isset($ItfInfoData['id'])?$ItfInfoData['id']:'' ?>" />


    <div class="element">
        <label>Newsletter Template<span class="red">(required)</span></label>
        <?php echo Html::ComboBox("newsletter",Html::CovertSingleArray($templates,"id","title"),"",array(),"Select Template"); ?>
    </div>

<!--
    <div class="element">
        <label>Subscribe Members<span class="red">(required)</span></label>
        <?php //echo Html::ComboBox("members",Html::CovertSingleArray($members,"id","email"),"",array("multiple"=>"multiple","class"=>"multiple"),""); ?>

    </div>-->

<div class="element">
 <select name="select2[]" id="select2" size="10" multiple="multiple" tabindex="1">

                  <option value="">---Please Select Email Id--- </option>

                <?php

                foreach($members as $emailid)

     

	

        {?>

          <option value="<?php echo $emailid['email']; ?>"><?php echo $emailid['email']; ?> </option>

    

         

       <?php  } ?>




          

      </select>
 </div>



<!-- Form Buttons -->
    <div class="entry">
        <button type="submit">Send</button>
        <button type="button" onclick="history.back()">Back</button>
    </div>
<!-- End Form Buttons -->
</form>
    <!-- End Form -->
</div>