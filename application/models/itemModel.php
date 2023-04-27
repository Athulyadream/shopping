<?php

class ItemModel extends tableop {


    public function addItem($post){
        
        $objitems   =   new tableop("items");
        $insertRow  =   $this->insert($post);   
        $id         =   $this->insertId();
        if($insertRow)
            return $id;
        else 
            return false; 
          
    }

    public function update_table($post,$where){

        $objitems      =  new tableop("items");
        $updateRow  =   $this->update($post,$where);   
        if($updateRow)
            return true;
        else 
            return false; 

    }
     

    public function getitem_row($where){

        $objitems      =  new tableop("items");
        $row    =   $this->getRow($where); 
        if($row)
            return $row;
        else 
            return false; 
      
       
    }



    public function delete_item($item_id){
       
        if($item_id !="")
            $query = $this->delete("id = '$item_id'");
        return true;

    }



    public function itemlist($where=NULL){
        $getitems =   $this->getAll($where);
        if($getitems){
            return $getitems;
        }else{
            return '';
        }
    }


}


?>