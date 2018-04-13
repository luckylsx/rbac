<?php $this->load->view('admin/layout/_meta')?>
<title>新建网站角色</title>
</head>
<body>
<article class="page-container">
	<form action="<?php echo site_url('admin/MenuManage/editAction')?>" method="post" class="form form-horizontal" id="form-admin-role-edit">
        <?php if(isset($menu['id']) && !empty($menu['id'])):?>
        <input type="hidden" name="id" value="<?php echo $menu['id'];?>">
        <?php endif;?>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>选择父级菜单：</label>
            <div class="formControls col-xs-4 col-sm-6">
                <select name="p_id" id="p_id" style="width:200px" class="input-text valid">
                    <option value="0">顶级菜单</option>
                    <?php foreach ($p_menu as $p_m):?>
                        <option value="<?php echo $p_m['id'];?>" <?php if ($menu['p_id']==$p_m['id']) echo 'selected';?>><?php echo $p_m['title'];?></option>
                    <?php endforeach;?>
                </select>
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3">菜单名：</label>
            <div class="formControls col-xs-4 col-sm-6">
                <input type="text" class="input-text" value="<?php echo $menu['title'] ?>" placeholder="菜单名" id="" name="title">
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3">菜单id值：</label>
            <div class="formControls col-xs-4 col-sm-6">
                <input type="text" class="input-text" value="<?php echo $menu['id_name'] ?>" placeholder="菜单id值" id="" name="id_name">
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3">图标：</label>
            <div class="formControls col-xs-4 col-sm-6">
                <input type="text" class="input-text" value="<?php echo htmlspecialchars($menu['menu_icon']);?>" placeholder="图标" id="" name="menu_icon">
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3">菜单url：</label>
            <div class="formControls col-xs-4 col-sm-6">
                <input type="text" class="input-text" value="<?php echo $menu['menu_url'] ?>" placeholder="菜单url 如：admin/admin/index" id="" name="menu_url">
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3">排序：</label>
            <div class="formControls col-xs-4 col-sm-6">
                <input type="number" class="input-text" value="<?php echo $menu['sort'] ?>" placeholder="排序" id="" name="sort">
            </div>
        </div>
		<div class="row cl">
			<div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-3">
				<button type="submit" class="btn btn-success radius" id="admin-role-save"><i class="icon-ok"></i> 确定</button>
			</div>
		</div>
	</form>
</article>

<!--_footer 作为公共模版分离出去-->
<?php $this->load->view("admin/layout/_footer")?>
<!--/_footer 作为公共模版分离出去-->

<!--请在下方写此页面业务相关的脚本-->
<script type="text/javascript" src="/public/admin/lib/jquery.validation/1.14.0/jquery.validate.js"></script>
<script type="text/javascript" src="/public/admin/lib/jquery.validation/1.14.0/validate-methods.js"></script>
<script type="text/javascript" src="/public/admin/lib/jquery.validation/1.14.0/messages_zh.js"></script>
<script type="text/javascript">
$(function(){
	$(".permission-list dt input:checkbox").click(function(){
		$(this).closest("dl").find("dd input:checkbox").prop("checked",$(this).prop("checked"));
	});
	$(".permission-list2 dd input:checkbox").click(function(){
		var l =$(this).parent().parent().find("input:checked").length;
		var l2=$(this).parents(".permission-list").find(".permission-list2 dd").find("input:checked").length;
		if($(this).prop("checked")){
			$(this).closest("dl").find("dt input:checkbox").prop("checked",true);
			$(this).parents(".permission-list").find("dt").first().find("input:checkbox").prop("checked",true);
		}
		else{
			if(l==0){
				$(this).closest("dl").find("dt input:checkbox").prop("checked",false);
			}
			if(l2==0){
				$(this).parents(".permission-list").find("dt").first().find("input:checkbox").prop("checked",false);
			}
		}
	});
	function close() {
        var index = parent.layer.getFrameIndex(window.name);
        parent.layer.close(index);
    }
    function refresh() {
        location.replace(location.href)
    }
	$("#form-admin-role-edit").validate({
		rules:{
			roleName:{
				required:true,
			},
		},
		onkeyup:false,
		focusCleanup:true,
		success:"valid",
		submitHandler:function(form){
			$(form).ajaxSubmit({
                success: function (res) {
                    res = JSON.parse(res);
                    if(res.errcode==0){
                        layer.msg('编辑成功!', {icon: 1,time:1000});
                        setTimeout(function () {
                            close();
                        },1500);
                    }else{
                        layer.msg('编辑失败!'+res.message, {icon: 5,time:1500});
                    }
                }
            });

		}
	});
});
</script>
<!--/请在上方写此页面业务相关的脚本-->
</body>
</html>