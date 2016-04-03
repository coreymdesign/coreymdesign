	<?php	
	if(function_exists('current_user_can'))
	if(!current_user_can('manage_options')) {
	die('Access Denied');
}	
if(!function_exists('current_user_can')){
	die('Access Denied');
}

/****<add>****/

function youtube_or_vimeo($url){
	if(strpos($url,'youtube') !== false || strpos($url,'youtu') !== false){	
		if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match)) {
			return 'youtube';
		}
	}
	elseif(strpos($url,'vimeo') !== false) {
		$explode = explode("/",$url);
		$end = end($explode);
		if(strlen($end) == 8 || strlen($end) == 9 )
			return 'vimeo';
	}
	return 'image';
}

/****</add>****/

function showportfolio() 
  {
	  
  global $wpdb;
  
  if(isset($_POST['search_events_by_title']))
$_POST['search_events_by_title']=esc_html(stripslashes($_POST['search_events_by_title']));
if(isset($_POST['asc_or_desc']))
$_POST['asc_or_desc']=esc_js($_POST['asc_or_desc']);
if(isset($_POST['order_by']))
$_POST['order_by']=esc_js($_POST['order_by']);
  $where='';
  	$sort["custom_style"] ="manage-column column-autor sortable desc";
	$sort["default_style"]="manage-column column-autor sortable desc";
	$sort["sortid_by"]='id';
	$sort["1_or_2"]=1;
	$order='';
	
	if(isset($_POST['page_number']))
	{
			
			if($_POST['asc_or_desc'])
			{
				$sort["sortid_by"]=$_POST['order_by'];
				if($_POST['asc_or_desc']==1)
				{
					$sort["custom_style"]="manage-column column-title sorted asc";
					$sort["1_or_2"]="2";
					$order="ORDER BY ".$sort["sortid_by"]." ASC";
				}
				else
				{
					$sort["custom_style"]="manage-column column-title sorted desc";
					$sort["1_or_2"]="1";
					$order="ORDER BY ".$sort["sortid_by"]." DESC";
				}
			}
	if($_POST['page_number'])
		{
			$limit=($_POST['page_number']-1)*20; 
		}
		else
		{
			$limit=0;
		}
	}
	else
		{
			$limit=0;
		}
	if(isset($_POST['search_events_by_title'])){
		$search_tag=esc_html(stripslashes($_POST['search_events_by_title']));
		}
		
		else
		{
		$search_tag="";
		}		
		
	 if(isset($_GET["catid"])){
	    $cat_id=$_GET["catid"];	
		}
       else
	   {
       if(isset($_POST['cat_search'])){
		$cat_id=$_POST['cat_search'];
		}else{
		
		$cat_id=0;}
       }
     
 if ( $search_tag ) {
		$where= " WHERE name LIKE '%".$search_tag."%' ";
	}
if($where){
	  if($cat_id){
	  $where.=" AND sl_width=" .$cat_id;
	  }
	
	}
	else{
	if($cat_id){
	  $where.=" WHERE sl_width=" .$cat_id;
	  }
	
	}
	
	 $cat_row_query="SELECT id,name FROM ".$wpdb->prefix."huge_itportfolio_portfolios WHERE sl_width=0";
	$cat_row=$wpdb->get_results($cat_row_query);
	
	// get the total number of records
	$query = "SELECT COUNT(*) FROM ".$wpdb->prefix."huge_itportfolio_portfolios". $where;
	
	$total = $wpdb->get_var($query);
	$pageNav['total'] =$total;
	$pageNav['limit'] =	 $limit/20+1;
	
	if($cat_id){
	$query ="SELECT  a.* ,  COUNT(b.id) AS count, g.par_name AS par_name FROM ".$wpdb->prefix."huge_itportfolio_portfolios  AS a LEFT JOIN ".$wpdb->prefix."huge_itportfolio_portfolios AS b ON a.id = b.sl_width LEFT JOIN (SELECT  ".$wpdb->prefix."huge_itportfolio_portfolios.ordering as ordering,".$wpdb->prefix."huge_itportfolio_portfolios.id AS id, COUNT( ".$wpdb->prefix."huge_itportfolio_images.portfolio_id ) AS prod_count
FROM ".$wpdb->prefix."huge_itportfolio_images, ".$wpdb->prefix."huge_itportfolio_portfolios
WHERE ".$wpdb->prefix."huge_itportfolio_images.portfolio_id = ".$wpdb->prefix."huge_itportfolio_portfolios.id
GROUP BY ".$wpdb->prefix."huge_itportfolio_images.portfolio_id) AS c ON c.id = a.id LEFT JOIN
(SELECT ".$wpdb->prefix."huge_itportfolio_portfolios.name AS par_name,".$wpdb->prefix."huge_itportfolio_portfolios.id FROM ".$wpdb->prefix."huge_itportfolio_portfolios) AS g
 ON a.sl_width=g.id WHERE  a.name LIKE '%".$search_tag."%' group by a.id ". $order ." "." LIMIT ".$limit.",20" ; 

	 }
	 else{
	 $query ="SELECT  a.* ,  COUNT(b.id) AS count, g.par_name AS par_name FROM ".$wpdb->prefix."huge_itportfolio_portfolios  AS a LEFT JOIN ".$wpdb->prefix."huge_itportfolio_portfolios AS b ON a.id = b.sl_width LEFT JOIN (SELECT  ".$wpdb->prefix."huge_itportfolio_portfolios.ordering as ordering,".$wpdb->prefix."huge_itportfolio_portfolios.id AS id, COUNT( ".$wpdb->prefix."huge_itportfolio_images.portfolio_id ) AS prod_count
FROM ".$wpdb->prefix."huge_itportfolio_images, ".$wpdb->prefix."huge_itportfolio_portfolios
WHERE ".$wpdb->prefix."huge_itportfolio_images.portfolio_id = ".$wpdb->prefix."huge_itportfolio_portfolios.id
GROUP BY ".$wpdb->prefix."huge_itportfolio_images.portfolio_id) AS c ON c.id = a.id LEFT JOIN
(SELECT ".$wpdb->prefix."huge_itportfolio_portfolios.name AS par_name,".$wpdb->prefix."huge_itportfolio_portfolios.id FROM ".$wpdb->prefix."huge_itportfolio_portfolios) AS g
 ON a.sl_width=g.id WHERE a.name LIKE '%".$search_tag."%'  group by a.id ". $order ." "." LIMIT ".$limit.",20" ; 
}

$rows = $wpdb->get_results($query);
 global $glob_ordering_in_cat;
if(isset($sort["sortid_by"]))
{
	if($sort["sortid_by"]=='ordering'){
	if($_POST['asc_or_desc']==1){
		$glob_ordering_in_cat=" ORDER BY ordering ASC";
	}
	else{
		$glob_ordering_in_cat=" ORDER BY ordering DESC";
	}
	}
}
$rows=open_cat_in_tree($rows);
	$query ="SELECT  ".$wpdb->prefix."huge_itportfolio_portfolios.ordering,".$wpdb->prefix."huge_itportfolio_portfolios.id, COUNT( ".$wpdb->prefix."huge_itportfolio_images.portfolio_id ) AS prod_count
FROM ".$wpdb->prefix."huge_itportfolio_images, ".$wpdb->prefix."huge_itportfolio_portfolios
WHERE ".$wpdb->prefix."huge_itportfolio_images.portfolio_id = ".$wpdb->prefix."huge_itportfolio_portfolios.id
GROUP BY ".$wpdb->prefix."huge_itportfolio_images.portfolio_id " ;
	$prod_rows = $wpdb->get_results($query);
		
foreach($rows as $row)
{
	foreach($prod_rows as $row_1)
	{
		if ($row->id == $row_1->id)
		{
			$row->ordering = $row_1->ordering;
		$row->prod_count = $row_1->prod_count;
	}
		}
	
	}
	

	 

	 
	$cat_row=open_cat_in_tree($cat_row);
		html_showportfolios( $rows, $pageNav,$sort,$cat_row);
  }

function open_cat_in_tree($catt,$tree_problem='',$hihiih=1){

global $wpdb;
global $glob_ordering_in_cat;
static $trr_cat=array();
if(!isset($search_tag))
$search_tag='';
if($hihiih)
$trr_cat=array();
foreach($catt as $local_cat){
	$local_cat->name=$tree_problem.$local_cat->name;
	array_push($trr_cat,$local_cat);
	$new_cat_query=	"SELECT  a.* ,  COUNT(b.id) AS count, g.par_name AS par_name FROM ".$wpdb->prefix."huge_itportfolio_portfolios  AS a LEFT JOIN ".$wpdb->prefix."huge_itportfolio_portfolios AS b ON a.id = b.sl_width LEFT JOIN (SELECT  ".$wpdb->prefix."huge_itportfolio_portfolios.ordering as ordering,".$wpdb->prefix."huge_itportfolio_portfolios.id AS id, COUNT( ".$wpdb->prefix."huge_itportfolio_images.portfolio_id ) AS prod_count
FROM ".$wpdb->prefix."huge_itportfolio_images, ".$wpdb->prefix."huge_itportfolio_portfolios
WHERE ".$wpdb->prefix."huge_itportfolio_images.portfolio_id = ".$wpdb->prefix."huge_itportfolio_portfolios.id
GROUP BY ".$wpdb->prefix."huge_itportfolio_images.portfolio_id) AS c ON c.id = a.id LEFT JOIN
(SELECT ".$wpdb->prefix."huge_itportfolio_portfolios.name AS par_name,".$wpdb->prefix."huge_itportfolio_portfolios.id FROM ".$wpdb->prefix."huge_itportfolio_portfolios) AS g
 ON a.sl_width=g.id WHERE a.name LIKE '%".$search_tag."%' AND a.sl_width=".$local_cat->id." group by a.id  ".$glob_ordering_in_cat; 
 $new_cat=$wpdb->get_results($new_cat_query);
 open_cat_in_tree($new_cat,$tree_problem. "— ",0);
}
return $trr_cat;

}

function editportfolio($id)
  {
	  
	  global $wpdb;
	  
	  if(isset($_GET["removeslide"])){
	     if($_GET["removeslide"] != ''){
	

	  $wpdb->query("DELETE FROM ".$wpdb->prefix."huge_itportfolio_images  WHERE id = ".$_GET["removeslide"]." ");


	
	   }
	   }

	   $query=$wpdb->prepare("SELECT * FROM ".$wpdb->prefix."huge_itportfolio_portfolios WHERE id= %d",$id);
	   $row=$wpdb->get_row($query);
	   if(!isset($row->portfolio_list_effects_s))
	   return 'id not found';
       $images=explode(";;;",$row->portfolio_list_effects_s);
	   $par=explode('	',$row->param);
	   $count_ord=count($images);
	   $cat_row=$wpdb->get_results("SELECT * FROM ".$wpdb->prefix."huge_itportfolio_portfolios WHERE id!=" .$id." and sl_width=0");
       $cat_row=open_cat_in_tree($cat_row);
	   	  $query=$wpdb->prepare("SELECT name,ordering FROM ".$wpdb->prefix."huge_itportfolio_portfolios WHERE sl_width=%d  ORDER BY `ordering` ",$row->sl_width);
	   $ord_elem=$wpdb->get_results($query);
	   
	    $query=$wpdb->prepare("SELECT * FROM ".$wpdb->prefix."huge_itportfolio_images where portfolio_id = %d order by ordering ASC  ",$row->id);
			   $rowim=$wpdb->get_results($query);
			   
			   if(isset($_GET["addslide"])){
			   if($_GET["addslide"] == 1){
	
$table_name = $wpdb->prefix . "huge_itportfolio_images";
    $sql_2 = "
INSERT INTO 

`" . $table_name . "` ( `name`, `portfolio_id`, `description`, `image_url`, `sl_url`, `ordering`, `published`, `published_in_sl_width`,) VALUES
( '', '".$row->id."', '', '', '', 'par_TV', 2, '1' )";

    $wpdb->query($sql_huge_itportfolio_images);
	

      $wpdb->query($sql_2);
	
	   }
	   }
	   
	
	   
	   $query="SELECT * FROM ".$wpdb->prefix."huge_itportfolio_portfolios order by id ASC";
			   $rowsld=$wpdb->get_results($query);
			  
			    $query = "SELECT *  from " . $wpdb->prefix . "huge_itportfolio_params ";

    $rowspar = $wpdb->get_results($query);

    $paramssld = array();
    foreach ($rowspar as $rowpar) {
        $key = $rowpar->name;
        $value = $rowpar->value;
        $paramssld[$key] = $value;
    }
	
	 $query="SELECT * FROM ".$wpdb->prefix."posts where post_type = 'post' and post_status = 'publish' order by id ASC";
			   $rowsposts=$wpdb->get_results($query);
	 
	 $rowsposts8 = '';
	 $postsbycat = '';
	 if(isset($_POST["iframecatid"])){
	 	  $query=$wpdb->prepare("SELECT * FROM ".$wpdb->prefix."term_relationships where term_taxonomy_id = %d order by object_id ASC",$_POST["iframecatid"]);
		$rowsposts8=$wpdb->get_results($query);


	 

			   foreach($rowsposts8 as $rowsposts13){
	 $query=$wpdb->prepare("SELECT * FROM ".$wpdb->prefix."posts where post_type = 'post' and post_status = 'publish' and ID = %d  order by ID ASC",$rowsposts13->object_id);
			   $rowsposts1=$wpdb->get_results($query);
			   $postsbycat = $rowsposts1;
			   
	 }
	 }
	
	   	   
    Html_editportfolio($ord_elem, $count_ord, $images, $row, $cat_row, $rowim, $rowsld, $paramssld, $rowsposts, $rowsposts8, $postsbycat);
  }
  
function add_portfolio()
{
	global $wpdb;
	
	$table_name = $wpdb->prefix . "huge_itportfolio_portfolios";
    $sql_2 = "
INSERT INTO 

`" . $table_name . "` ( `name`, `sl_height`, `sl_width`, `pause_on_hover`, `portfolio_list_effects_s`, `description`, `param`, `sl_position`, `ordering`, `published`, `categories`, `ht_show_sorting`, `ht_show_filtering`) VALUES
( 'New portfolio', '375', '600', 'on', 'cubeH', '4000', '1000', 'off', '1', '300', 'My First Category,My Second Category,My Third Category,', 'off', 'off')";

      $wpdb->query($sql_2);

   $query="SELECT * FROM ".$wpdb->prefix."huge_itportfolio_portfolios order by id ASC";
			   $rowsldcc=$wpdb->get_results($query);
			   $last_key = key( array_slice( $rowsldcc, -1, 1, TRUE ) );
			   
			   
	foreach($rowsldcc as $key=>$rowsldccs){
		if($last_key == $key){
			header('Location: admin.php?page=portfolios_huge_it_portfolio&id='.$rowsldccs->id.'&task=apply');
		}
	}	
}

/***<add>***/
function portfolio_video($id)
{
	global $wpdb;


	if(isset($_POST["huge_it_add_video_input"]) && $_POST["huge_it_add_video_input"] != '' ) {			
		if(!isset($_GET['thumb_parent']) || $_GET['thumb_parent'] == null) {
			
			$table_name = $wpdb->prefix . "huge_itportfolio_images";
			$query=$wpdb->prepare("SELECT * FROM ".$wpdb->prefix."huge_itportfolio_portfolios WHERE id= %d",$id);
			$row=$wpdb->get_row($query);
			$query=$wpdb->prepare("SELECT * FROM ".$wpdb->prefix."huge_itportfolio_images where portfolio_id = %s ", $row->id);
			$rowplusorder=$wpdb->get_results($query);

			foreach ($rowplusorder as $key=>$rowplusorders){

				if($rowplusorders->ordering == 0){				
					$rowplusorderspl = 1;
					$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."huge_itportfolio_images SET ordering = '".$rowplusorderspl."' WHERE id = %s ", $rowplusorders->id));
				}
				else { 
					$rowplusorderspl=$rowplusorders->ordering+1;
					$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."huge_itportfolio_images SET ordering = '".$rowplusorderspl."' WHERE id = %s ", $rowplusorders->id));
				}

			}
			$_POST["huge_it_add_video_input"] .=";";
			$sql_video = "INSERT INTO 
			`" . $table_name . "` ( `name`, `portfolio_id`, `description`, `image_url`, `sl_url`, `sl_type`, `link_target`, `ordering`, `published`, `published_in_sl_width`,`category`) VALUES 
			( '".$_POST["show_title"]."', '".$id."', '".$_POST["show_description"]."', '".$_POST["huge_it_add_video_input"]."', '".$_POST["show_url"]."', 'video', 'on', '0', '1', '1','' )";
		   $wpdb->query($sql_video);
	    }
	  

		else {
		    $query=$wpdb->prepare("SELECT * FROM ".$wpdb->prefix."huge_itportfolio_portfolios WHERE id= %d",$id);
		    $row=$wpdb->get_row($query);
			$query=$wpdb->prepare("SELECT * FROM ".$wpdb->prefix."huge_itportfolio_images where portfolio_id = %s and id = %d", $row->id,$_GET['thumb_parent']);
			$get_proj_image=$wpdb->get_row($query);
			$get_proj_image->image_url .= $_POST["huge_it_add_video_input"].";";
			//$get_proj_image->image_url .= ";";
			$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."huge_itportfolio_images SET image_url = '".$get_proj_image->image_url."' where portfolio_id = %s and id = %d", $row->id,$_GET['thumb_parent']));
		}

	}
   Html_portfolio_video();
}
function  portfolio_video_edit($id) {
	global $wpdb;
	$thumb = $_GET["thumb"];
	$portfolio_id = $_GET["portfolio_id"];
	$id = $_GET["id"];
	$query=$wpdb->prepare("SELECT * FROM ".$wpdb->prefix."huge_itportfolio_images where portfolio_id = %s and id = %d", $portfolio_id,$id);
			$get_proj_image=$wpdb->get_row($query);
		$input_edit_video = explode(";", $get_proj_image->image_url);//var_dump($input_edit_video );exit;
		$input_edit_video_thumb = $input_edit_video[$thumb];
		$video = youtube_or_vimeo($input_edit_video_thumb);

	if( isset($_POST["huge_it_add_video_input"]) && $_POST["huge_it_add_video_input"] != '') {
		$input_edit_video[$thumb] = $_POST["huge_it_add_video_input"];
		$new_url = implode(";", $input_edit_video);
		$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."huge_itportfolio_images SET image_url = '".$new_url."' where portfolio_id = %s and id = %d",$portfolio_id,$id));
	}

	if(isset($_POST["huge_it_edit_video_input"]) && $_POST["huge_it_edit_video_input"] != '')
		$edit = $_POST["huge_it_edit_video_input"];
	else  $edit = '';
		
	Html_portfolio_video_edit($thumb,$portfolio_id,$id,$input_edit_video_thumb,$video,$edit);

}

/***</add>***/

function popup_posts($id)
{
	  global $wpdb;
	 
	     if(isset($_GET["removeslide"])){
	     if($_GET["removeslide"] != ''){
	

	  $wpdb->query("DELETE FROM ".$wpdb->prefix."huge_itportfolio_images  WHERE id = ".$_GET["removeslide"]." ");


	
	   }
	   }

	   $query=$wpdb->prepare("SELECT * FROM ".$wpdb->prefix."huge_itportfolio_portfolios WHERE id= %d",$id);
	   $row=$wpdb->get_row($query);
	   if(!isset($row->portfolio_list_effects_s))
	   return 'id not found';
       $images=explode(";;;",$row->portfolio_list_effects_s);
	   $par=explode('	',$row->param);
	   $count_ord=count($images);
	   $cat_row=$wpdb->get_results("SELECT * FROM ".$wpdb->prefix."huge_itportfolio_portfolios WHERE id!=" .$id." and sl_width=0");
       $cat_row=open_cat_in_tree($cat_row);
	   	  $query=$wpdb->prepare("SELECT name,ordering FROM ".$wpdb->prefix."huge_itportfolio_portfolios WHERE sl_width=%d  ORDER BY `ordering` ",$row->sl_width);
	   $ord_elem=$wpdb->get_results($query);
	   
	    $query=$wpdb->prepare("SELECT * FROM ".$wpdb->prefix."huge_itportfolio_images where portfolio_id = %d order by id ASC  ",$row->id);
			   $rowim=$wpdb->get_results($query);
			   
			   if(isset($_GET["addslide"])){
			   if($_GET["addslide"] == 1){
	
$table_name = $wpdb->prefix . "huge_itportfolio_images";
    $sql_2 = "
INSERT INTO 

`" . $table_name . "` ( `name`, `portfolio_id`, `description`, `image_url`, `sl_url`, `ordering`, `published`, `published_in_sl_width`) VALUES
( '', '".$row->id."', '', '', '', 'par_TV', 2, '1' )";

    $wpdb->query($sql_huge_itportfolio_images);
	

      $wpdb->query($sql_2);
	
	   }
	   }
	
	   
	   $query="SELECT * FROM ".$wpdb->prefix."huge_itportfolio_portfolios order by id ASC";
			   $rowsld=$wpdb->get_results($query);
			  
			    $query = "SELECT *  from " . $wpdb->prefix . "huge_itportfolio_params ";

    $rowspar = $wpdb->get_results($query);

    $paramssld = array();
    foreach ($rowspar as $rowpar) {
        $key = $rowpar->name;
        $value = $rowpar->value;
        $paramssld[$key] = $value;
    }
	
	 $query="SELECT * FROM ".$wpdb->prefix."posts where post_type = 'post' and post_status = 'publish' order by id ASC";
			   $rowsposts=$wpdb->get_results($query);
			   
			   $categories = get_categories(  );
		if(isset($_POST["iframecatid"])){
		$iframecatid = $_POST["iframecatid"];
		}
		else
		{
		$iframecatid = $categories[0]->cat_ID;
		}
	 
	 	  $query=$wpdb->prepare("SELECT * FROM ".$wpdb->prefix."term_relationships where term_taxonomy_id = %d order by object_id ASC",$iframecatid);
		$rowsposts8=$wpdb->get_results($query);


	 

			   foreach($rowsposts8 as $rowsposts13){
	 $query=$wpdb->prepare("SELECT * FROM ".$wpdb->prefix."posts where post_type = 'post' and post_status = 'publish' and ID = %d  order by ID ASC",$rowsposts13->object_id);
			   $rowsposts1=$wpdb->get_results($query);
			   
			   $postsbycat = $rowsposts1;
			   
	 }

	  if(isset($_GET["closepop"])){
	  if($_GET["closepop"] == 1){

	      if($_POST["popupposts"] != 'none' and $_POST["popupposts"] != ''){
		  
		/*  	echo $_GET["closepop"].'sdasdasdsad';
	echo $_POST["popupposts"].'dddddddd';*/

$popuppostsposts = explode(";", $_POST["popupposts"]);

array_pop($popuppostsposts);

		foreach($popuppostsposts as $popuppostsposts1){
		$my_id = $popuppostsposts1;
		
$post_id_1 = get_post($my_id); 



			   $post_image = wp_get_attachment_url( get_post_thumbnail_id($popuppostsposts1) );
		$posturl=get_permalink($popuppostsposts1);
$table_name = $wpdb->prefix . "huge_itportfolio_images";

$descnohtmlnoq=strip_tags($post_id_1->post_content);
$descnohtmlnoq1 = html_entity_decode($descnohtmlnoq);
$descnohtmlnoq1 = htmlentities($descnohtmlnoq1, ENT_QUOTES, "UTF-8");


    $sql_posts = "
INSERT INTO 

`" . $table_name . "` ( `name`, `portfolio_id`, `description`, `image_url`, `sl_url`, `sl_type`, `link_target`, `ordering`, `published`, `published_in_sl_width`) VALUES
( '".$post_id_1->post_title."', '".$row->id."', '".$descnohtmlnoq1."', '".$post_image ."', '".$posturl."', 'image', 'on', '0', '2', '1' )";



      $wpdb->query($sql_posts);
	  
	

		}
		
	}
	if(!($_POST["lastposts"])){
	 $wpdb->query("UPDATE ".$wpdb->prefix."huge_itportfolio_portfolios SET published = '".$_POST["posthuge-it-description-length"]."' WHERE id = '".$_GET['id']."' ");
	 }
	  }
	  }

if(isset($_POST["lastposts"])){
$query=$wpdb->prepare("SELECT * FROM ".$wpdb->prefix."posts where post_type = 'post' and post_status = 'publish' order by id DESC LIMIT 0, ".$_POST["lastposts"]."");
			   $rowspostslast=$wpdb->get_results($query);
			   foreach($rowspostslast as $rowspostslastfor){
			   
			   		$my_id = $rowspostslastfor;
$post_id_1 = get_post($my_id); 



			   $post_image = wp_get_attachment_url( get_post_thumbnail_id($rowspostslastfor) );
		$posturl=get_permalink($rowspostslastfor);
$table_name = $wpdb->prefix . "huge_itportfolio_images";
$descnohtmlno=strip_tags($post_id_1->post_content);
$descnohtmlno1 = html_entity_decode($descnohtmlno);
$lengthtextpost = '300';
$descnohtmlno2 = substr_replace($descnohtmlno1, "", $lengthtextpost);
$descnohtmlno3 = htmlentities($descnohtmlno2, ENT_QUOTES, "UTF-8");
$posttitle = htmlentities($post_id_1->post_title, ENT_QUOTES, "UTF-8");
$posturl2 = htmlentities($posturl, ENT_QUOTES, "UTF-8");

			   
			       $sql_lastposts = "INSERT INTO 
`" . $table_name . "` ( `name`, `portfolio_id`, `description`, `image_url`, `sl_url`, `ordering`, `published`, `published_in_sl_width`) VALUES
( '".$posttitle."', '".$row->id."', '".$descnohtmlno3."', '".$post_image ."', '".$posturl."', 'par_TV', 2, '1' )";

    $wpdb->query($sql_huge_itportfolio_images);

      $wpdb->query($sql_lastposts);
	  }
}
	   	   
    Html_popup_posts($ord_elem, $count_ord, $images, $row, $cat_row, $rowim, $rowsld, $paramssld, $rowsposts, $rowsposts8, $postsbycat);
}

function removeportfolio($id)
{

	global $wpdb;
	 $sql_remov_tag=$wpdb->prepare("DELETE FROM ".$wpdb->prefix."huge_itportfolio_portfolios WHERE id = %d", $id);
	 $sql_remov_image=$wpdb->prepare("DELETE FROM ".$wpdb->prefix."huge_itportfolio_images WHERE portfolio_id = %d", $id);
 if(!$wpdb->query($sql_remov_tag))
 {
	  ?>
	  <div id="message" class="error"><p>portfolio Not Deleted</p></div>
      <?php
	 
 }
 else{
	 $wpdb->query($sql_remov_image);
 ?>
 <div class="updated"><p><strong><?php _e('Item Deleted.' ); ?></strong></p></div>
 <?php
 }
}

function apply_cat($id)
{	
//var_dump($_POST);
		 global $wpdb;
		 if(!is_numeric($id)){
			 echo 'insert numerc id';
		 	return '';
		 }
		 if(!(isset($_POST['sl_width']) && isset($_POST["name"]) ))
		 {
			 return '';
		 }
		 $cat_row=$wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."huge_itportfolio_portfolios WHERE id!= %d ", $id));
		 $corent_ord=$wpdb->get_var($wpdb->prepare('SELECT `ordering` FROM '.$wpdb->prefix.'huge_itportfolio_portfolios WHERE id = %d AND sl_width=%d',$id,$_POST['sl_width']));
		 $max_ord=$wpdb->get_var('SELECT MAX(ordering) FROM '.$wpdb->prefix.'huge_itportfolio_portfolios');
	 
            $query=$wpdb->prepare("SELECT sl_width FROM ".$wpdb->prefix."huge_itportfolio_portfolios WHERE id = %d", $id);
	        $id_bef=$wpdb->get_var($query);
      
	if(isset($_POST["content"])){
	$script_cat = preg_replace('#<script(.*?)>(.*?)</script>#is', '', stripslashes($_POST["content"]));
	}
	$name = $_POST["allCategories"];
	$name = str_replace(' ', '_', $name);
			$wpdb->query("UPDATE ".$wpdb->prefix."huge_itportfolio_portfolios SET  name = '".$_POST["name"]."'  WHERE id = '".$id."' ");
			$wpdb->query("UPDATE ".$wpdb->prefix."huge_itportfolio_portfolios SET  sl_width = '".$_POST["sl_width"]."'  WHERE id = '".$id."' ");
			$wpdb->query("UPDATE ".$wpdb->prefix."huge_itportfolio_portfolios SET  sl_height = '".$_POST["sl_height"]."'  WHERE id = '".$id."' ");
			$wpdb->query("UPDATE ".$wpdb->prefix."huge_itportfolio_portfolios SET  pause_on_hover = '".$_POST["pause_on_hover"]."'  WHERE id = '".$id."' ");
			$wpdb->query("UPDATE ".$wpdb->prefix."huge_itportfolio_portfolios SET  portfolio_list_effects_s = '".$_POST["portfolio_effects_list"]."'  WHERE id = '".$id."' ");
			$wpdb->query("UPDATE ".$wpdb->prefix."huge_itportfolio_portfolios SET  sl_position = '".$_POST["sl_position"]."'  WHERE id = '".$id."' ");
			$wpdb->query("UPDATE ".$wpdb->prefix."huge_itportfolio_portfolios SET  description = '".$_POST["sl_pausetime"]."'  WHERE id = '".$id."' ");
			$wpdb->query("UPDATE ".$wpdb->prefix."huge_itportfolio_portfolios SET  param = '".$_POST["sl_changespeed"]."'  WHERE id = '".$id."' ");
			$wpdb->query("UPDATE ".$wpdb->prefix."huge_itportfolio_portfolios SET  ordering = '1'  WHERE id = '".$id."' ");
                        $wpdb->query("UPDATE ".$wpdb->prefix."huge_itportfolio_portfolios SET  categories = '".$name."'  WHERE id = '".$id."' ");
                        $wpdb->query("UPDATE ".$wpdb->prefix."huge_itportfolio_portfolios SET  ht_show_sorting = '".$_POST["ht_show_sorting"]."'  WHERE id = '".$id."' ");
                        $wpdb->query("UPDATE ".$wpdb->prefix."huge_itportfolio_portfolios SET  ht_show_filtering = '".$_POST["ht_show_filtering"]."'  WHERE id = '".$id."' ");

		
	$query=$wpdb->prepare("SELECT * FROM ".$wpdb->prefix."huge_itportfolio_portfolios WHERE id = %d", $id);
	   $row=$wpdb->get_row($query);

				
				/***<image optimize>***/
				
		$query="SELECT * FROM ".$wpdb->prefix."huge_itportfolio_params";

	    $rowspar = $wpdb->get_results($query);
    $paramssld = array();
    foreach ($rowspar as $rowpar) {
        $key = $rowpar->name;
        $value = $rowpar->value;
        $paramssld[$key] = $value;
    }
			$image_prefix = "_huge_it_small_portfolio";

			$view0_width = $paramssld['ht_view0_block_width']; 
			$view1_width = $paramssld['ht_view1_block_width']; 
			$view2_width = $paramssld['ht_view2_element_width']; 
			$view3_width = $paramssld['ht_view3_mainimage_width']; 
			$view4_width = $paramssld["ht_view5_main_image_width"]; 
			$view6_width = $paramssld["ht_view6_width"];
		$cropwidth = max($view0_width,$view1_width,$view2_width ,$view3_width,$view4_width,$view6_width);
		if(!function_exists('huge_it_copy_image_to_small')) {
			function huge_it_copy_image_to_small($imgurl,$image_prefix,$width1) {
				if(youtube_or_vimeo($imgurl) !== 'image')
					return;
				$pathinfo = pathinfo($imgurl);
				$extension = $pathinfo["extension"];
				$extension = strtolower($extension);
				$ext = array("png","jpg","jpeg","gif","psd","swf","bmp","wbmp","tiff_ll","tiff_mm","jpc","iff","ico");
				if((strlen($imgurl) < 3) || (!in_array($extension,$ext))){ 
					return false;
				}		
				if($width1 < 280) {
						$width1 = "280";
					}
					$pathinfo = pathinfo($imgurl);
					$filename = $pathinfo["filename"];//get image's name
					$extension = $pathinfo["extension"];//get image,s extension
					set_time_limit (0);
					$upload_dir = wp_upload_dir(); 
					$path = parse_url($imgurl, PHP_URL_PATH);
					$url = $upload_dir["path"];//get upload path
					$copy_image = $url.'/'.$filename.$image_prefix.".".$extension;
					//$path = substr($path,1);
					if(file_exists($copy_image)) {
						return;
					}
					$imgurl = $_SERVER['DOCUMENT_ROOT'].$path;
					if(function_exists("wp_get_image_editor")) {
						$size = wp_get_image_editor($imgurl);

					}

					else {
						return false;
					}
					if(method_exists($size,"get_size")) {
						$old_size = $size ->get_size();
					}
					else {
						return false;
					}

					$Width = $old_size['width'];//old image's width
					$Height =$old_size['height'];//old image's height
					if ($width1 < $Width) {
						$width = $width1;
						$height = (int)(($width * $Height)/$Width);//get new height
					}
					else {
						return false;
					}
					$img = wp_get_image_editor( $imgurl);

					$upload_dir = wp_upload_dir(); 
					if ( ! is_wp_error( $img ) ) {
						$img->resize( $width, $height, true );
						$url = $upload_dir["path"];//get upload path
						$copy_image = $url.'/'.$filename.$image_prefix.".".$extension;
						if(!file_exists($copy_image)) {
							$img->save($copy_image);//save new image if not exist

						}
					}
				return true;
			}
		}
	   
				/***</image optimize>***/

	if(isset($_POST['changedvalues']) && $_POST['changedvalues'] != '') {
			$query=$wpdb->prepare("SELECT * FROM ".$wpdb->prefix."huge_itportfolio_images where portfolio_id = %d AND id in (".$_POST['changedvalues'].") order by id ASC",$row->id);
			$rowim=$wpdb->get_results($query);
			//   var_dump($_POST['changedvalues']);
			foreach ($rowim as $key=>$rowimages){

				$imgDescription = str_replace("%","%%",$_POST["im_description".$rowimages->id.""]);
				$imgTitle = str_replace("%","%%",$_POST["titleimage".$rowimages->id.""]);
				//var_dump($imgDescription);
				$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."huge_itportfolio_images SET  ordering = '".$_POST["order_by_".$rowimages->id.""]."'  WHERE ID = %d ", $rowimages->id));
				$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."huge_itportfolio_images SET  link_target = '".$_POST["sl_link_target".$rowimages->id.""]."'  WHERE ID = %d ", $rowimages->id));
				$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."huge_itportfolio_images SET  sl_url = '%s' WHERE ID = %d ",$_POST["sl_url".$rowimages->id.""], $rowimages->id));
				$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."huge_itportfolio_images SET  name = '".$imgTitle."'  WHERE ID = %d ", $rowimages->id));
				$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."huge_itportfolio_images SET  description = '".$imgDescription."'  WHERE ID = %d ", $rowimages->id));
				$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."huge_itportfolio_images SET  image_url = '".$_POST["imagess".$rowimages->id.""]."'  WHERE ID = %d ", $rowimages->id));
				$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."huge_itportfolio_images SET  category = '".$_POST["category".$rowimages->id.""]."'  WHERE ID = %d ", $rowimages->id));
					/***<image optimize>***/
						$imagesuploader = explode(";", $_POST["imagess".$rowimages->id.""]);
						array_pop($imagesuploader);
						$count = count($imagesuploader);
						
						for($i = 0;$i < $count;$i++) {
							huge_it_copy_image_to_small($imagesuploader[$i],$image_prefix,$cropwidth);
						}
					
					/***</image optimize>***/
			}
	}

if (isset($_POST['params'])) {
      $params = $_POST['params'];
      foreach ($params as $key => $value) {
          $wpdb->update($wpdb->prefix . 'huge_itportfolio_params',
              array('value' => $value),
              array('name' => $key),
              array('%s')
          );
      }
     
    }
	
				   if($_POST["imagess"] != ''){
				   		   $query=$wpdb->prepare("SELECT * FROM ".$wpdb->prefix."huge_itportfolio_images where portfolio_id = %d order by id ASC", $row->id);
			   $rowim=$wpdb->get_results($query);
	  foreach ($rowim as $key=>$rowimages){
	  $orderingplus = $rowimages->ordering+1;
	  $wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."huge_itportfolio_images SET  ordering = %d  WHERE ID = %d ", $orderingplus, $rowimages->id));
	  }
	
$table_name = $wpdb->prefix . "huge_itportfolio_images";
	$imagesnewuploader = explode(";;;", $_POST["imagess"]);
	
	array_pop($imagesnewuploader);

	foreach($imagesnewuploader as $imagesnewupload){
		huge_it_copy_image_to_small($imagesnewupload,$image_prefix,$cropwidth);		
    $sql_2 = "
INSERT INTO 

`" . $table_name . "` ( `name`, `portfolio_id`, `description`, `image_url`, `sl_url`, `ordering`, `published`, `published_in_sl_width`) VALUES
( '', '".$row->id."', '', '".$imagesnewupload.";', '', 'par_TV', 2, '1' )";

   
	

      $wpdb->query($sql_2);
		}
	   }
	   
	if(isset($_POST["posthuge-it-description-length"])){
	 $wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."huge_itportfolio_portfolios SET  published = %d WHERE id = %d ", $_POST["posthuge-it-description-length"], $_GET['id']));
}
	?>
	<div class="updated"><p><strong><?php _e('Item Saved'); ?></strong></p></div>
	<?php
	
    return true;
	
}

?>