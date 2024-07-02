<div class="card mb-3">
    <div class="card-header position-relative min-vh-25 mb-7">
        <div class="bg-holder rounded-3 rounded-bottom-0"
            style="background-image:url(<?=base_url()?>static/assets/img/generic/4.jpg);"></div>
        <!--/.bg-holder-->
        <div class="avatar avatar-5xl avatar-profile">
            <?php if(@session()->get('user_details')['user']['picture']){ ?>
            <img class="rounded-circle img-thumbnail shadow-sm"
                src="<?=session()->get('user_details')['user']['picture']?>" width="200" alt="">
            <?php }else{ ?>
            <span class="rounded-circle img-thumbnail fas fa-user"></span>
            <?php } ?>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-lg-6">
                <h5 class="text-400 fs-9 fw-normal">Welcome</h5>
                <?=@session()->get('user_details')['user']['name']?'<h4 class="text-primary mb-1">'.session()->get('user_details')['user']['name'].'</h4>':''?>
                <?=@session()->get('user_details')['user']['name']?'<h4 class="text-500 mb-1">'.session()->get('user_details')['user']['email'].'</h4>':''?>

                <div class="border-bottom border-dashed my-4 d-lg-none"></div>
                <?php #echo '<pre>'; print_r(session()->get('user_details')); die; ?>
            </div>
        </div>
    </div>
</div>
