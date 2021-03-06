<?php
Html::AddJavaScript("dashboard/assests/jquery.js","component");
//Html::AddJavaScript("dashboard/assests/jquery-ui-sliderAccess.js","component");
//Html::AddJavaScript("dashboard/assests/jquery-ui-timepicker-addon.js","component");
Html::AddStylesheet("dashboard/assests/jquery-ui-timepicker-addon.css","component");

if(empty($_SESSION['FRONTUSER']))
{
 
    redirectUrl(CreateLink(array("signin")));
}

$countries = $objsite->getCountries();

$userinfo = $obj->getUserInfo($_SESSION['FRONTUSER']['id']);

$quote = new Quote();
$quote_info = $quote->getQuote();


$state = new State();
$areas = $state->getAllStateFront();


$serviceObj = new ServiceCategory();
$services = $serviceObj->getCategories();

$categoryObj = new Category();
$cats = $categoryObj->getCategories();
$categories = array();
foreach($cats as $categorys)
{
    foreach($categorys['subcat'] as $subcat){
        foreach($subcat['subcat'] as $subsubcat){
            $categories[] = $subsubcat;
        }
    }
}
//echo"<pre>"; print_r($categories); die;

$finalizeData = $quote->getOrder();
$carts = $quote->getCart();

$activeQuotes = $quote->getActiveQuote();
//echo "<pre>";print_r($activeQuotes);die;
$closedQuotes = $quote->getClosedQuote();
$expiredQuotes = $quote->getExpiredQuote();


$transactions = $quote->getAllTransactions();

$quoteobj = new Quote();



//echo"<pre>"; print_r($expiredQuotes); die;

if(isset($_POST['submit'])){

    if(!empty($_POST['emailid'])){
        if(!empty($_POST['change_password'])){
            $obj->ChangePasswordFront($_POST['change_password']);
        }

        $obj->front_user_update($_POST);
        flashMsg("Your profile is modified.");
        redirectUrl(CreateLink(array("dashboard#tab7")));
    }

    if(!empty($_POST['quote_name']) && $_POST['submit'] == 'Finalize Enquiry' ){
        $quote->addQuote($_POST);
        flashMsg("Success: Your Quote is successfully created ");
        redirectUrl(CreateLink(array("dashboard#tab2")));
    }

    if(!empty($_POST['quote_name'])){
        $prodata = array();

        if(isset($_FILES['product_image']['name']) and !empty($_FILES['product_image']['name']))
        {
            $fimgname="plucka_".rand();
            $objimage= new ITFImageResize();
            $objimage->load($_FILES['product_image']['tmp_name']);
            $objimage->save(PUBLICFILE."products/".$fimgname);
            $imagename = $objimage->createnames;
        }
        $prodata = array('main_image'=>$imagename,'code'=>'CP'.time(),'logn_desc'=>$_POST['product_desc'],'special_req'=>$_POST['special_req']);
        $product = new Product();
        $product_id= $product->admin_add($prodata);
        $quote_data = array('product_id'=>$product_id,'quote_name'=>$_POST['quote_name'],'service_id'=>$_POST['serviceGroup'],'quantity'=>$_POST['quantity'],'location'=>$_POST['location'],'service_cat'=>$_POST['service_cat'],'finish_time'=>$_POST['finish_time']);
        $quote->addCustomQuote($quote_data);
        flashMsg("Success: Your have created a custom quote !");
        redirectUrl(CreateLink(array("dashboard#tab2")));

    }

    if(!empty($_POST['bid_check']) ){
       //echo '<pre>'; print_r($_POST); die;
       $quote->addCart($_POST);
$p = new Paypal();             // initiate an instance of the class
$c = new Quote();
$payment =  new Payment();
$total =0;

$carts = $c->getCart();

//echo '<pre>'; print_r($carts); exit;
//
foreach($carts as $cart){
  $quote_id = $cart['quote_id'];
    $total += $cart['bid_amount'];
}

$datas = array(
       'user_id'=> $_SESSION['FRONTUSER']['id'],
      'quote_id'=> $quote_id,
      'quantity'=> count($carts),
        'amount'=> $total
);
//echo '<pre>'; print_r($datas); exit;
$order_id = $payment->addOrder($datas);
//echo "<pre>";print_r($order_id);die;
//echo "beofr unset"."<pre>";print_r($_SESSION);
unset($_SESSION['orderid']);
unset($_SESSION['quote_id']);
$_SESSION['orderid']=$order_id;
$_SESSION['quote_id']= $quote_id;
//echo "after unset"."<pre>";print_r($_SESSION);die;
foreach($carts as $cart){
    $orderData = array(
                'order_id'=>$order_id,
                'bid_id'=>$cart['id'],
                'amount'=>$cart['bid_amount']
    );

   $detail_id = $payment->addOrderDetails($orderData);
}
        
  $payment->confirmOrder($order_id);
 
     //echo "<pre>";print_r($detail_id);die;
        redirectUrl(CreateLink(array("dashboard#tab5")));
    }
  
}

 $payment_type=$obj->Get_User($_SESSION['FRONTUSER']['id']);
 
 $pay= $payment_type['payment_type'];

 
?>

<script>
function discardBida(id){
        $.ajax({
            url: "<?php echo SITEURL; ?>itf_ajax/index.php",
            type :"POST",
            data: "id="+id+"&itfpg=discardBida",
            dataType:"json",
            success:function(data){
                if(data.res == 'error'){
                    alert("Accepted bid can not be deleted !");

                    return false;
                }else{
                    window.location = ("<?php echo CreateLink(array('dashboard#tab9')); ?>");
                    window.location.reload(true);
                }
            }

        });


    }
    // Wait until the DOM has loaded before querying the document
    $(document).ready(function(){

        $('ul.tabs').each(function(){
            // For each set of tabs, we want to keep track of
            // which tab is active and it's associated content
            var $active, $content, $links = $(this).find('a');

            // If the location.hash matches one of the links, use that as the active tab.
            // If no match is found, use the first link as the initial active tab.
            $active = $($links.filter('[href="'+location.hash+'"]')[0] || $links[0]);
            $active.addClass('active');
            $content = $($active.attr('href'));

            // Hide the remaining content
            $links.not($active).each(function () {
                $($(this).attr('href')).hide();
            });

            // Bind the click event handler
            $(this).on('click', 'a', function(e){
                // Make the old tab inactive.
                $active.removeClass('active');
                $content.hide();

                // Update the variables with the new link and content
                $active = $(this);
                $content = $($(this).attr('href'));

                // Make the tab active.
                $active.addClass('active');
                $content.show();

                // Prevent the anchor's default click action
                e.preventDefault();
            });
        });
        var activelink = location.hash+"1";
        if(activelink!="1")
            $(activelink).click();
    });


</script>

    <script type="text/javascript">

        $(document).ready(function() {

            $.validator.addMethod("noSpace", function(value, element) {

                var resinfo = parseInt(value.indexOf(" "));

                if(resinfo == 0 && value !="") { return false; } else return true;

            }, "Space are not allowed as first string !");

            $('#info').validate({
                rules: {
                    name:{required:true, maxlength:'100', noSpace: true},
                    last_name:{required:true, maxlength:'100', noSpace: true},
                    address:{required:true, maxlength:'100', noSpace: true},
                    change_password:{minlength:8, maxlength:20, noSpace: true},
                   // payment_type:"required"

                },
                messages: {
                    name:{required:"You must fill in all of the fields !"},
                    last_name: {required:"You must fill in all of the fields !"},
                    address: {required:"You must fill in all of the fields !" },
                    //payment_type: "You must fill in all of the fields !",
                    change_password:{ required: "You must fill in all of the fields !"}

                }
            });


        });
    </script>

<div style="padding-top:25px;">
<ul class="tabs" style="border-bottom:2px #b6b6b6 solid;">
    <li><a class="#" href="#tab1" id="tab11">Dashboard</a></li>
    <li><a class="" href="#tab7" id="tab71" >My Profile</a></li>
    <li><a class="" href="#tab3" id="tab31">Quote Request</a></li>
    <li><a class="" href="#tab2" id="tab21">Enquiry Quote</a></li>
<!--    <li><a class="" href="#tab4" id="tab41">Cart</a></li>-->
    <li><a class="" href="#tab5" id="tab51">Active Quote</a></li>
    <li><a class="" href="#tab6" id="tab61">Closed Quote</a></li>
    <li><a class="" href="#tab9" id="tab91">Expired Quote</a></li>
<!--    <li><a class="" href="#tab8" id="tab81">My Transaction</a></li>-->

</ul>
<div style="display: block;" id="tab1">
    <div class="cont_info">
        <script src="http://maps.google.com/maps?file=api&v=2&key=AIzaSyDI6TNXqSv2QJ8W9J8fmMzTp0qd9U2q6WQ&sensor=false" type="text/javascript"></script>
        <div class="summary">
            <div class="summary_lft">
                <div class="summary_lft_cont">
                    <p>Customer Id:<span> <?php echo $userinfo['registration_id'];?></span></p>
                    <p>Member Since:<span> <?php echo date('d M Y',$userinfo['created_date']); ?></span></p><br>
                    <p><b>Summary of quote requested:</b></p>
                    <p>Total quote requested:<span> <?php echo $quote->getTotalQuoteByUser($_SESSION['FRONTUSER']['id']); ?></span></p>
                    <p>Total quotes fulfilled:<span> <?php echo count($closedQuotes); ?></span></p>
                </div>
            </div>
            <div class="summary_rgt">
                <div class="map">
                    <?php
                    //echo"<pre>"; print_r($userinfo); die;
                        if(!empty($userinfo['address']) and !empty($userinfo['country_name'])){

                        $myaddress = urlencode($userinfo['address'].' '.$userinfo['country_name']);
                        //here is the google api url
                        $url = "http://maps.googleapis.com/maps/api/geocode/json?address=$myaddress&sensor=false";

                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        $getmap = curl_exec($ch);
                        curl_close($ch);

                        $googlemap = json_decode($getmap);

                        //get the latitute, longitude from the json result by doing a for loop
                        foreach($googlemap->results as $res){
                            $address = $res->geometry;
                            $latlng = $address->location;
                            $formattedaddress = $res->formatted_address;
                            ?>
                      <iframe class="map" width="447" height="257" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?f=q&amp;source=s_q&amp;hl=en&amp;geocode=&amp;q=<?php echo $myaddress;?>&amp;ie=UTF8&amp;hq=&amp;hnear=<?php echo urlencode($formattedaddress);?>&amp;t=m&amp;z=14&amp;iwloc=A&amp;output=embed"></iframe>
                        <?php
                            break;
                        }
                    } else {?>
                        <img src="<?php echo SITEURL.'images/map-not-available_lg.gif';?>" width="447" height="257" >

                    <?php
                    }
                    ?>
                </div>
            </div>
        </div>

    </div>
</div>

<div style="display: block;" id="tab7">
    <div class="cont_info">
        <h3>Contact Information</h3>
        <form id="info" name="profile" method="post" action="<?php echo CreateLink(array('dashboard')); ?>" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo isset($userinfo['user_id'])?$userinfo['user_id']:''; ?>">
            <div class="sec">
                <label>First Name <span class="required">*</span> </label>
                <input name="name" type="text" value="<?php echo isset($userinfo['name'])?$userinfo['name']:''; ?>">
                <div class="clear"></div>
            </div>
            <div class="sec">
                <label>Last Name <span class="required">*</span></label>
                <input name="last_name" type="text" value="<?php echo isset($userinfo['last_name'])?$userinfo['last_name']:''; ?>">
                <div class="clear"></div>
            </div>
            <div class="sec">
                <label>Email ID <span class="required">*</span></label>
                <input name="emailid" type="text" readonly  value="<?php echo isset($userinfo['email'])?$userinfo['email']:''; ?>">
                <div class="clear"></div>
            </div>
            <div class="sec">
                <label>Change Password - to change the current password.</label>
                <input name="change_password" type="password" value="">
                <div class="clear"></div>
            </div>
            <div class="sec">
                <label>Country <span class="required">*</span></label>
                <select class="sect" name="country_code">
                    <?php foreach($countries as $country){ ?>
                        <option value="<?php echo $country['country_code'];?>" <?php if($userinfo['country_code'] == $country['country_code']){ echo"selected"; } ?>>
                            <?php echo $country['country_name'];?> (<?php echo $country['country_code'];?>)
                        </option>
                    <?php } ?>
                </select>
                <div class="clear"></div>
            </div>


            <div class="sec">
                <label>Address <span class="required">*</span></label>
                <textarea name="address"><?php echo isset($userinfo['address'])?$userinfo['address']:''; ?></textarea>
                <div class="clear"></div>
            </div>
            <div class="sec">
                <label>Postal Code</label>
                <input name="postal_code" type="text" value="<?php echo isset($userinfo['postal_code'])?$userinfo['postal_code']:''; ?>">
                <div class="clear"></div>
            </div>

<!--            <div class="sec">
                <label>Payment Option</label>
                <input type="radio" style="margin-left: 15px;" name="payment_type" value="credit_card" <?php //if($userinfo['payment_type'] == 'credit_card') { echo "checked='true'";} ?>>By Credit Card
                <input type="radio" name="payment_type" value="account" <?php //if($userinfo['payment_type'] == 'account') { echo "checked='true'";} ?> >By Account
                <div class="clear"></div>
            </div>-->

            <div class="sec">
                <label>Edit Image</label>
                <img src="<?php echo PUBLICPATH."/profile/"; ?><?php if($userinfo['profile_image']){ echo $userinfo['profile_image'];} else { echo 'no_image.jpg'; }; ?>" class="edit_mg" height="129px" width="120px">
                <div class="upld">
                    <p><input type="file" name="image" value="Upload"> </p>
                </div>
                <div class="clear"></div>
            </div>
            <div class="clear"></div>

            <div class="sec">
                <label>&nbsp;</label>
                <input type="submit" name="submit" value="update">
                <div class="clear"></div>
            </div>
        </form>
    </div>
</div>
<div style="display: none;" id="tab2" class="detail">
   <div id="quotelist" style="display: block;">
        <div style="float: right;">
            <input type="button" value="create custom quote" onclick="custom_quote()" />
        </div>
        <div class="quoe_req enquiry_list">
          <?php if(isset($finalizeData)) { ?>
            <ul>
                <?php foreach($finalizeData as $finalize){ ?>
                <a href="#itf" title="click for details" onclick="showdetails(<?php echo $finalize['id']; ?>)">
                    <li><p><?php echo $finalize['quote_name']; ?> </p><span>Created on : <?php echo date('d M Y h:i A',$finalize['create_date']); ?></span></li>
                </a>
                <?php } ?>
            </ul>
           <?php } ?>
        </div>
    </div>
    <div class="detial" id="custom_quote" style="display: none;">
        <form id="detail" name="custom_quote_form" method="post" enctype="multipart/form-data" action="#" >
        <div class="detial_lft">
            <h3>Create Custom Quote</h3>

                <div class="mat">
                    <label>Quote Title <span class="required">*</span></label>
                    <input type="text" name="quote_name">
                    <div class="clear"></div>
                </div>

<!--                <div class="mat">
                    <label>Product Name<span class="required">*</span></label>
                    <input type="text" name="product_name">
                    <div class="clear"></div>
                </div>-->

                <div class="mat">
                        <label>Quote Description <span class="required">*</span></label>
                    <textarea name="product_desc"></textarea>
                    <div class="clear"></div>
                </div>


                <div class="mat">
                    <label>Special Requirment</label>
                    <textarea name="special_req"></textarea>
                    <div class="clear"></div>
                </div>

                <div class="mat">
                    <label>Service Location <span class="required">*</span></label>
                    <?php echo Html::ComboBox("location",Html::CovertSingleArray($areas,"id","name"),"",array('class'=>'upload'),"Select Location"); ?>
                    <div class="clear"></div>
                </div>

                <div class="mat">
                <label>Bid Duration Time <span class="required">*</span></label>
                    <input type="text" id="dateinput" name="finish_time">
                    <div class="clear"></div>
                </div>

        </div>
        <div class="detial_rgt">

                <div class="cate">
                   <h3>Service Category <span class="required">*</span></h3>
                    <?php //echo Html::ComboBox("category",Html::CovertSingleArray($categories,"id","catname"),"",array('class'=>'upload'),"Select Category"); ?>
 <select class="upload" name="serviceGroup[]" id="servicecategory" multiple>
                    <?php foreach($services as $cat){ ?>
                        <?php if(count($cat['subcat']) > 0){ ?>
                            <optgroup label="<?php echo $cat['catname'] ?>" style="padding-top: 10px;">
                                <?php foreach($cat['subcat'] as $subcat){ ?>
                                    <?php if(count($subcat['subcat']) > 0){ ?>
                                        <optgroup label="<?php echo $subcat['catname'] ?>" style="padding-left: 10px;">
                                            <?php foreach($subcat['subcat'] as $subsubcat){ ?>
                                                <option value="<?php echo $subsubcat['id'] ?>"><?php echo $subsubcat['catname'] ?></option>
                                            <?php } ?>
                                        </optgroup>

                                    <?php } else { ?>
                                        <option value="<?php echo $subcat['id'] ?>"><?php echo $subcat['catname'] ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </optgroup>

                        <?php } else { ?>
                            <option value="<?php echo $cat['id'] ?>"><?php echo $cat['catname'] ?></option>
                        <?php } ?>
                    <?php } ?>

                </select>
                   
                    <div class="clear"></div>
                </div>
                <div class="clear"></div>
                <div class="cate">
                    <h3 class="cate">Product Image</h3>
                    <input type="file" name="product_image" value="upload">
                </div>
                <div class="clear"></div>
                <div class="cate">
                    <h3 class="cate">Upload file</h3>
                    <input type="file" name="attachment" value="upload">
                </div>
            <div class="clear"></div>
                <div class="cate">
                    <input type="submit" value="submit" name="submit">
                    <input type="button" class="backbutton" id="custom_back" value="Back">
                </div>

        </div>
        </form>
        <div class="clear"></div>
    </div>
    <div class="enq_cont" id="quote_detail" style="display: none;">

    </div>
</div>
<div style="display: block;" id="tab3" class="detail">
    <div class="quoe_req">
        <h3>Quote Request</h3>
        <Ul>
           <?php if(isset($quote_info)){ if(count($quote_info) > 0) { ?>

           <?php foreach($quote_info as $quote){ ?>
            <li>
                <div class="quote_detail">
                    <div class="quote_detail_lft">
                        <?php if(!empty($quote['product']['main_image']) and file_exists(PUBLICFILE."products/".$quote['product']['main_image'])) { ?>
                        <img src="<?php echo PUBLICPATH."products/".$quote['product']['main_image']; ?>" alt="">
                        <?php } else { ?>
                            <img src="<?php echo PUBLICPATH."products/noImageProduct.jpg"; ?>" alt=""></a>
                        <?php } ?>
                        <p><span><?php echo $quote['product']['name']; ?></span><br>
                            <?php echo $quote['product']['logn_desc']; ?>
                        </p>
                    </div>
                    <div class="quote_detail_rgt">
                        <form id="quantity" name="frmquantity" method="post" enctype="multipart/form-data" action="<?php echo CreateLink(array('dashboard')); ?>">
                            <input type="hidden" name="id" value="<?php echo $quote['product']['id']; ?>" >
                            <label>Quantity</label>
                            <input type="text" name="quantity" value="<?php echo $quote['quantity']; ?>" id="qty">
                            <a href="#itf" onclick="removeQuote();"><img src="<?php echo TemplateUrl();?>images/close_btn.png"> </a>
                        </form>
                    </div>
                </div>
            </li>
           <?php } ?>


        </ul>
        <form id="request" name="frmrequest" action="<?php echo CreateLink(array('dashboard')); ?>" method="post" enctype="multipart/form-data">

            <div class="bid">
                <label>Quote Name <span class="required">*</span></label>
                <input name="quote_name" type="text">

                <div class="clear"></div>
            </div>
            <div class="bid">
                <label>Bid/Time duration <span class="required">*</span></label>
                <input name="finish_time" type="text" class="dte" id="date">
                <div class="clear"></div>
            </div>
            <div class="bid desc">
                <label>Quote Detail <span class="required">*</span></label>
                <textarea name="quote_desc"></textarea>
                <div class="clear"></div>
            </div>
            <div class="bid">
                <label>Delivery Area <span class="required">*</span></label>
                <?php echo Html::ComboBox("location",Html::CovertSingleArray($areas,"id","name"),"",array(),"Select Location"); ?>
                <div class="clear"></div>
            </div>
            <div class="bid">
                <a href="<?php echo CreateLink(array("product")); ?>" class="button">Back to Products</a>
                <input type="button" value="Discard" onclick="discardQuote();">
                <input type="submit"  name="submit" value="Finalize Enquiry">
            </div>
        </form>

        <?php } else { ?>
            <p style="text-align: center;">No Product Available !</p>
        <?php } } ?>
    </div>

</div>
<div style="display:none;" id="tab4">
    <?php if(count($carts) > 0) { 
        
        
        ?>
    <div class="cart">
        <div class="cart_cont">
            <div class="suply">
                <p>Supplier ID</p>
            </div>
            <div class="quote_name">
                <p>Quote Title</p>
            </div>
            <div class="unit">
                <p>Bid Price</p>
            </div>
            <div class="clear"></div>
        </div>
        <div class="cart_cont_txt">
            <ul>

                <?php foreach($carts as $cart) { 
                    
                 
                    $str .=$cart['supplier_id'] . ",";
                     $str1 .=$stieinfo['currency_prefix'].$cart['bid_amount'] . ",";
         
             
                    ?>
                <li>
                    <div class="cart_cont_lft">
                        <p><?php echo $cart['supplier_id']; ?></p>
                    </div>
                    <div class="cart_cont_mid">
<!--                        <p><?php echo $cart['quote_name']; ?><br><span><?php if($cart['quote_desc']){ echo "(".WordLimit($cart['quote_desc']).")";} ?></span></p>-->
                    <p><?php echo $cart['quote_name']; ?><br><span><?php if($cart['quote_desc']){ echo "(".$cart['quote_desc'].")";} ?></span></p>
                    </div>
                    <div class="cart_cont_rgt">
                        <p><?php echo currency($cart['bid_amount']); ?><span class="close"><a href="javascript:removeCart(<?php echo $cart['id']; ?>);"><img src="<?php echo TemplateUrl();?>images/close_btn.png"> </a></span></p>
                    </div>
                <li>
                <?php } 
                
         $_SESSION['supplierid']=substr($str, 0, (strlen($str)-1));
          $_SESSION['bidamount']=substr($str1, 0, (strlen($str1)-1));
      
                ?>
            </ul>

            <div class="clear"></div>
        </div>
     
        <div class="grand">
  
            <h3>Grand Total: <?php echo Currency($quoteobj->getTotalPrice()); ?></h3>
               <?php //if($pay=="credit_card"){?>
            
            
            
                <a href="<?php echo CreateLink(array("payment")); ?>" >Check Out</a>
             <?php //} else { 
               

           
                 ?>
               
<!--                 <a href="javascript:adminNotif();">Check Out</a>-->
                  <?php //} ?>
          
    
        </div>
          
    </div>
    <?php } else { ?>
        <p style="text-align: center; margin-top: 50px;">Your cart is empty !</p>
    <?php } ?>
</div>
<div style="display: none;" id="tab5" class="detail">
    <div class="active_quote" id="quote_active">
        <?php if(count($activeQuotes) > 0 ) { ?>
        <ul>
            <li>
                <div class="pro_name_lft">
                    <p><b>Quote Title</b></p>
                </div>
                <div class="pro_name_second">
                    <p><b>Bids By </b></p>
                </div>
                <div class="pro_name_third">
                    <p><b>Status</b></p>
                </div>
                <div class="pro_name_rgt">
                    <p><b>Delivery Location</b></p>
                </div>
                <div class="clear"></div>
            </li>


            <?php
            if($activeQuotes)
                {  
                foreach($activeQuotes as $quotes) 
                    { $bidders = $quoteobj->getBidsByQuotes($quotes['id']);
                    ?>
            <li>
                 <?php foreach($bidders as $bidder) {
                     ?>
                <div class="pro_name_lft">
                    <a href="#itf" title="click for details" onclick="showActiveQuotes(<?php echo $quotes['id']; ?>,<?php echo $bidder['user_id']; ?>)">
                    <p><span><?php echo $quotes['quote_name']; ?></span></p>
                    <p><?php echo $quotes['quote_desc']; ?></p>
<!--                    <p><?php echo WordLimit($quotes['quote_desc']); ?></p>-->
                    </a>
                </div>
                <div class="pro_name_second">
                    <p>
                   
                    <span><?php echo $bidder['supplier_id']; ?></span>
                  
                    </p>
                </div>
                <div class="pro_name_third">
                    <p><span><?php echo $quoteobj->getQuoteStatus($quotes['quote_status']); ?></span></p>
            
                </div>
                <div class="pro_name_rgt">
                    <p><span><?php echo $quotes['location_name']; ?></span></p>
                </div>
                <div class="clear"></div>
                  <?php } ?>
            </li>
            <?php } } ?>

        </ul>
        <?php } else { ?>
            <p style="text-align: center; margin-top: 50px;">No Active Quotes Available !</p>
        <?php } ?>
    </div>
    <div id="active_quotes"></div>
</div>
<div style="display:none;" id="tab6">
    <div class="active_quote close_quote">
        <?php if(count($closedQuotes) >0 ){ ?>
        <ul>
            <li>
                <div class="pro_name2_lft">
                    <p><b>Quote Title</b></p>
                </div>
                <div class="pro_name2_mid">
                    <p><b>Supplier Review</b></p>
                </div>
                <div class="clear"></div>
            </li>
            <?php if($closedQuotes){ foreach($closedQuotes as $closedQuote) {   $reviews = $quoteobj->getSupplierReviews($closedQuote['id']); ?>
                <li>
                    <div class="pro_name2_lft">
                        <p><span><?php echo $closedQuote['quote_name']; ?></span></p>
                        <p><?php echo $closedQuote['quote_desc']; ?></p>
<!--                        <p><?php echo WordLimit($closedQuote['quote_desc']); ?></p>-->
                    </div>

                    <div class="pro_name2_mid">
                        <?php if(count($reviews) >0 ) { ?>
                            <?php foreach($reviews as $review){ ?>
                        <p><?php echo $review['review_text'].'<br> <span> by '.$review['registration_id'].'</span>'; ?></p>
<!--                                <p><?php echo WordLimit($review['review_text']).'<br> <span> by '.$review['registration_id'].'</span>'; ?></p>-->
                            <?php }?>
                        <?php } else { ?>
                            <p>No review !</p>
                        <?php } ?>
                        <div class="clear"></div>
                    </div>
                    <div class="pro_name2_rgt2">
                        <p><a href="<?Php echo CreateLink(array("ajax",'quote_id'=>$closedQuote['id']));?>" class="review">write review</a></p>
                    </div>
                    <div class="clear"></div>
                </li>
            <?php }  } ?>
        </ul>
        <?php } else { ?>
            <p style="text-align: center; margin-top: 50px;">No closed quote available ! </p>
        <?php } ?>

    </div>
</div>
<div style="display:none;" id="tab9">
    <div class="active_quote close_quote">
        <?php if(isset($expiredQuotes) and count($expiredQuotes) >0 ){ ?>
            <ul>
                <li>
                    <div class="pro_name2_lft">
                        <p><b>Quote Title</b></p>
                    </div>
                    <div class="pro_name2_mid">
                        <p><b>Status</b></p>
                    </div>
                     <div class="bid_rgt">
                    <p><b>Discard</b></p>
                </div>
                    <div class="clear"></div>
                </li>
                <?php foreach($expiredQuotes as $expiredQuote) {   ?>
                    <li>
                        <div class="pro_name2_lft">
                            <p><span><?php echo $expiredQuote['quote_name']; ?></span></p>
<!--                            <p><?php echo WordLimit($expiredQuote['quote_desc']); ?></p>-->
                             <p><?php echo $expiredQuote['quote_desc']; ?></p>
                            
                        </div>
                                   <div class="bid_rgt">
  <p><span><a href="#itf" onclick="discardBida(<?php echo $expiredQuote['id']; ?>)"><img alt="close" src="<?php echo TemplateUrl(); ?>/images/close_btn.png"></a> </span></p>
                                   </div>                     
  <div class="pro_name2_mid">
                            <span style="color: #ff0000; font-size: 14px !important; margin-left: 10px;">Expired</span>
                            <div class="clear"></div>
                        </div>

                        <div class="clear"></div>
                    </li>
                <?php } ?>
            </ul>
        <?php } else { ?>
            <p style="text-align: center; margin-top: 50px;">No expired quote available ! </p>
        <?php } ?>

    </div>
</div>
<div style="display:none;" id="tab8">
    <div class="transaction">
        <?php if(count($transactions) >0 ){ ?>
            <table class="tbltransaction" cellspacing="0" cellpadding="0" >
               <tr>
                   <th>Quote Name</th>
                   <th>Amount</th>
                   <th>No. of Bids</th>
                   <th>Description</th>
                   <th>Transaction Id</th>
                   <th>Payment Status</th>
               </tr>

            <?php foreach($transactions as $transaction) { ?>
                <tr>
                    <td><?php echo $transaction['quote_name']; ?></td>
                    <td><?php echo Currency($transaction['amount']); ?></td>
                    <td><?php echo $transaction['quantity']; ?></td>
<!--                    <td><?php echo WordLimit($transaction['quote_desc'],8); ?></td>-->
                     <td><?php echo $transaction['quote_desc']; ?></td>
                    <td><?php echo isset($transaction['txn_id'])?$transaction['txn_id']:'NA'; ?></td>
                    <td><?php echo isset($transaction['payment_status'])?$transaction['payment_status']:'Payment Pending'; ?></td>
                </tr>


            <?php } ?>

            </table>
        <?php } else { ?>
            <p style="text-align: center; margin-top: 50px;">No transaction available ! </p>
        <?php } ?>

    </div>
</div>
</div>
<script>
    function removeQuote(){
        $.ajax({
            url: "<?php echo SITEURL; ?>itf_ajax/index.php",
            type :"POST",
            data: $('#quantity').serialize() +"&itfpg=quote",
            success:function(msg){
                window.location = ("<?php //echo CreateLink(array('dashboard#tab3')); ?>");
                window.location.reload(true);
            }
        });
        return false;
    }
//     function paydata(suppid,bidam){
//     
//
//        $.ajax({
//            url: "<?php echo CreateLink(array("payment")); ?>",
//            type :"POST",
//            data: "supplierid="+suppid+"&bidamount="+bidam,
//            success:function(msg){
//                window.location = ("<?php echo CreateLink(array('dashboard#tab4')); ?>");
//                window.location.reload(true);
//            }
//        });
//    }
    
    function removeCart(id){
        
        $.ajax({
            url: "<?php echo SITEURL; ?>itf_ajax/index.php",
            type :"POST",
            data: "id="+id+"&itfpg=cart",
            
            success:function(msg){
                window.location = ("<?php echo CreateLink(array('dashboard#tab4')); ?>");
                window.location.reload(true);
            }
        });
    }
    
    
    function adminNotif(){
       
        $.ajax({
            url: "<?php echo SITEURL; ?>itf_ajax/index.php",
            type :"POST",
            data: "user=abc",
          
            success:function(msg){
                window.location = ("<?php echo CreateLink(array('dashboard')); ?>");
                window.location.reload(true);
                  alert("Your cart notification goes to admin");
              
            }
        });
    }

    function discardQuote(){
        $.ajax({
            url: "<?php echo SITEURL; ?>itf_ajax/index.php",
            type :"POST",
            data: $('#request').serialize() +"&itfpg=discard",
            success:function(msg){
                window.location.reload(true);
            }
        });

    }

    function showActiveQuotes(id,uid){

        $.ajax({
            url: "<?php echo SITEURL; ?>itf_ajax/work_area.php",
            type :"POST",
            data: "id="+id+"&uid="+uid,
            success:function(msg){
                $("#quote_active").hide();
                $("#active_quotes").html(msg);
                $("#active_quotes").show();
            }
        });
    }

    function showdetails(id){
        $.ajax({
            url: "<?php echo SITEURL; ?>itf_ajax/quote.php",
            type :"POST",
            data: "id="+id,
            success:function(msg){
                $("#quotelist").hide();
                $("#quote_detail").html(msg);
                $("#quote_detail").show();
            }
        });
    }

    function custom_quote(){
        $("#quotelist").hide();
        $("#custom_quote").show();
    }



</script>
<script type="text/javascript" src="http://code.jquery.com/ui/1.10.3/jquery-ui.min.js"></script>
<script language="javascript" src="<?php echo TemplateUrl();?>js/jquery.validate.js"></script>

<script type="text/javascript" src="<?php echo SITEURL; ?>/site/component/com_dashboard/assests/jquery-ui-timepicker-addon.js"></script>

<script type="text/javascript">

    $(function(){

        $('#date').datetimepicker({
            minDateTime:new Date(),
            timeFormat: 'HH:mm:ss'
        });

        $('#dateinput').datetimepicker({
            minDateTime:new Date(),
            timeFormat: 'HH:mm:ss'
        });

        $('#custom_back').click(function() {
            $("#custom_quote").hide();
            $("#quotelist").show();
        });

        $('#request').validate({
            rules: {
                quote_name:"required",
                quote_desc:"required",
                quantity:{required:true,number:true},
                price:{required:true,number:true},
                finish_time:{required:true, date:true},
                location:"required",
                category:"required"
            },
            messages: {

            }
        });

        $('#detail').validate({
            rules: {
                quote_name:"required",
                quote_desc:"required",
                'serviceGroup[]':"required",
                product_desc:"required",
                quantity:{number:true},
                price:{required:true,number:true},
                finish_time:{required:true, date:true},
                location:"required",
                category:"required",
                attachment:{ accept: "doc|xls|pdf|png|jpe?g|gif" },
                product_image: { accept: "png|jpe?g|gif" }
            },
            messages: {
                quantity:{number:'Accept only numeric value !'},
                attachment:{accept:"File must be PDF, Excel, DOC, JPG, XLSX, GIF or PNG !"},
                product_image:{accept:"File must be JPG, GIF or PNG ! &nbsp;&nbsp;&nbsp;&nbsp;"},
                 'serviceGroup[]':"Please select at least one service category !"
             
            }
        });
    });

</script>