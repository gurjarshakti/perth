<?php 
$item_per_page =36;
require('../itfconfig.php');
$page_number = filter_var($_POST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH);

//throw HTTP error if page number is not valid
if(!is_numeric($page_number)){
	header('HTTP/1.1 500 Invalid page number!');
	exit();
}

//get current starting point of records
$position = ($page_number * $item_per_page);

$objCategory = new Category();


if(isset($_POST['sort']) && !empty($_POST['sort'])){

$InfoData = $objCategory->getSortByCategory($id,$_POST['sort']);

}else{
//$InfoData = $objCategory->getCategories($parent);
$InfoData = $objCategory->getCategoriesAjaxloader($position,$item_per_page);
}

		



/*
//Limit our results within a specified range. 
$results = mysql_query("SELECT * FROM itf_category where parent='0' ORDER BY id DESC LIMIT $position, $item_per_page ");
//$results->execute(); //Execute prepared Query
//$results->bind_result($id, $name, $message); //bind variables to prepared statement
echo "SELECT * FROM itf_category where parent='0' ORDER BY id DESC LIMIT $position, $item_per_page" ;
//output results from database
echo '<ul class="page_result">';

while($sql = mysql_fetch_array($results)){ //fetch values
	echo '<li id="item_'.$sql['id'].'"><span class="page_name">'.$sql['id'].') '.$sql['catname'].'</span><span class="page_message">'.$sql['catname'].'</span></li>';	
}
echo '</ul>';
print_r($sql);*/
?>
<?php foreach ($InfoData as $info){  ?>
<div class="all_product_product">
<ul>
<li>
<?php if(count($info['subcat']) > 0) { ?>
<a href="<?php echo CreateLink(array('product', $info['slug'],'parent'=>$info['id'])); ?>">
<?php if(!empty($info['image']) and file_exists(PUBLICFILE."categories/".$info['image'])) { ?>
<img src="<?php echo PUBLICPATH."categories/".$info['image']; ?>" style="border:solid 4px #fff"; alt="" height="209px;" width="218px;"></a>
<?php } else { ?>
<img src="<?php echo PUBLICPATH."categories/noimage.jpg"; ?>" style="border:solid 4px #fff"; alt="" height="209px;" width="218px;"></a>
<?php } ?>
<?php } else { ?>
<a href="<?php echo CreateLink(array('product',$info['slug'],'itemid'=>'catdetail','id'=>$info['id'])); ?>">
<?php if(!empty($info['image']) and file_exists(PUBLICFILE."categories/".$info['image'])) { ?>
<img src="<?php echo PUBLICPATH."categories/".$info['image']; ?>" style="border:solid 4px #fff"; alt="" height="209px;" width="218px;"></a>
<?php } else { ?>
<img src="<?php echo PUBLICPATH."categories/noimage.jpg"; ?>" style="border:solid 4px #fff"; alt="" height="209px;" width="218px;"></a>
<?php } ?>
<?php } ?>

<h1><?php echo $info['catname']; ?></h1>                    
<?php if(count($info['subcat']) > 0) { ?>
<a href="<?php echo CreateLink(array('product',$info['slug'],'parent'=>$info['id'])); ?>">
<input name="View Detail" type="button" value="View Detail" class="detail"></a>
<?php } else { ?>
<a href="<?php echo CreateLink(array('product',$info['slug'],'itemid'=>'catdetail','id'=>$info['id'])); ?>">
<input name="View Detail" type="button" value="View Detail" class="detail"></a>
<?php } ?>
</li>
</ul>
</div>
<?php } ?>