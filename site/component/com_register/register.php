<script src="<?php echo TemplateUrl();?>js/jquery.validate.js"></script>
<?php

if(!empty($_SESSION['FRONTUSER']))
{
    redirectUrl(CreateLink(array("dashboard")));
}

$obj = new User();
$currentpage = isset($_GET['itfpage'])?$_GET['itfpage']:'';
$actions = isset($_REQUEST['itemid'])?$_REQUEST['itemid']:'default';
$page = BASEPATHS."/site/component/com_".$currentpage."/".$actions.".php";

if(file_exists($page))
   include_once($page);
else
   include_once(BASEPATHS.'/site/component/com_404/404.php');
	
?>