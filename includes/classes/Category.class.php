<?php

class Category 

{

	

	function __construct()

	{

		global $itfmysql;

		$this->dbcon=$itfmysql;

	}

	

	function Get_Category($id)
	{
		$sql="select * from itf_category where id='".$id."'";
		return $this->dbcon->Query($sql);

	}

	

	function admin_addCategory($datas)

	{
      $datas["slug"]=empty($datas["slug"])?Html::seoUrl($datas["catname"]):Html::seoUrl($datas["slug"]);
        if(!empty($_FILES['image']['name'])){

            if(isset($datas['id'])){

                $imagenames = $this->upload($datas['id']);

            } else{

                $imagenames = $this->upload();

            }

            $datas['image'] = $imagenames['image'];

        }

		unset($datas['id']);

		return $this->dbcon->Insert('itf_category',$datas);



	}





    function upload($id)

    {



        if(isset($id) and !empty($id))

        {

            $info = $this->CheckCategory($id);

            if(!empty($_FILES['image']['name'])){

                @unlink(PUBLICFILE."categories/".$info['image']);

            }



        }

        if(isset($_FILES['image']['name']) and !empty($_FILES['image']['name']))

        {

            $fimgname="plucka_".rand();

            $objimage= new ITFImageResize();

            $objimage->load($_FILES['image']['tmp_name']);

            $objimage->save(PUBLICFILE."categories/".$fimgname);

            $productimagename = $objimage->createnames;



            $datas['image'] = $productimagename;

        }



        return $datas;

    }





	function admin_updateCategory($datas)

	{

        $imagenames = $this->upload($datas['id']);

        if(!empty($_FILES['image']['name'])){

            $datas['image'] = $imagenames['image'];

        }

		$condition = array('id'=>$datas['id']);
		$datas["slug"]=empty($datas["slug"])?Html::seoUrl($datas["catname"]):Html::seoUrl($datas["slug"]);

		unset($datas['id']);

		return $this->dbcon->Modify('itf_category',$datas,$condition);

	}



	



	function cat_deleteAdmin($id)

	{

        if(isset($id) and !empty($id))

        {

            $info = $this->CheckCategory($id);

            if(!empty($_FILES['image']['name'])){

                @unlink(PUBLICFILE."categories/".$info['image']);

            }



        }

        $sql="delete from itf_category where id in(".$id.")";

		$this->dbcon->Query($sql);



		return $this->dbcon->querycounter;

	}

	

	function cat_deleteSeller($id)

	{

        if(isset($id) and !empty($id))

        {

            $info = $this->CheckCategorySeller($id);

            if(!empty($_FILES['image']['name'])){

                @unlink(PUBLICFILE."categories/".$info['image']);

            }



        }

        $sql="delete from itf_category where id in(".$id.") and seller_id='".$_SESSION['FRONTUSER']['id']."'"; 

		$this->dbcon->Query($sql);



		return $this->dbcon->querycounter;

	}

	

	

	

	

	function showAllCategory()

	{

		$sql="select *  from itf_category where status='1' order by catname";



		return $this->dbcon->FetchAllResults($sql);

	}

	





	function ShowAllCategorySearch($txtsearch)

	{

		$sql="select * from itf_category where  name like ( '%".$this->dbcon->EscapeString($txtsearch)."%')";

		return $this->dbcon->FetchAllResults($sql);

	}

	

	function CheckCategory($UsId)

	{

		$sql="select U.* from itf_category U where U.id='".$UsId."'";

	 	return $this->dbcon->Query($sql);

	}

		function CheckCategorySeller($UsId)

	{

		$sql="select U.* from itf_category U where U.id='".$UsId."' and seller_id='".$_SESSION['FRONTUSER']['id']."' ";

	 	return $this->dbcon->Query($sql);

	}



    function CheckCategoryByName($cat_name ,$parent = 0)

    {

        $sql="select C.* from itf_category C where C.catname = '".$this->dbcon->EscapeString($cat_name)."' and C.parent = '".$parent."'";

        return $this->dbcon->Query($sql);

    }

			

	

	//Function for change status	

	function PublishBlock($ids)

	{	



		$infos=$this->CheckCategory($ids);

		if($infos['status']=='1')

			$datas=array('status'=>'0');

		else

			$datas=array('status'=>'1');

		

		$condition = array('id'=>$ids);

		$this->dbcon->Modify('itf_category',$datas,$condition);

		

		return ($infos['status']=='1')?"0":"1";



	}



	//front end============================================================
     
	 
//	  function getAllCategoryFrontlist($parent)
//
//    {
//
//        $sql="select *  from itf_category where parent ='".$parent."' and status='1' order by catname";
//
//        $res = $this->dbcon->FetchAllResults($sql);
//
//	}


    function getAllCategoryFront($parent=0,$limit=10000)

    {

        $sql="select *  from itf_category where parent ='".$parent."' and status='1' order by id asc limit ".$limit." ";

        $res = $this->dbcon->FetchAllResults($sql);



        if(count($res) > 0){

            foreach($res as &$r)

            {

                $re = $this->getCategories($r['id']);

                $r["subcat"] = $re;



            }

        }

        return $res;

    }



    // Get All categories and subcategories



    function getCategories($parent=0)
    {
        $sql="select *  from itf_category where parent ='".$parent."' and status=1 order by catname";
        $res = $this->dbcon->FetchAllResults($sql);



        if(count($res) > 0){

            foreach($res as &$r)

            {

                $re = $this->getCategories($r['id']);

                $r["subcat"] = $re;



            }

        }

            return $res;



    }



    function getCategoriesAjaxloader($position,$item_per_page)
    {
        $sql="select *  from itf_category where parent ='0' and status=1 ORDER BY catname ASC LIMIT $position, $item_per_page ";
        $res = $this->dbcon->FetchAllResults($sql);
        if(count($res) > 0){
            foreach($res as &$r)
            {
                $re = $this->getCategories($r['id']);
                $r["subcat"] = $re;



            }

        }

            return $res;



    }

	 function getCategoriesAdmin($parent=0)

    {

       $sql="select *  from itf_category where parent ='".$parent."' order by catname";

        $res = $this->dbcon->FetchAllResults($sql);



        if(count($res) > 0){

            foreach($res as &$r)

            {

                $re = $this->getCategoriesAdmin($r['id']);

                $r["subcat"] = $re;



            }

        }

            return $res;



    }

	

	 function getCategoriesSeller($parent=0)

    {

        $sql="select *  from itf_category where  seller_id = '".$_SESSION['FRONTUSER']['id']."' order by catname ";

        $res = $this->dbcon->FetchAllResults($sql);

    // echo "<pre>"; print_r($res);

      //  if(count($res) > 0){

//            xforeach($res as &$r)

//            {

//                $re = $this->getCategoriesSeller($r['id']);

//                $r["subcat"] = $re;

//

//            }

//        }

            return $res;



    }

    

    function getSupCategories()

    {

        $sql="select *  from itf_category where  status='1' order by catname";

        $res = $this->dbcon->FetchAllResults($sql);



        if(count($res) > 0){

            foreach($res as &$r)

            {

                $re = $this->getCategories($r['id']);

                $r["subcat"] = $re;



            }

        }

            return $res;



    }



    function getBreadcum($catid=0,$level=0){

        $res = $this->CheckCategory($catid);

        if(isset($res['id']) and $level == 0){

            return $this->getBreadcum($res['parent'],$level+1)."/ ".$res['catname'];

        }elseif(isset($res['id'])){

            return $this->getBreadcum($res['parent'],$level+1).'/ <a href="'.CreateLink(array('product','id'=>$res['id'])).'">'.$res["catname"].'</a> ';

        }else{



            return "";

        }

    }



    function getBreadcumProduct($catid=0,$level=0){

        $res = $this->CheckCategory($catid);

        if(isset($res['id']) and $level == 0){

            return $this->getBreadcum($res['parent'],$level+1).'/ <a href="'.CreateLink(array('product','id'=>$res['id'])).'">'.$res["catname"].'</a> ';

        }elseif(isset($res['id'])){

            return $this->getBreadcum($res['parent'],$level+1).'/ <a href="'.CreateLink(array('product','parent'=>$res['id'])).'">'.$res["catname"].'</a> ';

        }else{



            return "";

        }

    }



    

    function getQuoteProductParentHierarchy($catid=0,$level=0){

        $res = $this->CheckCategory($catid);

        if(isset($res['id']) and $level == 0){

            return $this->getParentHierarchy($res['parent'],$level+1).'> <a href="'.CreateLink(array('product','itemid'=>'catdetail','id'=>$res['id'])).'">'.$res["catname"].'</a> ';

        }elseif(isset($res['id'])){

            return $this->getParentHierarchy($res['parent'],$level+1).'> <a href="'.CreateLink(array('product','parent'=>$res['id'])).'">'.$res["catname"].'</a> ';

        }else{



            return "";

        }

    }



     function getParentHierarchy($catid=0,$level=0){

        $res = $this->CheckCategory($catid);

        if(isset($res['id']) and $level == 0){

            return $this->getParentHierarchy($res['parent'],$level+1)."> ".$res['catname'];

        }elseif(isset($res['id'])){

            return $this->getParentHierarchy($res['parent'],$level+1).'> <a href="'.CreateLink(array('product','parent'=>$res['id'])).'">'.$res["catname"].'</a> ';

        }else{



            return "";

        }

    }

    

    

    

    

    function  getAllCategories()

    {

        $sql="select *  from itf_category where status='1' order by id desc limit 32 ";



        return $this->dbcon->FetchAllResults($sql);

    }



    function showCategoriesList($parent=0)

    {

        $categories = $this->getCategories($parent);

        $catlist = array();

        foreach($categories as $key=>$category)

        {

            $catlist[$category["id"]] = $category["catname"];

            if(count($category["subcat"]) > 0){

                foreach($category["subcat"] as $subcat)

                {

                    $catlist[$subcat["id"]] = $category["catname"].">>".$subcat["catname"];

                    if(count($subcat["subcat"]) > 0){

                        foreach($subcat["subcat"] as $subsubcat)

                        {
							
                            $catlist[$subsubcat["id"]] = $category["catname"].">>".$subcat["catname"].">>".$subsubcat["catname"];

                        }

                    }

                }



            }

        }



        return $catlist;

    }



    function showCategoriesListFront($parent=0)

    {

        $categories = $this->getCategories($parent);

        $catlist = array();

        foreach($categories as $key=>$category)

        {

            $catlist[] = array("id"=>$category["id"],"catname"=>$category["catname"],"status"=>$category["status"]);

            if(count($category["subcat"]) > 0){

                foreach($category["subcat"] as $subcat)

                {

                    $catlist[] = array("id"=>$subcat["id"],"catname"=>$category["catname"].">>".$subcat["catname"],"status"=>$subcat["status"]);

                    if(count($subcat["subcat"]) > 0){

                        foreach($subcat["subcat"] as $subsubcat)

                        {

                            $catlist[] = array("id"=>$subsubcat["id"],"catname"=>$category["catname"].">>".$subcat["catname"].">>".$subsubcat["catname"],"status"=>$subsubcat["status"]);

                        }

                    }

                }



            }

        }



        return $catlist;

    }







 function showCategoriesListAdmin($parent=0)

    {

        $categories = $this->getCategoriesAdmin($parent);

        $catlist = array();

        foreach($categories as $key=>$category)

        {

            $catlist[] = array("id"=>$category["id"],"catname"=>$category["catname"],"status"=>$category["status"]);

            if(count($category["subcat"]) > 0){

                foreach($category["subcat"] as $subcat)

                {

                    $catlist[] = array("id"=>$subcat["id"],"catname"=>$category["catname"].">>".$subcat["catname"],"status"=>$subcat["status"]);

                    if(count($subcat["subcat"]) > 0){

                        foreach($subcat["subcat"] as $subsubcat)

                        {

                            $catlist[] = array("id"=>$subsubcat["id"],"catname"=>$category["catname"].">>".$subcat["catname"].">>".$subsubcat["catname"],"status"=>$subsubcat["status"]);

                        }

                    }

                }



            }

        }



        return $catlist;

    }







 function showCategoriesListSeller($parent=0)

    {

        $categories = $this->getCategoriesSeller($parent);

        $catlist = array();

        foreach($categories as $key=>$category)

        {

            $catlist[] = array("id"=>$category["id"],"catname"=>$category["catname"],"status"=>$category["status"]);

            if(count($category["subcat"]) > 0){

                foreach($category["subcat"] as $subcat)

                {

                    $catlist[] = array("id"=>$subcat["id"],"catname"=>$category["catname"].">>".$subcat["catname"],"status"=>$subcat["status"]);

                    if(count($subcat["subcat"]) > 0){

                        foreach($subcat["subcat"] as $subsubcat)

                        {

                            $catlist[] = array("id"=>$subsubcat["id"],"catname"=>$category["catname"].">>".$subcat["catname"].">>".$subsubcat["catname"],"status"=>$subsubcat["status"]);

                        }

                    }

                }



            }

        }



        return $catlist;

    }









    function showCountProduct($id)

    {

        $sql="select *  from itf_product where category_id ='".$id."' and status='1'";

        $res = $this->dbcon->FetchAllResults($sql);



        return count($res);

    }

	

	

    function getSortByCategory($catid,$sort = 'id')

    {

        if($sort == 'id'){

            $order_by = 'where P.parent=0 order by P.'.$sort.' desc';

        }else{

            $order_by = 'where P.parent=0 order by P.'.$sort.' asc';

        }



       $sql="select P.* from itf_category P  ".$order_by ;



        $res= $this->dbcon->FetchAllResults($sql);

	        if(count($res) > 0){

            foreach($res as &$r)

            {

                $re = $this->getCategories($r['id']);

                $r["subcat"] = $re;



            }

        }

            return $res;

		

		

    }

	

    function getSortBySubCategory($catid,$sort = 'id')

    {

        if($sort == 'id'){

            $order_by = 'where P.parent='.$catid.'  order by P.'.$sort.' desc';

        }else{

            $order_by = 'where P.parent='.$catid.' order by P.'.$sort.' asc';

        }



      $sql="select P.* from itf_category P  ".$order_by ;



        $res= $this->dbcon->FetchAllResults($sql);

	        if(count($res) > 0){

            foreach($res as &$r)

            {

                $re = $this->getCategories($r['id']);

                $r["subcat"] = $re;



            }

        }

            return $res;

		

		

    }


	function getAllActiveCat($parentid=0) {
	
		$sql="select *  from itf_category where parent='".$parentid."' and status=1 order by id ASC";
		return $this->dbcon->FetchAllResults($sql);
	
	} 
	
		function getAllActiveSubCat($parentid) {
	
		$sql="select *  from itf_category where parent='".$parentid."' and status=1 order by catname ASC";
		return $this->dbcon->FetchAllResults($sql);
	
	} 
	function getCatNameValue($ids){
	
		$sql="select id,catname from itf_category where id='".$ids."'";

		return $this->dbcon->Query($sql);
	
	
	}
		
		

		
		
		
}

?>