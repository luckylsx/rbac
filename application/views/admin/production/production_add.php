<!--_meta 作为公共模版分离出去-->
<?php $this->load->view('admin/layout/_meta')?>
<!--/meta 作为公共模版分离出去-->
<link href="/public/admin/lib/webuploader/0.1.5/webuploader.css" rel="stylesheet" type="text/css" />
</head>
<body>
<nav class="breadcrumb"><a class="btn btn-success radius r" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a></nav>
<div class="page-container">
    <form action="<?php echo base_url('admin/ProductionController/addAction') ?>" method="post" class="form form-horizontal" id="form-production-add" onsubmit="return false;">
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>品牌标题：</label>
            <div class="formControls col-xs-8 col-sm-8">
                <input type="text" class="input-text" value="" placeholder="" id="brand_title" name="brand_title">
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>产品标题：</label>
            <div class="formControls col-xs-8 col-sm-8">
                <input type="text" class="input-text" value="" placeholder="" id="name" name="name">
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>产品货号：</label>
            <div class="formControls col-xs-8 col-sm-8">
                <input type="text" class="input-text" value="" placeholder="" id="Item_No" name="Item_No">
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>分类栏目：</label>
            <div class="formControls col-xs-4 col-sm-8">
                <span class="select-box">
				<select name="cate_id" class="select" id="cate_id">
                    <?php foreach ($cateList as $cate):?>
					<option value="<?php echo $cate['id'] ?>"><?php echo $cate['name'] ?></option>
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
                        <option value="<?php echo $type['id'] ?>"><?php echo $type['name'] ?></option>
                    <?php endforeach;?>
				</select>
				</span>
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>家居风格：</label>
            <?php foreach ($styles as $style):?>
            <label for="style_<?php echo $style['id'];?>"><?php echo $style['name'];?> &nbsp;<input type="checkbox" name="styles[]" value="<?php echo $style['id'];?>" id="style_1"></label> &nbsp;&nbsp;&nbsp;
            <?php endforeach;?>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2">允许评论：</label>
            <div class="formControls col-xs-8 col-sm-9 skin-minimal">
                <div class="check-box">
                    <input type="checkbox" id="checkbox-1" name="is_comment" value="1">
                    <label for="checkbox-1">&nbsp;</label>
                </div>
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2">价格：</label>
            <div class="formControls col-xs-4 col-sm-4">
                <input type="text" name="price" id="price" placeholder="产品价格" value="" class="input-text" style="width:50%">
                元</div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2">产品标签：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="text" name="label" id="label" placeholder="多个标签字用英文逗号隔开" value="" class="input-text">
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2">产品摘要：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <textarea name="abstract" id="abstract" cols="" rows="" class="textarea"  placeholder="说点什么...最少输入10个字符" datatype="*10-100" dragonfly="true" maxlength="200"></textarea>
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
</div>

<!--_footer 作为公共模版分离出去-->
<?php $this->load->view("admin/layout/_footer")?>
<!--/_footer 作为公共模版分离出去-->
<script type="text/javascript">
    $(function () {
        function close() {
            var index = parent.layer.getFrameIndex(window.name);
             parent.layer.close(index);
        }
        $("#product_submit").click(function () {
            $.ajax({
                type: "POST",   //提交的方法
                url:$("#form-production-add").attr("action"), //提交的地址
                data:$('#form-production-add').serialize(),// 序列化表单值
                async: false,
                success: function(res) {  //成功
                    data = JSON.parse(res);
                    if (data.errcode == 0){
                        var redirect = "<?php echo base_url('admin/ProductionController/index')  ?>";
                        layer.msg('添加成功!', {icon: 1,time:1000});
                        setTimeout(function () {
                            close();
                            window.location.href=redirect;
                        },1200);
                    }else{
                        layer.msg('添加失败！', {icon: 5,time:2000});
                    }
                }
            });
        });
    })
</script>
</body>
</html>