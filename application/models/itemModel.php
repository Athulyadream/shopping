<?php

class itemModel extends tableop {


  public function addItem($data){
        $data = implode(",", $data);
        $query = $this->insert_query("INSERT INTO `items`( `item_name`, `item_code`, `Item_price`, `Item_category`, `status`, `created_at`) VALUES (".$data.")");
        echo "INSERT INTO `items`( `item_name`, `item_code`, `Item_price`, `Item_category`, `status`, `created_at`) VALUES (".$data.")";
        // print_r($query);
        // die();
         return $query;
          
    }

      public function update_table($table,$data,$where){
        $data = implode(",", $data);
      

        $sql = "UPDATE `".$table."` SET ".$data;
         if($where !=""){
            $sql .=" WHERE ".$where;
         }
       
         
          $query = $this->query($sql);

         return $query;
          

    }
     

    public function getitem_row($table,$where){
      
        $result = $this->query_result_row("SELECT * FROM `".$table."` WHERE ".$where);
        return $result;
    }



    public function delete_item($item_id){
       
        if($item_id !=""){
            $sql ="DELETE FROM `items` WHERE id =".$item_id;
            $query = $this->query($sql);
        }
        return $query;

    }



   


    public function itemlist($where=NULL){

        $sql ="SELECT * FROM `items` ";

        if($where != ""){
            $sql .= " WHERE ".$where;
        }
        $result = $this->query_result($sql);
        if($result){
            return $result;
        }else{
            return '';
        }
    }


}


?>