﻿<?php $this->load->view('admin/layout/_meta')?>
<title>新建网站角色</title>
</head>
<body>
<article class="page-container">
	<form action="<?php echo site_url('admin/LabelManage/labelAddAction')?>" method="post" class="form form-horizontal" id="form-cate-add">
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>名称：</label>
            <div class="formControls col-xs-4 col-sm-6" id="label">
                <input type="text" class="input-text" value="" placeholder="分类名称" id="roleName" name="name">
            </div>
        </div>
        <div class="row cl">
			<label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>类型：</label>
			<div class="formControls col-xs-4 col-sm-6">
                <label>家居类型：<input type="radio" value="1" checked id="type_1" name="type"></label>&nbsp;&nbsp;&nbsp;&nbsp;
                <label>家居风格：<input type="radio" value="2" id="type_2" name="type"></label>
			</div>
		</div>
		<div class="row cl" id="cover">
			<label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>封面图：</label>
			<div class="formControls col-xs-4 col-sm-3" id="show_img">
				<input type="file" placeholder="封面图" id="upload" name="cover">
                <input type="hidden" name="cover" id="coValue">
			</div>
            <br>
            <p style="height:30px">
                <span id="upFile" style="display: block;background: #ccc;color: #000000;width: 50px;line-height: 30px;text-align: center;margin-left: 250px;cursor:pointer">上传</span>
            </p>
		</div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3">排序：</label>
            <div class="formControls col-xs-4 col-sm-6">
                <input type="number" class="input-text" value="" placeholder="排序" id="" name="sort">
            </div>
        </div>
		<div class="row cl">
			<div class="col-xs-8 col-sm-9 col-xs-offset-5 col-sm-offset-3">
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
$(function() {
    $(".permission-list dt input:checkbox").click(function () {
        $(this).closest("dl").find("dd input:checkbox").prop("checked", $(this).prop("checked"));
    });
    $(".permission-list2 dd input:checkbox").click(function () {
        var l = $(this).parent().parent().find("input:checked").length;
        var l2 = $(this).parents(".permission-list").find(".permission-list2 dd").find("input:checked").length;
        if ($(this).prop("checked")) {
            $(this).closest("dl").find("dt input:checkbox").prop("checked", true);
            $(this).parents(".permission-list").find("dt").first().find("input:checkbox").prop("checked", true);
        }
        else {
            if (l == 0) {
                $(this).closest("dl").find("dt input:checkbox").prop("checked", false);
            }
            if (l2 == 0) {
                $(this).parents(".permission-list").find("dt").first().find("input:checkbox").prop("checked", false);
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
	$("#form-cate-add").validate({
		onkeyup:false,
		focusCleanup:true,
		success:"valid",
		submitHandler:function(form){
			$(form).ajaxSubmit({
                success: function (res) {
                    res = JSON.parse(res);
                    if(res.errcode==0){
                        setTimeout(function () {
                            close();
                        },"1500");
                        layer.msg('添加成功!', {icon: 1,time:1000});
                    }else{
                        layer.msg('添加失败！'+res.message, {icon: 5,time:2000});
                    }
                }
            });

		}
	});
});
$("#upFile").click(function () {
    if (!$("#upload").val()){
        alert("请选择上传文件");
        return false;
    }
    var formData = new FormData();
    formData.append("cover", $("#upload")[0].files[0]);
    console.log($("#upload")[0].files[0]);
    $.ajax({
        url: "<?php echo site_url('admin/LabelManage/upload') ?>",
        type: "POST",
        data: formData,
        // 告诉jQuery不要去处理发送的数据
        processData: false,
        // 告诉jQuery不要去设置Content-Type请求头
        contentType: false,
        success: function (res) {
            var data = JSON.parse(res);
            console.log(data.errcode);
            if (data.errcode === 0) {
                $("#coValue").val(data.data);
            } else {
                layer.msg('上传失败！', {icon: 5,time:2000});
            }
        }
    })
});
</script>
<!--/请在上方写此页面业务相关的脚本-->
</body>
</html>