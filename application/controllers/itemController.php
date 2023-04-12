<?php

class itemController extends framework {


    public function __construct(){
        $this->itemModel = $this->model('itemModel');
    }

    public function index(){

       $item = $this->itemModel->itemlist();
        $data['item'] = $item;
        $this->view("itemlist",$data);
    }

//add items


    public function item(){
 
        $this->view("item");
     }
    public function addItem(){


          $errors = array();
          $date = date('Y-m-d H:i:s');

          $item_name = "'".$_POST['item_name']."'";
          $item_code = "'".$_POST['item_code']."'";
          $Item_price = "'".$_POST['Item_price']."'";
          $Item_category = "'".$_POST['Item_category']."'";
          $status = 1;
          $created_at = "'".$date."'";

          if (empty($item_name)) { array_push($errors, "Item name is required"); }
          if (empty($item_code)) { array_push($errors, "Item code is required"); }
          if (empty($Item_price)) { array_push($errors, "Item price is required"); }
          if (empty($Item_category)) { array_push($errors, "Item category is required"); }

        
          if (count($errors) == 0) {
              $data = array($item_name,$item_code,$Item_price, $Item_category,$status,$created_at);
              if($this->itemModel->addItem($data)){
                  $_SESSION['message_success'] = "Item has been created successfully";
                  $this->redirect("itemController/itemlist");
              }else{
                  $_SESSION['message_error'] = "Item failed to create";
                  $this->redirect("itemController/itemlist ");
              }
   
          }else{
                $_SESSION['message_error'] = "Item failed to create";
                $this->redirect("itemController/itemlist ");
                
          }

    }


// update items

       public function itemedit($id){
       
            $where = "id='$id'";
            $data=$this->itemModel->getitem_row('items',$where);
        
     

        $this->view("itemedit",$data);
     }
    public function updateItem($itemId){
    	   $item_data = array();


          $item_name = $_POST['item_name'];
          $item_code =$_POST['item_code'];
          $Item_price = $_POST['Item_price'];
          $Item_category = $_POST['Item_category'];

        $item_data[] = "item_name = '".$item_name."'";
        $item_data[] = "item_code ='".$item_code."'";
        $item_data[] = "Item_price ='".$Item_price."'";
        $item_data[] = "Item_category ='".$Item_category."'";
      	$update_item_where = "id = '$itemId'";
        $table = 'items';
        $query = $this->itemModel->update_table($table,$item_data,$update_item_where);
        
          $item = $this->itemModel->itemlist();
        $data['page'] = 1;
        $data['item'] = $item;
        $this->view("itemlist",$data);
                              
    }

//details view
    public function itemDetails(){
        $itemid = $_POST['itemid'];
        $wheres  = "id = '$itemid'";
        $data = $this->itemModel->getitem_row('items',$wheres);
        $this->view("item",$data);
    }

 //Delete items

	public function item_delete($id){
        $date = date('Y-m-d H:i:s');
        $itemid = $id;
        $itemdetails=$this->itemModel->delete_item($itemid);
           $item = $this->itemModel->itemlist();
        $data['page'] = 1;
        $data['item'] = $item;
        $this->view("itemlist",$data);


    }


 //list Items

    public function itemlist(){
        $date = date('Y-m-d H:i:s');
        $item = $this->itemModel->itemlist();
        $data['page'] = 1;
        $data['item'] = $item;
        $this->view("itemlist",$data);

    }
 




}