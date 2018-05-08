<style type="text/css">
    .dropDown_hover .dropDown_A a:hover{
        opacity: 1;
        background: black;
    }
</style>
<header class="navbar-wrapper">
	<div class="navbar navbar-fixed-top">
		<div class="container-fluid cl">
            <a class="logo navbar-logo f-l mr-10 hidden-xs" href="javascript:void(0)">目录册</a>
            <span class="logo navbar-slogan f-l mr-10 hidden-xs">后台</span>
            <a aria-hidden="false" class="nav-toggle Hui-iconfont visible-xs" href="javascript:;">&#xe667;</a>
			<nav id="Hui-userbar" class="nav navbar-nav navbar-userbar hidden-xs">
				<ul class="cl">
					<li class="">
                            <?php echo isset($user['username'])&&!empty($user['username']) ? $user['username']:"";?><i class="Hui-iconfont">&#xe6d5;</i>
						<ul class="dropDown-menu menu radius" style="border-radius: 5px">
							<li><a href="<?php  echo site_url('admin/login/logout') ?>">退出</a></li>
						</ul>
					</li>
				</ul>
			</nav>
		</div>
	</div>
</header>