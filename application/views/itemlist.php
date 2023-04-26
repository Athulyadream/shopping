<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Shopping</title>
    <!-- base:css -->
<link rel='stylesheet' type='text/css' href='//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css'>
<!-- <link rel='stylesheet' type='text/css' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/fontawesome.min.css'>



<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/fontawesome.min.js"></script> -->

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>

  </head>
  <body>
    <div class="container-scroller">
	
    <ddiv class="container-fluid page-body-wrapper">
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                   <div class="row">
              <div class="col-md-8 col-lg-8">
                  <h4 class="card-title">List Item</h4></div>
              <div class="col-md-4 col-lg-4">

                  <span  style="float: right;"> <a href="<?php echo BASEURL; ?>itemController/item" class="btn btn-primary btn-sm"><i class="mdi mdi-plus menu-icon"></i> Add Item</a>
</span></div></div>
                 <!--  <p class="card-description">
                    Add class <code>.table-bordered</code>
                  </p> -->



                  <div class="table-responsive pt-3">
                 
                 
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>
                            #
                          </th>
                          <th>
                            Item Name 
                          </th>
                           <th>
                            Item code 
                          </th>
                           <th>
                            Item Price
                          </th>
                            <th>
                            Item Category
                          </th>
                          <th>
                            Created Date
                          </th>
                          <th>
                            Action
                          </th>
                        
                        </tr>
                      </thead>
                      <tbody>
                        <?php  if($data['item']):
                          $var = (($data['page']-1)*10)+1;
                          foreach ($data['item'] as $key => $value) { ?>

                          <tr>
                            <td><?=$var++?></td>
                            <td><?= $value['item_name']?></td>
                            <td><?= $value['item_code']?></td>
                            <td><?= $value['Item_price']?></td>
                            <td><?= $value['Item_category']?></td>


                            <td><?= date("m-d-Y", strtotime($value['created_at'])) ?></td>
                            <td>
                              <?php if($value['status'] ==1){ ?><label class="badge badge-success">active</label><?php }else{?> <label class="badge badge-warning">Disabled</label> <?php } ?>

                              <a href="<?php echo BASEURL; ?>itemController/itemedit/<?=$value['id']?>">Edit<i class="fa fa-pencil menu-icon"></i></a>

                              <a href="<?php echo BASEURL; ?>itemController/item_delete/<?=$value['id']?>" onclick="return confirm('Are you sure you want to delete this item')">Delete<i class="mdi mdi-delete menu-icon"></i></a>

                            
                            </td>

                          </tr>

                          
                        <?php } endif;?>
                      </tbody>
                    </table>
                  <!--   <?php 
                    if($data['number_of_page'] > 1){ 
                      ?>
                      <br/>
                      <ul class="pagination">
                        <li class="page-item disabled">

                        <?php 

                        $page = $data['page'];
                        if($page>=2){  ?> 
            <a href='index1.php?page=<?php echo $page-1; ?>'>  Prev </a>   
      <?php  } else{ ?>
<a class="page-link" href="#" tabindex="-1">Previous</a>
     <?php }      ?>
      
    </li>
                       <?php for($pages = 1; $pages<= $data['number_of_page']; $pages++) {  ?>


                          
    
    <li class="page-item"><a class="page-link" href="index2.php?page=<?=$pages?>"><?=$pages?></a></li>
   
   
                        
                      <?php  } ?>
                      <li class="page-item">

                          <?php if($page<$data['number_of_page']){   ?> 
            <a href='index1.php?page=<?php echo $page+1; ?>'>  Next </a>   
      <?php  } else{ ?>
  <a class="page-link" href="#">Next</a>
     <?php }      ?>
    
    </li>
   </ul>
                  <?php  } ?> -->
                  </div>
                </div>
              </div>
            </div>
            
          
          </div>
          </div>




        <footer class="footer">
          <div class="footer-wrap">
            <div class="d-sm-flex justify-content-center justify-content-sm-between">
           
            </div>
          </div>
        </footer>
        <!-- partial -->
      </div>
      <!-- main-panel ends -->
    </div>
		<!-- page-body-wrapper ends -->
    </div>
		<!-- container-scroller -->
    <!-- base:js -->
    <script type="text/javascript">
  
  baseurl = "<?=BASEURL?>";
 
</script>
    <!-- End custom js for this page-->
  </body>
</html>