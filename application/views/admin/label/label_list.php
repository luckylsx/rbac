<?php $this->load->view('admin/layout/_meta')?>
<title>标签管理</title>
<script>
    var confAdd_url = "<?php echo site_url('admin/LabelManage/labelAdd')?>";
</script>
</head>
<body>
<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 产品管理 <span class="c-gray en">&gt;</span> 标签管理 <a class="btn btn-success radius r" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a></nav>
<div class="page-container">
    <div class="text-c">
        <form action="" method="post">
            <select name="type" id="search" style="width:200px" class="input-text valid">
                <option value="1" <?php if ($type==1) echo "selected";?> >家居类型</option>
                <option value="2" <?php if ($type==2) echo "selected";?>>家居风格</option>
            </select>
            <button name="" id="" class="btn btn-success" type="submit"><i class="Hui-iconfont"></i> 搜索分类</button>
        </form>
    <div class="cl pd-5 bg-1 bk-gray"><span class="l">
            <!--<a href="javascript:;" onclick="datadel()" class="btn btn-danger radius"><i class="Hui-iconfont">&#xe6e2;</i> 批量删除</a>-->
            <a class="btn btn-primary radius" href="javascript:;" onclick="label_add('添加标签',confAdd_url,'780','350')"><i class="Hui-iconfont">&#xe600;</i> 添加标签</a> </span> </div>
    </div>
	<table class="table table-border table-bordered table-hover table-bg">
		<thead>
			<tr class="text-c">
				<!--<th width="25"><input type="checkbox" value="" name=""></th>-->
				<th width="100">分类名称</th>
                <th width="150">封面图</th>
				<th width="120">排序</th>
				<th width="150">创建时间</th>
				<th width="150">更新时间</th>
				<th width="120">操作</th>
			</tr>
		</thead>
		<tbody>
            <?php foreach ($labelList as $label):?>
			<tr class="text-c">
				<!--<td><input type="checkbox" value="" name=""></td>-->
				<td><?php echo $label['name'];?></td>
                <th width="100"><img src="<?php echo base_url($label['cover']);?>" width="50px"/></th>
                <td><?php echo $label['sort'];?></td>
                <td><?php echo $label['created_at'];?></td>
                <td><?php echo $label['updated_at'];?></td>
				<td class="f-14">
                    <a title="编辑" href="javascript:;" onclick="label_edit('配置编辑','edit?id='+<?php echo $label['id'];?>,<?php echo $label['id'];?>)" style="text-decoration:none"><i class="Hui-iconfont">&#xe6df;</i></a>
                    <a title="删除" href="javascript:;" onclick="label_del(this,'<?php echo $label['id'];?>')" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6e2;</i></a>
                </td>
			</tr>
            <?php endforeach;?>
		</tbody>
	</table>
    <?php echo $page_show;?>
</div>
<!--_footer 作为公共模版分离出去-->
<?php $this->load->view("admin/layout/_footer")?>
<!--/_footer 作为公共模版分离出去-->

<!--请在下方写此页面业务相关的脚本-->
<script type="text/javascript" src="/public/admin/lib/datatables/1.10.0/jquery.dataTables.min.js"></script>
<script type="text/javascript">
/*标签添加*/
function label_add(title,url,w,h){
	layer_show(title,url,w,h);
}
/*标签编辑*/
function label_edit(title,url,id,w,h){
    layer_show(title,url,780,350);
}
/*标签删除*/
function label_del(obj,id){
	layer.confirm('角色删除须谨慎，确认要删除吗？',function(roleId){
		$.ajax({
			type: 'POST',
			url: "<?php echo site_url('admin/LabelManage/delLabel');?>",
            data:{id:id},
            dataType:"JSON",
			success: function(data){
			    if(data.errcode==0){
                    $(obj).parents("tr").remove();
                    layer.msg('删除成功!',{icon:1,time:1000});
                }else{
                    layer.msg('删除失败!',{icon:5,time:1000});
                }

			},
		});
	});
}
$("#search").change(function () {
    var v = $("#search").val();
    var personal = "<?php echo $conconf['personality'] ?>";
    if (v==personal){
        $("#p_zhunti").attr("style","display:inline");
        $("#p_zhunti").attr("style","width:150px");
    }else{
        $("#p_zhunti").attr("style","display:none");
    }
})
</script>
</body>
</html>