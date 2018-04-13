<?php $this->load->view('admin/layout/_meta')?>
<title>角色管理</title>
<script>
    var confAdd_url = "<?php echo site_url('admin/SystemManage/showSystemAdd')?>";
</script>
</head>
<body>
<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 系統管理 <span class="c-gray en">&gt;</span> 角色管理 <a class="btn btn-success radius r" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a></nav>
<div class="page-container">
	<div class="cl pd-5 bg-1 bk-gray"><span class="l">
            <!--<a href="javascript:;" onclick="datadel()" class="btn btn-danger radius"><i class="Hui-iconfont">&#xe6e2;</i> 批量删除</a>-->
            <a class="btn btn-primary radius" href="javascript:;" onclick="system_add('添加配置字段',confAdd_url,'780','350')"><i class="Hui-iconfont">&#xe600;</i> 添加配置字段</a> </span> <span class="r">共有数据：<strong><?php echo count($conf_list);?></strong> 条</span> </div>
	<table class="table table-border table-bordered table-hover table-bg">
		<thead>
			<tr>
				<th scope="col" colspan="6">角色管理</th>
			</tr>
			<tr class="text-c">
				<!--<th width="25"><input type="checkbox" value="" name=""></th>-->
				<th width="100">配置字段</th>
				<th width="100">配置值</th>
				<th width="120">配置描述</th>
				<th width="120">创建时间</th>
				<th width="120">更新时间</th>
				<th width="120">操作</th>
			</tr>
		</thead>
		<tbody>
            <?php foreach ($conf_list as $conf):?>
			<tr class="text-c">
				<!--<td><input type="checkbox" value="" name=""></td>-->
				<td><?php echo $conf['column'];?></td>
                <td><?php echo $conf['value'];?></td>
                <td><?php echo $conf['desc'];?></td>
                <td><?php echo $conf['created_at'];?></td>
                <td><?php echo $conf['updated_at'];?></td>
				<td class="f-14">
                    <a title="编辑" href="javascript:;" onclick="system_edit('配置编辑','systemEdit?id='+<?php echo $conf['id'];?>,<?php echo $conf['id'];?>)" style="text-decoration:none"><i class="Hui-iconfont">&#xe6df;</i></a>
                    <a title="删除" href="javascript:;" onclick="system_del(this,'<?php echo $conf['id'];?>')" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6e2;</i></a>
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
/*管理员-角色-添加*/
function system_add(title,url,w,h){
	layer_show(title,url,w,h);
}
/*管理员-角色-编辑*/
function system_edit(title,url,id,w,h){
    layer_show(title,url,780,350);
}
/*管理员-角色-删除*/
function system_del(obj,id){
	layer.confirm('角色删除须谨慎，确认要删除吗？',function(roleId){
		$.ajax({
			type: 'POST',
			url: "<?php echo site_url('admin/SystemManage/delSystemConf');?>",
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
</script>
</body>
</html>