<?php
$userobj = new User();

$currentpagenames=isset($_GET['itfpage'])?$_GET['itfpage']:'';
$pagetitle="Profile Configuration";
$ItfInfoData = $userobj->Get_User($_SESSION['LoginInfo']['USERID']);
include(BASEPATHS."/fckeditor/fckeditor.php");
?>


<script type="text/javascript">

    $(document).ready(function() {

        var Validator = jQuery('#itffrminput').validate({
            rules: {

                name: "required",
                last_name: "required",
                email: {required:true, email:true},

            },
            messages: {

                name: "Please enter first name.",
                last_name:"Please enter last name.",
                email:"Please enter valid email id.",

            }
        });
    });
</script>
<?php
if(isset($_POST['id']))
{
    if(!empty($_POST['id']))
    {
        $userobj->admin_update($_POST);
        flash("Configuration is successfully updated");
        redirectUrl("itfmain.php?itfpage=".$currentpagenames);
    }
}
?>
<div class="full_w">
    <!-- Page Head -->
    <div class="h_title"><?php echo $pagetitle;?></div>

    <form action="" method="post" name="itffrminput" id="itffrminput">
        <input type="hidden" name="id" id="id" value="<?php echo isset($ItfInfoData['id'])?$ItfInfoData['id']:'' ?>" />

            <div class="element">
                <span class="req">&nbsp;</span>
                <label>First Name<span class="red">(required)</span></label>
                <input class="text" name="name" type="text"  id="name" size="35" value="<?php echo isset($ItfInfoData['name'])?$ItfInfoData['name']:'' ?>" required="required" />
            </div>

            <div class="element">
                <span class="req">&nbsp;</span>
                <label>Last Name<span class="red">(required)</span></label>
                <input class="text" name="last_name" type="text"  id="last_name" size="35" value="<?php echo isset($ItfInfoData['last_name'])?$ItfInfoData['last_name']:'' ?>" required="required" />
            </div>

            <div class="element">
                <span class="req">&nbsp;</span>
                <label>Email Id<span class="red">(required)</span></label>
                <input class="text" name="email" type="text"  id="email" size="35" value="<?php echo isset($ItfInfoData['email'])?$ItfInfoData['email']:'' ?>" required="required" />
            </div>

            <!-- Form Buttons -->
            <div class="entry">
                <button type="submit">Submit</button>
                <button type="button" onclick="history.back()">Back</button>
            </div>
            <!-- End Form Buttons -->
    </form>
    <!-- End Form -->
</div>