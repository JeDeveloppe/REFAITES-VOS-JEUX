<?php
    if(isset($_SESSION['alertMessage'])){

    ?>
      <div class="jumbotron alertDiv bg-<?php echo $_SESSION['alertMessageConfig']; ?> alert-dismissible fade show" style="position: absolute; top: 90px; right: 10px; min-width:300px;" role="alert">
            <?php 
            echo $_SESSION['alertMessage'];
            if(isset($_SESSION['alertMessage-details'])){print_r($_SESSION['alertMessage-details']);}
            ?>
        </div>

    <?php
    unset($_SESSION['alertMessage']);
        if(isset($_SESSION['alertMessage-details'])){
            unset($_SESSION['alertMessage-details']);
        }
    unset($_SESSION['alertMessageConfig']);
    }
?>