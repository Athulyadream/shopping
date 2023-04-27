<?php

class ItemController extends framework {


    public function __construct(){
        $this->ItemModel = $this->model('ItemModel');
    }

    public function index(){

       $item = $this->ItemModel->itemlist();
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
              if($this->ItemModel->addItem($data)){
         
         // $sessionName = "Item has been created successfully";
         

                  $_SESSION['message_success'] = "Item has been created successfully";
                  $this->redirect("ItemController/itemlist");
              }else{
                  $_SESSION['message_error'] = "Item failed to create";
            $this->flash("Item failed to create","error");

                  $this->redirect("ItemController/itemlist ");
              }
   
          }else{
            $this->flash("Item failed to create","error");
                $_SESSION['message_error'] = "Item failed to create";
                $this->redirect("ItemController/itemlist ");
                
          }

    }


// update items

       public function itemedit($id){
       
            $where = "id='$id'";
            $data=$this->ItemModel->getitem_row('items',$where);
          
        
     

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
        $query = $this->ItemModel->update_table($table,$item_data,$update_item_where);
          if($query){
                $_SESSION['message_success'] = "Item has been edited successfully";
              }else{
                 $_SESSION['message_error'] = "Item failed to edit";
              }
        $item = $this->ItemModel->itemlist();
        $data['page'] = 1;
        $data['item'] = $item;
        $this->view("itemlist",$data);
                              
    }

//details view
    public function itemDetails(){
        $itemid = $_POST['itemid'];
        $wheres  = "id = '$itemid'";
        $data = $this->ItemModel->getitem_row('items',$wheres);
        $this->view("item",$data);
    }

 //Delete items

	public function item_delete($id){
        $date = date('Y-m-d H:i:s');
        $itemid = $id;
        $itemdetails=$this->ItemModel->delete_item($itemid);
        if($itemdetails){
                $_SESSION['message_success'] = "Item has been deleted successfully";
              }else{
                 $_SESSION['message_error'] = "Item failed to delete";
              }
           $item = $this->ItemModel->itemlist();
        $data['page'] = 1;
        $data['item'] = $item;
        $this->view("itemlist",$data);


    }


 //list Items

    public function itemlist(){
        $date = date('Y-m-d H:i:s');
        $item = $this->ItemModel->itemlist();
        $data['page'] = 1;
        $data['item'] = $item;
        $this->view("itemlist",$data);

    }
 




}