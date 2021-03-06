<?php
if(isset($_POST['id']))
{
	if(!empty($_POST['id']))
	{
		$userobj->user_update($_POST);
		flash("Customer/Supplier is succesfully updated");
		redirectUrl("itfmain.php?itfpage=".$currentpagenames);
	}
	else
	{
		$userobj->freeRegister($_POST);
		flash("Customer is succesfully added");
		redirectUrl("itfmain.php?itfpage=".$currentpagenames);
	}
}
$ids=isset($_GET['id'])?$_GET['id']:'';
$ItfInfoData = $userobj->getUserInfo($ids);

$objsite = new Site();
$countries = $objsite->getCountries();
$payment_types = array('credit_card'=>'Credit Card','account'=>'Account');
$pagetitles="Customer/Supplier";
?>
<script type="text/javascript">
$(document).ready(function() {

    $.validator.addMethod("noSpace", function(value, element) {

        var resinfo = parseInt(value.indexOf(" "));

        if(resinfo == 0 && value !="") { return false; } else return true;

    }, "Space are not allowed as first string !");

    var Validator = jQuery('#itffrminput').validate({
        rules: {

            name:{required:true, maxlength:'100', noSpace: true},
            last_name:{required:true, maxlength:'100', noSpace: true},
			email:{required:true,email:true,
                <?php if(empty($ids)) { ?>
                remote: {
                    url: "<?php echo SITEURL; ?>/itf_ajax/index.php",
                    type: "post",
                    data: {
                        emailid: function() {
                            return $( "#email" ).val();
                        }
                    }
                }
               <?php } ?>
			
			},
            <?php if(empty($ids)) { ?>
           'password': {
                required: true
            },
            <?php } ?>
           'password2': {
               <?php if(empty($ids)) { ?>
                required: true,
               <?php } ?>
                equalTo: '#password'
            },

            payment_type:"required",
            country_code:"required"
        },
		messages: {
			
			name: " Please enter first name",
			last_name: " Please enter last name",

			email:" Please enter valid email address",
			
		}
    });
});
</script>


<div class="full_w">
    <!-- Page Heading -->
    <div class="h_title"><?php echo ($ids=="")?"Add New ":"Edit "; echo $pagetitles;?></div>
    <!-- Page Heading -->

    <form action="" method="post" name="itffrminput" id="itffrminput" >
        <input type="hidden" name="id" id="id" value="<?php echo isset($ItfInfoData['user_id'])?$ItfInfoData['user_id']:'' ?>" />
        <input type="hidden" name="usertype" value="4" />
         <input type="hidden" name="memberid" value="8">
        <div class="element">
            <label>First Name<span class="red">(required)</span></label>
            <input class="text" name="name" type="text"  id="name" size="35" value="<?php echo isset($ItfInfoData['name'])?$ItfInfoData['name']:'' ?>" />
        </div>

        <div class="element">
            <label>Last Name<span class="red">(required)</span></label>
            <input class="text"  name="last_name" type="text"  id="last_name" size="35" value="<?php echo isset($ItfInfoData['last_name'])?$ItfInfoData['last_name']:'' ?>" />
        </div>

        <div class="element">
            <label>Email id<span class="red">(required)</span></label>
            <?php if(empty($ids)){ ?>
                <input class="text" name="email" type="text"  id="email" size="35" value="<?php echo isset($ItfInfoData['email'])?$ItfInfoData['email']:'' ?>" />
            <?php } else{ ?>
                <input class="text" name="email2" type="text" readonly="readonly" size="35" value="<?php echo isset($ItfInfoData['email'])?$ItfInfoData['email']:'' ?>" />
            <?php } ?>
        </div>

        <div class="element">
            <label>Password<?php if(empty($ids)){ ?><span class="red">(required)</span> <?php } ?></label>
            <input class="text" name="password" type="password"  id="password" size="35" value="" />
        </div>

        <div class="element">
            <label>Verify Password <?php if(empty($ids)){ ?><span class="red">(required)</span> <?php } ?></label>
            <input class="text" name="password2" type="password"  id="password2" size="35" value="" />
        </div>

<!--        <div class="element">
            <label>Payment Type<span class="red">(required)</span></label>
            <?php //echo Html::ComboBox("payment_type",$payment_types,$ItfInfoData['payment_type'],array(),"Select Payment Type"); ?>
        </div>-->

        <div class="element">
            <label>Address</label>
            <textarea name="address" class="textarea"><?php echo isset($ItfInfoData['address'])?$ItfInfoData['address']:'' ?></textarea>
        </div>

        <div class="element">
            <label>Country<span class="red">(required)</span></label>
            <?php echo Html::ComboBox("country_code",Html::CovertSingleArray($countries,"country_code","country_name"),$ItfInfoData['country_code'],array(),"Select Country"); ?>
        </div>

        <div class="element">
            <label>Phone</label>
            <input class="text" name="business_phone" type="text"  id="business_phone" size="35" value="<?php echo isset($ItfInfoData['business_phone'])?$ItfInfoData['business_phone']:'' ?>" />
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