<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Shopping</title>
    <!-- base:css -->
<link rel='stylesheet' type='text/css' href='//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css'>
<link rel='stylesheet' type='text/css' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/fontawesome.min.css'>




<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
  </head>
  <body>
    <div class="container-scroller">
	
    <div class="container-fluid page-body-wrapper">
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="row">
            <div class="col-12 grid-margin">
              <div class="card">
           

                <div class="card-body">
                  <h4 class="card-title">Add Item</h4>
                  <form class="form-sample" action="<?php echo BASEURL; ?>itemController/addItem" method="post" name="item">
                   
                   <div class="row">
                      <div class="col-md-6">
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">Item Name</label>
                          <div class="col-sm-9">
                          
                             <input type="text" name="item_name"  placeholder="Item Name" class="form-control" />
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">Item Code</label>
                          <div class="col-sm-9">
                           <input type="text" name="item_code"  placeholder="Item Code" class="form-control" />
                          </div>
                        </div>
                      </div>
                    </div>


                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">Item Price</label>
                          <div class="col-sm-9">
                            <input type="text" name="Item_price"  placeholder="Item Price" class="form-control" />
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">Item Category</label>
                          <div class="col-sm-9">
                            <input type="text" name="Item_category"  placeholder="Item Category" class="form-control" />
                          </div>
                        </div>
                      </div>
                    </div>



                  


                     
                    </div>
                    <button type="submit" class="btn btn-primary mr-2" onclick="return check();">Submit</button>
          <a href="<?=BASEURL?>itemController/itemlist" class="btn btn-light">Cancel</a>
                  </form>
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
     
    function check(){
   
            var alphaExp = /^[a-zA-Z .,@-]+$/;
            var num = /^([0-9]*)$/;
            
            // var emaile = /^([a-zA-Z0-9_\-\.]+)@([a-zA-Z0-9_\-\.]+)\.([a-zA-Z]{2,5})$/;
            // var phoneve = /^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/im;
            // var hour=/^[0-9]+([.][0-9]+)?$/;


              if(document.item.item_name.value=="")
              {
                alert('Item Name field is required');
                document.item.item_name.focus();
                return false;
              }
              else if(!document.item.item_name.value.match(alphaExp)){
                alert('Enter valid Item name');
                document.item.item_name.focus();
                return false;
              }else if(document.item.item_code.value=="")
              {
                alert('Item code field is required');
                document.item.item_code.focus();
                return false;
              }else if(document.item.Item_price.value=="")
              {
                alert('Item price field is required');
                document.item.Item_price.focus();
                return false;
              } else if(!document.item.Item_price.value.match(num)){
                alert('Enter valid Item price');
                document.item.Item_price.focus();
                return false;
              }else if(document.item.Item_category.value=="")
              {
                alert('Item category field is required');
                document.item.Item_category.focus();
                return false;
              }
    }
 
</script>
    <!-- End custom js for this page-->
  </body>
</html>