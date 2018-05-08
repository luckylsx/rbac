<?php $this->load->view('admin/layout/_meta')?>
<title>分类管理</title>
<script>
    var production_url = "<?php echo site_url('admin/ProductionController/productionAdd')?>";
</script>
</head>
<body>
<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 产品管理 <span class="c-gray en">&gt;</span> 分类管理 <a class="btn btn-success radius r" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a></nav>
<div class="page-container">
    <form action="<?php echo base_url('admin/ProductionController/index');?>" method="GET">
        <div class="text-c"> 日期范围：
            <input type="text" onfocus="WdatePicker({ maxDate:'#F{$dp.$D(\'logmax\')||\'%y-%M-%d\'}' })" id="logmin" class="input-text Wdate" style="width:120px;" placeholder="发布日期" name="start_date" value="<?php echo isset($search['start_date'])&&!empty($search['start_date'])?$search['start_date']:''; ?>">
            -
            <input type="text" onfocus="WdatePicker({ minDate:'#F{$dp.$D(\'logmin\')}',maxDate:'%y-%M-%d' })" id="logmax" class="input-text Wdate" style="width:120px;" placeholder="发布日期" name="end_date" value="<?php echo isset($search['end_date'])&&!empty($search['end_date'])?$search['end_date']:''; ?>">
            <input type="text" name="degist" id="digest" placeholder=" 产品名称/摘要/分类名称/标签名称" style="width:240px" class="input-text" value="<?php echo isset($search['end_date'])&&!empty($search['degist'])?$search['degist']:''; ?>">
            <input type="text" name="pro_num" id="pro_num" placeholder="产品货号" style="width:150px" class="input-text" value="<?php echo isset($search['end_date'])&&!empty($search['pro_num'])?$search['pro_num']:''; ?>">
            <button name="" id="" class="btn btn-success" type="submit"><i class="Hui-iconfont">&#xe665;</i> 搜产品</button>
        </div>
    </form>
    <div class="cl pd-5 bg-1 bk-gray mt-20">
        <span class="l">
            <a href="javascript:;" onclick="proBatch_del()" class="btn btn-danger radius"><i class="Hui-iconfont">&#xe6e2;</i> 批量删除</a>
            <a class="btn btn-primary radius" onclick="product_add('添加产品',production_url)" href="javascript:;">
                <i class="Hui-iconfont">&#xe600;</i> 添加产品</a>
        </span>
        <span class="r">共有数据：<strong><?php echo $total; ?></strong> 条</span>
    </div>
    <div class="mt-20">
        <table class="table table-border table-bordered table-bg table-hover table-sort">
            <thead>
            <tr class="text-c">
                <th width="40"><input name="" type="checkbox" value=""></th>
                <th width="150">产品货号</th>
                <th width="60">封面图</th>
                <th width="120">产品名称</th>
                <th>摘要</th>
                <th width="100">单价</th>
                <th width="100">发布状态</th>
                <th width="150">操作</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($proList as $production): ?>
            <tr class="text-c va-m">
                <td><input name="prov[]" class="prov" type="checkbox" value="<?php echo $production['id'] ?>"></td>
                <td ><?php echo $production['Item_No'] ?></td>
                <td><img width="60" class="product-thumb" src="<?php echo $production['image_cover'];?>"></td>
                <td class="text-l"><?php echo $production['name'] ?></td>
                <td class="text-l"><?php echo $production['abstract'] ?></td>
                <td><span class="price"><?php echo $production['price'] ?></td>
                <td class="td-status">
                    <?php if ($production['on_sale']==1): ?>
                        <span class="label label-success radius">已发布</span>
                    <?php else:?>
                        <span class="label label-defaunt radius">已下架</span>
                    <?php endif; ?>
                </td>
                <td class="td-manage">
                    <?php if ($production['on_sale']==1): ?>
                    <a style="text-decoration:none" onClick="product_stop(this,<?php echo $production['id'];?>)" href="javascript:;" title="下架">
                        <i class="Hui-iconfont">&#xe6de;</i>
                    </a>
                    <?php elseif ($production['on_sale']==2): ?>
                    <a style="text-decoration:none" onClick="product_start(this,<?php echo $production['id'];?>)" href="javascript:;" title="发布">
                        <i class="Hui-iconfont">&#xe603;</i>
                    </a>
                    <?php endif; ?>
                    <a style="text-decoration:none" class="ml-5" onClick="product_edit('产品编辑','edit?id='+<?php echo $production['id'];?>,'10001')" href="javascript:;" title="编辑">
                        <i class="Hui-iconfont">&#xe6df;</i>
                    </a>
                    <a style="text-decoration:none" class="ml-5" onClick="product_del(this,<?php echo $production['id'];?>)" href="javascript:;" title="删除">
                        <i class="Hui-iconfont">&#xe6e2;</i>
                    </a>
                </td>
            </tr>
            <?php endforeach;?>
            </tbody>
        </table>
        <?php echo $page_show; ?>
    </div>
</div>
<!--_footer 作为公共模版分离出去-->
<?php $this->load->view("admin/layout/_footer")?>
<!--/_footer 作为公共模版分离出去-->

<!--请在下方写此页面业务相关的脚本-->
<script type="text/javascript" src="/public/admin/lib/datatables/1.10.0/jquery.dataTables.min.js"></script>
<!--请在下方写此页面业务相关的脚本-->
<script type="text/javascript" src="/public/admin/lib/My97DatePicker/4.8/WdatePicker.js"></script>
<script type="text/javascript" src="/public/admin/lib/laypage/1.2/laypage.js"></script>
<script type="text/javascript">
    /*产品-添加*/
    function product_add(title,url){
        var index = layer.open({
            type: 2,
            title: title,
            content: url,
        });
        layer.full(index);
        //layer_show(title,url,909,900);
    }
    /*产品-查看*/
    function product_show(title,url,id){
        var index = layer.open({
            type: 2,
            title: title,
            content: url
        });
        layer.full(index);
    }
/*产品-编辑*/
function product_edit(title,url,id){
    var index = layer.open({
        type: 2,
        title: title,
        content: url
    });
    layer.full(index);
}

    /*产品-下架*/
    function product_stop(obj,id){
        layer.confirm('确认要下架吗？',function(index){
            $.ajax({
                type: 'POST',
                url: "<?php echo base_url('admin/ProductionController/productStop')?>",
                data:{production_id:id},
                dataType:"JSON",
                success: function(data){
                    if(data.errcode==0){
                        $(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" onClick="product_start(this,'+id+')" href="javascript:;" title="发布"><i class="Hui-iconfont">&#xe603;</i></a>');
                        $(obj).parents("tr").find(".td-status").html('<span class="label label-defaunt radius">已下架</span>');
                        $(obj).remove();
                        layer.msg('已下架!',{icon: 5,time:1000})
                    }else{
                        layer.msg('删除失败!',{icon:5,time:1000});
                    }
                }
            });
        });
    }
    /*产品-发布*/
    function product_start(obj,id){
        layer.confirm('确认要发布吗？',function(index){
            $.ajax({
                type: 'POST',
                url: "<?php echo base_url('admin/ProductionController/productStart')?>",
                data:{production_id:id},
                dataType:"JSON",
                success: function(data){
                    if(data.errcode==0){
                        $(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" onClick="product_stop(this,'+id+')" href="javascript:;" title="下架"><i class="Hui-iconfont">&#xe6de;</i></a>');
                        $(obj).parents("tr").find(".td-status").html('<span class="label label-success radius">已发布</span>');
                        $(obj).remove();
                        layer.msg('已发布!',{icon: 6,time:1000});
                    }else{
                        layer.msg('删除失败!',{icon:5,time:1000});
                    }
                }
            });
        });
    }
    /*产品-删除*/
    function product_del(obj,id){
        layer.confirm('确认要删除吗？',function(index){
            $.ajax({
                type: 'POST',
                url: "<?php echo base_url('admin/ProductionController/productDel')?>",
                data: {production_id:id},
                dataType: 'json',
                success: function(data){
                    if (data.errcode==0){
                        $(obj).parents("tr").remove();
                        layer.msg('已删除!',{icon:1,time:1000});
                    }else{
                        layer.msg('删除失败!',{icon:5,time:2000});
                    }
                }
            });
        });
    }
    /*产品-批量删除*/
    function proBatch_del() {
        var proIds = [];
        $.each($("input[type='checkbox']:checked"),function () {
            proIds.push($(this).val());
        });
        var len = $("input[type='checkbox']:checked").length;
        layer.confirm("确定要删除选中的"+len+"个吗?",function () {
            $.post(
                "<?php echo base_url('admin/ProductionController/productDelBatch')?>",
                {"proIds":proIds},
                function (res) {
                    data = JSON.parse(res);
                    if (data.errcode == 0){
                        $("input[type='checkbox']:checked").parents("tr").remove();
                        layer.msg('删除成功!', {icon: 1,time:1000});
                    }else{
                        layer.msg('删除失败！', {icon: 5,time:2000});
                    }
                });
        })
    }
</script>
</body>
</html>