<?php $this->load->view('admin/layout/_meta')?>
<title>新建网站角色</title>
</head>
<body>
<article class="page-container">
	<form action="<?php echo site_url('admin/SystemManage/editAction')?>" method="post" class="form form-horizontal" id="form-admin-role-edit">
        <?php if(isset($conf['id']) && !empty($conf['id'])):?>
        <input type="hidden" name="id" value="<?php echo $conf['id'];?>">
        <?php endif;?>

        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>字段名称：</label>
            <div class="formControls col-xs-4 col-sm-6">
                <input type="text" class="input-text" value="<?php echo $conf['column']?>" placeholder="字段名" id="roleName" name="column">
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3">字段值：</label>
            <div class="formControls col-xs-4 col-sm-6">
                <input type="text" class="input-text" value="<?php echo $conf['value']?>" placeholder="字段值" id="" name="value">
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3">字段描述：</label>
            <div class="formControls col-xs-4 col-sm-6">
                <input type="text" class="input-text" value="<?php echo $conf['desc']?>" placeholder="字段描述" id="" name="desc">
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
                        layer.msg('编辑成功!', {icon: 1,time:1500});
                        setTimeout(function () {
                            close();
                        },1500);
                    }else{
                        layer.msg('编辑失败!'+res.message, {icon: 5,time:2000});
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