<?php $this->load->view('admin/layout/_meta')?>
<title>编辑</title>
</head>
<body>
<article class="page-container">
	<form action="<?php echo site_url('admin/ProductionController/editAction')?>" method="post" class="form form-horizontal" id="form-production-edit">
        <?php if(isset($detail['id']) && !empty($detail['id'])):?>
        <input type="hidden" name="id" value="<?php echo $detail['id'];?>">
        <?php endif;?>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>品牌标题：</label>
            <div class="formControls col-xs-8 col-sm-8">
                <input type="text" class="input-text" value="<?php echo $detail['brand_title'];?>" placeholder="品牌标题" id="brand_title" name="brand_title">
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>产品标题：</label>
            <div class="formControls col-xs-8 col-sm-8">
                <input type="text" class="input-text" value="<?php echo $detail['brand_title'];?>" placeholder="产品标题" id="name" name="name">
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>产品货号：</label>
            <div class="formControls col-xs-8 col-sm-8">
                <input type="text" class="input-text" value="<?php echo $detail['Item_No'];?>" placeholder="产品货号" id="Item_No" name="Item_No">
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>分类栏目：</label>
            <div class="formControls col-xs-4 col-sm-8">
                <span class="select-box">
				<select name="cate_id" class="select" id="cate_id">
                    <?php foreach ($cateList as $cate):?>
                        <option value="<?php echo $cate['id'] ?>" <?php if ($cate['id']==$detail['cate_id']) echo "selected";?>><?php echo $cate['name'] ?></option>
                    <?php endforeach;?>
				</select>
				</span>
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>家居类型：</label>
            <div class="formControls col-xs-4 col-sm-8">
                <span class="select-box">
				<select name="type" class="select">
                    <?php foreach ($types as $type):?>
                        <option value="<?php echo $type['id'] ?>" <?php if ($type['id']==$detail['type_id']) echo "selected";?>>
                            <?php echo $type['name'];?>
                        </option>
                    <?php endforeach;?>
				</select>
				</span>
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>家居风格：</label>
            <?php foreach ($styles as $style):?>
                <label for="style_<?php echo $style['id'];?>">
                    <?php echo $style['name'];?> &nbsp;
                    <input type="checkbox" name="styles[]" value="<?php echo $style['id'];?>" id="style_1" <?php if (in_array($style['id'],json_decode($detail['styles_id'],true))) echo "checked" ?>>
                </label> &nbsp;&nbsp;&nbsp;
            <?php endforeach;?>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2">允许评论：</label>
            <div class="formControls col-xs-8 col-sm-9 skin-minimal">
                <div class="check-box">
                    <input type="checkbox" id="checkbox-1" name="is_comment" value="1" <?php if ($detail['is_comment']==1) echo "checked";?>>
                    <label for="checkbox-1">&nbsp;</label>
                </div>
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2">价格：</label>
            <div class="formControls col-xs-4 col-sm-4">
                <input type="text" name="price" id="price" placeholder="产品价格" value="<?php echo $detail['price'];?>" class="input-text" style="width:50%">
                元</div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2">产品标签：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="text" name="label" id="label" placeholder="多个标签字用英文逗号隔开" value="<?php echo $detail['label'];?>" class="input-text">
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2">产品摘要：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <textarea name="abstract" id="abstract" cols="" rows="" class="textarea"  placeholder="说点什么...最少输入10个字符" datatype="*10-100" dragonfly="true" maxlength="200"><?php echo $detail['abstract'];?></textarea>
                <p class="textarea-numberbar"><em class="textarea-length">0</em>/<span id="textarea_size">200</span></p>
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2">封面图：</label>
            <div class="formControls col-xs-4 col-sm-4">
                <div class="uploader-thum-container">
                    <!--用来存放文件信息-->
                    <div id="thelist" class="uploader-list"></div>
                    <div class="btns" style="display: inline">
                        <input type="file" id="upload" name="cover" style="width: 65px">
                        <input type="hidden" name="cover" id="coValue">
                        <span style="display: inline-block">上传</span>
                    </div>

                </div>
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2">图片上传：</label>
            <div class="formControls col-xs-4 col-sm-4">
                <div class="btns">
                    <input type="file" placeholder="封面图" id="upload" name="cover" style="width: 65px">
                    <input type="hidden" name="image_list" id="coValue">
                    <span>上传</span>
                </div>
            </div>
        </div>
        <div class="row cl">
            <div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-2">
                <button id="product_submit" class="btn btn-primary radius" type="submit"><i class="Hui-iconfont">&#xe632;</i> 保存</button>
                <button onClick="layer_close();" class="btn btn-default radius" type="button">&nbsp;&nbsp;取消&nbsp;&nbsp;</button>
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
	$("#form-production-edit").validate({
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