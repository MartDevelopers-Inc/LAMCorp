<?php
    session_start();
    include('config/config.php');
    include('config/checklogin.php');
    //include('partials/analytics.php');
    check_login();
    
    $len = 6;
    $paynumber = substr(str_shuffle('0123456789'),1,$len);
    
    
    if(isset($_POST['addPayment']))
    {
            $error = 0;
            if (isset($_POST['pay_number']) && !empty($_POST['pay_number'])) {
                $pay_number=mysqli_real_escape_string($mysqli,trim($_POST['pay_number']));
            }else{
                $error = 1;
                $err="Payment Number cannot be empty";
            }
            if (isset($_POST['transaction_code']) && !empty($_POST['transaction_code'])) {
                $transaction_code=mysqli_real_escape_string($mysqli,trim($_POST['transaction_code']));
            }else{
                $error = 1;
                $err="Transaction Code cannot be empty";
            }
            
            if(!$error)
            {
                $sql="SELECT * FROM  LAMCorp_payments WHERE  transaction_code='$transaction_code' ";
                $res=mysqli_query($mysqli,$sql);
                if (mysqli_num_rows($res) > 0) {
                $row = mysqli_fetch_assoc($res);
                if ($transaction_code==$row['transaction_code'])
                {
                    $err =  "Transaction With That Code Already Exists";
                }
                else
                {
                    $err =  "Transaction With That Code Already Exists";
                }
            }
            else
            {
               $pay_number = $_POST['pay_number'];
               $kiosk_number = $_GET['kiosk_number'];
               $litres_purchased = $_POST['litres_purchased'];
               $client_name = $_POST['client_name'];
               $client_phone = $_POST['client_phone'];
               $till_number = $_POST['till_number'];
               $transaction_code = $_POST['transaction_code'];
               $amount = $_POST['amount'];
                             
                //Insert Captured information to a database table
                $query="INSERT INTO LAMCorp_payments (pay_number, kiosk_number, litres_purchased, client_name, client_phone, till_number, transaction_code, amount) VALUES (?,?,?,?,?,?,?,?)";
                $stmt = $mysqli->prepare($query);
                //bind paramaters
                $rc=$stmt->bind_param('ssssssss', $pay_number, $kiosk_number, $litres_purchased, $client_name, $client_phone, $till_number, $transaction_code, $amount);
                $stmt->execute();

                //declare a varible which will be passed to alert function
                if($stmt)
                {
                 $success = "Payment Posted"; //&& header("refresh:1; url=add_payment.php");
                }
                else 
                {
                    $err = "Please Try Again Or Try Later";
                } 
            }
        }    
            
    }
?>
<!DOCTYPE html>
<html lang="en">
<?php include("partials/head.php");?>
<body data-spy="scroll" data-target="#navSection" data-offset="100">
    
    <!--  BEGIN NAVBAR  -->
    <div class="header-container fixed-top">
        <?php include("partials/header.php");?>
    </div>
    <!--  END NAVBAR  -->

    <!--  BEGIN NAVBAR  -->
    <?php
        $kiosk_number = $_GET['kiosk_number'];
        $ret="SELECT * FROM  LAMCorp_waterPoints WHERE wp_number = ? "; 
        $stmt= $mysqli->prepare($ret) ;
        $stmt->bind_param('s', $kiosk_number);
        $stmt->execute() ;//ok
        $res=$stmt->get_result();
        $cnt=1;
        while($row=$res->fetch_object())
        {
    ?>
        <div class="sub-header-container">
            <header class="header navbar navbar-expand-sm">
                <a href="javascript:void(0);" class="sidebarCollapse" data-placement="bottom"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-menu"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg></a>
                <ul class="navbar-nav flex-row">
                    <li>
                        <div class="page-header">

                            <nav class="breadcrumb-one" aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="javascript:void(0);">Payments</a></li>
                                    <li class="breadcrumb-item active" aria-current="page"><span>Add  Payment</span></li>
                                </ol>
                            </nav>

                        </div>
                    </li>
                </ul>
            </header>
        </div>
        <!--  END NAVBAR  -->

        <!--  BEGIN MAIN CONTAINER  -->
        <div class="main-container" id="container">

            <div class="overlay"></div>
            <div class="search-overlay"></div>

            <!--  BEGIN SIDEBAR  -->
            <?php include("partials/sidebar.php");?>
            <!--  END SIDEBAR  -->

            <!--  BEGIN CONTENT AREA  -->
            <div id="content" class="main-content">
                <div class="container">
                    <div class="container">
                        <hr>
                        <div class="row">
                            <div id="flFormsGrid" class="col-lg-12 layout-spacing">
                                <div class="statbox widget box box-shadow">
                                    <div class="widget-header">
                                        <div class="row">
                                            <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                                <h4>Fill all fields</h4>
                                            </div>                                                                        
                                        </div>
                                    </div>
                                    <div class="widget-content widget-content-area">
                                        <form method="post" enctype="multipart/form-data" >
                                            <div class="form-row mb-4">
                                                <div class="form-group col-md-4">
                                                    <label for="inputPassword4">Payment Number</label>
                                                    <input required type="text"  readonly  value="LAMCorp-Payment-<?php echo $paynumber;?>" name="pay_number"  class="form-control">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="inputPassword4">Water Kiosk Number</label>
                                                    <input required type="text" readonly  value="<?php echo $row->wp_number;?>" name="kiosk_number"  class="form-control">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="inputPassword4">Water Litres Purchased</label>
                                                    <input required type="text" onChange="getPrice(this.value);"  name="litres_purchased"  class="form-control">
                                                </div>
                                                <hr>
                                                <div class="form-group col-md-6">
                                                    <label for="inputPassword4">Client Name</label>
                                                    <input required type="text"  name="client_name"  class="form-control">
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="inputPassword4">Client Phone Number</label>
                                                    <input required type="text"   name="client_phone"  class="form-control">
                                                </div>
                                                <hr>
                                                <div class="form-group col-md-6">
                                                    <label for="inputAddress">Till Number</label>
                                                    <select name="till_number" class="form-control  basic">
                                                        <option selected="selected">Select The Till Number Client Paid Using</option>
                                                        <?php
                                                            $ret="SELECT * FROM  LAMCorp_tills "; 
                                                            $stmt= $mysqli->prepare($ret) ;
                                                            $stmt->execute() ;//ok
                                                            $res=$stmt->get_result();
                                                            $cnt=1;
                                                            while($row=$res->fetch_object())
                                                            {
                                                        ?>
                                                            <option value="<?php echo $row->till_number;?>"><?php echo $row->till_number;?></option>
                                                        <?php }?>
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="inputPassword4">Transaction Code</label>
                                                    <input required type="text" name="transaction_code"  class="form-control">
                                                </div>
                                                <div class="form-group col-md-12">
                                                    <label for="inputPassword4">Amount</label>
                                                    <input required type="text"  readonly id="Amount"  name="amount"  class="form-control">
                                                </div>
                                            </div>
                                        <button type="submit" name="addPayment" class="btn btn-primary mt-3">Add Payment</button>
                                        </form>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>              
                    </div>
                </div>
                <?php include("partials/footer.php");?>
            </div>
            <!--  END CONTENT AREA  -->
        </div>
    <?php }?>
    <!-- END MAIN CONTAINER -->
    
    <!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
    <script src="assets/js/libs/jquery-3.1.1.min.js"></script>
    <script src="bootstrap/js/popper.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <script src="plugins/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="assets/js/app.js"></script>
    <script src="plugins/file-upload/file-upload-with-preview.min.js"></script>
    <script src="plugins/select2/select2.min.js"></script>
    <script src="plugins/select2/custom-select2.js"></script>
    
    <script>
        $(document).ready(function() {
            App.init();
        });

        var ss = $(".basic").select2({
        tags: true,
        });
    </script>
    <script src="plugins/highlight/highlight.pack.js"></script>
    <script src="assets/js/custom.js"></script>
    <!-- END GLOBAL MANDATORY SCRIPTS -->
    <script src="assets/js/scrollspyNav.js"></script>
</body>

</html>